<?php

declare(strict_types=1);

namespace Drupal\geo_starter_mcp;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;

/**
 * Introspects the GEO Starter content model from the RUNNING site.
 *
 * This is the substance behind the `geo.describe_content_model` and
 * `geo.validate_node` MCP tools: it reads live field definitions (not a static
 * file), so an agent always sees the model the site actually has. That is the
 * point of recommendation #2 in Buytaert's "Do AI coding agents recommend
 * Drupal?" (2026) — defeat stale training data with live introspection.
 *
 * This class uses only stable core entity-field APIs and is independent of the
 * MCP transport, so it is unit/kernel testable without mcp_server.
 */
final class ContentModel {

  /**
   * The node bundles GEO Starter governs.
   */
  public const BUNDLES = ['service', 'answer', 'article', 'evidence_source'];

  /**
   * The schema.org type hints per bundle (mirrors content-model.schema.json).
   */
  private const SCHEMA_ORG = [
    'service' => ['Service', 'WebPage', 'BreadcrumbList'],
    'answer' => ['Question', 'Answer', 'WebPage'],
    'article' => ['Article', 'WebPage', 'BreadcrumbList'],
    'evidence_source' => ['CreativeWork'],
  ];

  public function __construct(
    private readonly EntityFieldManagerInterface $entityFieldManager,
    private readonly EntityTypeBundleInfoInterface $bundleInfo,
  ) {}

  /**
   * Describes the live content model as a JSON-serialisable array.
   *
   * @return array
   *   { version, entityType, contentTypes: { bundle: { jsonapiType,
   *   schemaOrg, label, fields: [ { machineName, label, drupalType,
   *   cardinality, required, reference? } ] } } }
   */
  public function describe(): array {
    $bundleLabels = $this->bundleInfo->getBundleInfo('node');
    $types = [];
    foreach (self::BUNDLES as $bundle) {
      $fields = [];
      foreach ($this->entityFieldManager->getFieldDefinitions('node', $bundle) as $name => $def) {
        // Expose the title base field plus configurable (field_*) fields; skip
        // internal/computed base fields an agent should not author.
        $isConfigurable = method_exists($def, 'isBaseField') ? !$def->isBaseField() : str_starts_with($name, 'field_');
        if ($name !== 'title' && !$isConfigurable) {
          continue;
        }
        $cardinality = $def->getFieldStorageDefinition()->getCardinality();
        $entry = [
          'machineName' => $name,
          'label' => (string) $def->getLabel(),
          'drupalType' => $def->getType(),
          'cardinality' => $cardinality === -1 ? 'unlimited' : $cardinality,
          'required' => $def->isRequired(),
        ];
        $settings = $def->getSettings();
        if (!empty($settings['handler_settings']['target_bundles'])) {
          $entry['reference'] = [
            'targetType' => $settings['target_type'] ?? 'node',
            'targetBundles' => array_values($settings['handler_settings']['target_bundles']),
          ];
        }
        if (!empty($settings['allowed_values'])) {
          $entry['allowedValues'] = array_keys($settings['allowed_values']);
        }
        $fields[] = $entry;
      }
      $types[$bundle] = [
        'jsonapiType' => "node--$bundle",
        'schemaOrg' => self::SCHEMA_ORG[$bundle] ?? [],
        'label' => (string) ($bundleLabels[$bundle]['label'] ?? $bundle),
        'fields' => $fields,
      ];
    }
    return [
      'version' => '1.0.0',
      'entityType' => 'node',
      'contentTypes' => $types,
    ];
  }

  /**
   * Validates a logical-node payload against the live model.
   *
   * @param string $bundle
   *   One of self::BUNDLES.
   * @param array $values
   *   Flat field map (machineName => value).
   *
   * @return string[]
   *   A list of human-readable problems. Empty array means valid.
   */
  public function validate(string $bundle, array $values): array {
    $problems = [];
    if (!in_array($bundle, self::BUNDLES, TRUE)) {
      return [sprintf('Unknown content type "%s". Expected one of: %s.', $bundle, implode(', ', self::BUNDLES))];
    }
    $defs = $this->entityFieldManager->getFieldDefinitions('node', $bundle);
    foreach ($defs as $name => $def) {
      $isConfigurable = method_exists($def, 'isBaseField') ? !$def->isBaseField() : str_starts_with($name, 'field_');
      if ($name !== 'title' && !$isConfigurable) {
        continue;
      }
      if ($def->isRequired() && (!array_key_exists($name, $values) || $values[$name] === '' || $values[$name] === NULL || $values[$name] === [])) {
        $problems[] = sprintf('Missing required field "%s" (%s).', $name, $def->getLabel());
      }
    }
    // Reject unknown keys so typos surface instead of silently dropping.
    foreach (array_keys($values) as $key) {
      if (!isset($defs[$key])) {
        $problems[] = sprintf('Unknown field "%s" for content type "%s".', $key, $bundle);
      }
    }
    return $problems;
  }

}
