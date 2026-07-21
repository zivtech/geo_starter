<?php

declare(strict_types=1);

/**
 * @file
 * Generates docs/api/content-model.schema.json from the recipe's config/.
 *
 * The machine-readable content model (recommendation #3 in Buytaert's "Do AI
 * coding agents recommend Drupal?", 2026) must never drift from the actual
 * config. This dev-only generator reads node.type.*, field.field.node.*,
 * field.storage.node.* and the editorial workflow, and emits the same
 * structure shipped in docs/api/content-model.schema.json.
 *
 * Usage:
 *   php tools/generate-content-model-schema.php          # write the file
 *   php tools/generate-content-model-schema.php --check  # CI drift guard:
 *                                                        # exit 1 if stale
 *
 * Requires Symfony YAML (ships with any Composer/Drupal project). Run from the
 * recipe root, or from a project that has this recipe as a path repo.
 *
 * NOTE: this generator is NOT run on a fresh site install — it is a maintainer
 * tool, like the other scripts in tools/. It does not touch a database.
 */

// --- locate a Composer autoloader (for Symfony\Component\Yaml\Yaml) ---
$autoload = null;
foreach ([
  __DIR__ . '/../vendor/autoload.php',
  __DIR__ . '/../../vendor/autoload.php',
  __DIR__ . '/../../../vendor/autoload.php',
  __DIR__ . '/../../../../autoload.php',
] as $candidate) {
  if (is_file($candidate)) { $autoload = $candidate; break; }
}
if ($autoload !== null) {
  require $autoload;
}
if (!class_exists(\Symfony\Component\Yaml\Yaml::class)) {
  fwrite(STDERR, "Symfony YAML not found. Run from a Composer project that has it,\n");
  fwrite(STDERR, "e.g. inside a drupal/cms project with this recipe as a path repo.\n");
  exit(2);
}

use Symfony\Component\Yaml\Yaml;

$configDir = realpath(__DIR__ . '/../config');
$outFile = __DIR__ . '/../docs/api/content-model.schema.json';
$check = in_array('--check', $argv, true);
// 1.1.0 is the additive content-model contract first shipped by recipe 1.2.0.
// Recipe and model versions are intentionally independent.
$schemaVersion = '1.1.0';
$schemaId = 'https://git.drupalcode.org/project/geo_starter/-/raw/1.2.0/docs/api/content-model.schema.json';
$schemaDescription = 'Versioned, machine-readable description of the GEO Starter content model: node types, fields, reference targets, the editorial workflow, and schema.org mappings. Agents fetch this to resolve stale training data (Drupal training data skews to D7) and to know the exact field machine names before reading via JSON:API or preparing a local draft artifact. Generated from config/ by tools/generate-content-model-schema.php — do not hand-edit; regenerate on content-model changes.';

if ($configDir === false) {
  fwrite(STDERR, "Could not locate config/ relative to tools/.\n");
  exit(2);
}

/** Load a single config YAML file as an array. */
$load = static function (string $name) use ($configDir): ?array {
  $path = "$configDir/$name.yml";
  return is_file($path) ? (Yaml::parseFile($path) ?: []) : null;
};

// --- field storage: type + cardinality, keyed by field machine name ---
$storage = [];
foreach (glob("$configDir/field.storage.node.*.yml") as $file) {
  $cfg = Yaml::parseFile($file) ?: [];
  $name = $cfg['field_name'] ?? null;
  if ($name === null) { continue; }
  $storage[$name] = [
    'type' => $cfg['type'] ?? 'string',
    'cardinality' => (int) ($cfg['cardinality'] ?? 1),
    'allowed_values' => array_column($cfg['settings']['allowed_values'] ?? [], 'value'),
  ];
}

// --- per-bundle field instances (label, required, reference target) ---
// Bundles are derived from the shipped config, never hardcoded: a bundle
// added to config/ without a schema.org mapping below must fail loudly here
// (including under --check), not silently stay absent from the schema.
$bundles = array_map(
  static fn (string $f): string => substr(basename($f, '.yml'), strlen('node.type.')),
  glob("$configDir/node.type.*.yml") ?: []
);
sort($bundles);
$schemaOrg = [
  // Keep these mappings aligned with the companion module's canonical-page
  // graph. BreadcrumbList is deliberately not emitted; each supported node
  // bundle does emit its own WebPage graph object.
  'service' => ['Service', 'WebPage'],
  'answer' => ['Question', 'Answer', 'WebPage'],
  'article' => ['Article', 'WebPage'],
  'evidence_source' => ['CreativeWork', 'WebPage'],
];
$unmapped = array_diff($bundles, array_keys($schemaOrg));
if ($unmapped !== []) {
  fwrite(STDERR, sprintf(
    "UNMAPPED BUNDLE(S): %s — config/ ships node.type.*.yml for them but\n"
    . "\$schemaOrg in tools/%s has no entry. Add the mapping and regenerate\n"
    . "before shipping the new type.\n",
    implode(', ', $unmapped),
    basename(__FILE__),
  ));
  exit(2);
}

// Load the existing document up front: hand-authored prose (the metadata
// blocks and the per-type descriptions) is preserved from it when present —
// the generator owns only the machine facts derived from config/.
$existing = is_file($outFile) ? json_decode((string) file_get_contents($outFile), true) : [];
if (!is_array($existing)) {
  $existing = [];
}

$contentTypes = [];
foreach ($bundles as $bundle) {
  $type = $load("node.type.$bundle");
  $fields = [
    [
      'machineName' => 'title', 'label' => 'Title', 'drupalType' => 'string',
      'cardinality' => 1, 'required' => true, 'base' => true,
    ],
  ];
  $fieldFiles = glob("$configDir/field.field.node.$bundle.*.yml") ?: [];
  sort($fieldFiles);
  foreach ($fieldFiles as $file) {
    $cfg = Yaml::parseFile($file) ?: [];
    $fieldName = $cfg['field_name'] ?? null;
    if ($fieldName === null) { continue; }
    $st = $storage[$fieldName] ?? ['type' => 'string', 'cardinality' => 1, 'allowed_values' => []];
    $entry = [
      'machineName' => $fieldName,
      'label' => $cfg['label'] ?? $fieldName,
      'drupalType' => $st['type'],
      'cardinality' => $st['cardinality'] === -1 ? 'unlimited' : $st['cardinality'],
      'required' => (bool) ($cfg['required'] ?? false),
    ];
    if (!empty($st['allowed_values'])) {
      $entry['allowedValues'] = $st['allowed_values'];
    }
    $settings = $cfg['settings'] ?? [];
    if (isset($settings['handler_settings']['target_bundles'])) {
      $targetType = str_starts_with((string) ($cfg['settings']['handler'] ?? ''), 'default:')
        ? substr($cfg['settings']['handler'], 8)
        : ($settings['target_type'] ?? 'node');
      $entry['reference'] = [
        'targetType' => $targetType,
        'targetBundles' => array_values($settings['handler_settings']['target_bundles']),
      ];
    }
    $fields[] = $entry;
  }
  $handAuthored = $existing['contentTypes'][$bundle]['description'] ?? '';
  $contentTypes[$bundle] = [
    'jsonapiType' => "node--$bundle",
    'schemaOrg' => $schemaOrg[$bundle],
    'description' => is_string($handAuthored) && $handAuthored !== ''
      ? $handAuthored
      : ($type['description'] ?? ''),
    'fields' => $fields,
  ];
}

/**
 * Converts one exported Drupal field definition into its logical-payload
 * schema. The payload format is documented below: it is deliberately not the
 * JSON:API wire format, but it must include every field config exposes.
 */
$fieldPayloadSchema = static function (array $field): array {
  $type = $field['drupalType'];
  $itemSchema = match ($type) {
    'string' => array_filter([
      'type' => 'string',
      'minLength' => $field['required'] ? 1 : null,
    ], static fn (mixed $value): bool => $value !== null),
    'text_long' => ['$ref' => '#/$defs/textLong'],
    'text_with_summary' => ['$ref' => '#/$defs/textWithSummary'],
    'datetime' => [
      'type' => 'string',
      'format' => 'date',
      // `format` is annotation-only in some JSON Schema implementations.
      'pattern' => '^\\d{4}-\\d{2}-\\d{2}$',
    ],
    'link' => ['$ref' => '#/$defs/link'],
    'list_string' => array_filter([
      'type' => 'string',
      'enum' => $field['allowedValues'] ?? [],
    ], static fn (mixed $value): bool => $value !== []),
    'entity_reference', 'entity_reference_revisions' => [
      'type' => 'string',
      'format' => 'uuid',
      'pattern' => '^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$',
    ],
    default => throw new \RuntimeException(sprintf(
      'Unsupported field storage type "%s" for %s. Add an explicit logical-payload mapping before generating.',
      $type,
      $field['machineName'],
    )),
  };

  $cardinality = $field['cardinality'];
  if ($type === 'entity_reference' || $type === 'entity_reference_revisions' || $cardinality !== 1) {
    $schema = ['type' => 'array', 'items' => $itemSchema];
    if ($field['required']) {
      $schema['minItems'] = 1;
    }
    if ($cardinality !== 'unlimited') {
      $schema['maxItems'] = $cardinality;
    }
    return $schema;
  }

  if ($field['required'] && in_array($type, ['text_long', 'text_with_summary'], true)) {
    return [
      'allOf' => [
        $itemSchema,
        ['properties' => ['value' => ['minLength' => 1]]],
      ],
    ];
  }

  return $itemSchema;
};

// Helpers and the per-type payload definitions are generated alongside the
// field inventory. This makes an additive config field appear in the strict
// write-preflight contract in the same run, rather than relying on a second
// hand-maintained list that can silently omit it.
$payloadDefs = [
  'textLong' => [
    'type' => 'object',
    'required' => ['value'],
    'properties' => [
      'value' => ['type' => 'string'],
      'format' => ['type' => 'string'],
    ],
    'additionalProperties' => false,
  ],
  'textWithSummary' => [
    'type' => 'object',
    'required' => ['value'],
    'properties' => [
      'value' => ['type' => 'string'],
      'summary' => ['type' => 'string'],
      'format' => ['type' => 'string'],
    ],
    'additionalProperties' => false,
  ],
  'link' => [
    'type' => 'object',
    'required' => ['uri'],
    'properties' => [
      'uri' => [
        'type' => 'string',
        'minLength' => 1,
        'format' => 'uri-reference',
        'pattern' => '^[a-z][a-z0-9+.-]*:[^\\s]+$',
      ],
      'title' => ['type' => 'string'],
    ],
    'additionalProperties' => false,
  ],
];
foreach ($contentTypes as $bundle => $contentType) {
  $properties = [];
  $required = [];
  foreach ($contentType['fields'] as $field) {
    $properties[$field['machineName']] = $fieldPayloadSchema($field);
    if ($field['required']) {
      $required[] = $field['machineName'];
    }
  }
  $payloadDefs[$bundle] = [
    'type' => 'object',
    'required' => $required,
    'properties' => $properties,
    'additionalProperties' => false,
  ];
}

// --- editorial workflow ---
$wf = $load('workflows.workflow.geo_starter_editorial') ?? [];
$typeSettings = $wf['type_settings'] ?? [];
$workflowErrors = [];
$states = [];
foreach ($typeSettings['states'] ?? [] as $id => $state) {
  $states[$id] = [
    'label' => $state['label'] ?? $id,
    'weight' => (int) ($state['weight'] ?? 0),
    'published' => (bool) ($state['published'] ?? false),
    'defaultRevision' => (bool) ($state['default_revision'] ?? false),
  ];
}

$transitions = [];
foreach ($typeSettings['transitions'] ?? [] as $id => $transition) {
  $from = array_values(array_map('strval', $transition['from'] ?? []));
  $to = (string) ($transition['to'] ?? '');
  if ($from === [] || $to === '') {
    $workflowErrors[] = "transition $id must declare non-empty from/to states";
  }
  $transitions[$id] = [
    'label' => $transition['label'] ?? $id,
    'from' => $from,
    'to' => $to,
    'weight' => (int) ($transition['weight'] ?? 0),
  ];
}

$appliesTo = [];
foreach ($typeSettings['entity_types'] ?? [] as $entityType => $entityBundles) {
  foreach ($entityBundles as $entityBundle) {
    $appliesTo[] = "$entityType:$entityBundle";
  }
}

$workflow = [
  'id' => (string) ($wf['id'] ?? ''),
  'type' => (string) ($wf['type'] ?? ''),
  'appliesTo' => $appliesTo,
  'defaultState' => (string) ($typeSettings['default_moderation_state'] ?? ''),
  'states' => $states,
  'transitions' => $transitions,
];
if ($workflow['id'] === '' || $workflow['type'] === '' || $states === [] || $transitions === [] || $appliesTo === []) {
  $workflowErrors[] = 'workflow id, type, states, transitions, and entity-type coverage are required';
}
if (!isset($states[$workflow['defaultState']])) {
  $workflowErrors[] = 'default moderation state must reference a declared state';
}
foreach ($transitions as $id => $transition) {
  $unknownStates = array_diff([...$transition['from'], $transition['to']], array_keys($states));
  if ($unknownStates !== []) {
    $workflowErrors[] = sprintf('transition %s references unknown states: %s', $id, implode(', ', $unknownStates));
  }
}
if ($workflowErrors !== []) {
  fwrite(STDERR, "INVALID WORKFLOW CONFIG:\n- " . implode("\n- ", $workflowErrors) . "\n");
  exit(2);
}

fwrite(STDERR, sprintf(
  "Parsed %d content types, %d field storages, %d workflow states, %d transitions, and %d bundle applications from config/.\n",
  count($contentTypes), count($storage), count($states), count($transitions), count($appliesTo)
));

// The full document (metadata blocks mirror the committed schema; the
// generator owns contentTypes, the workflow's machine facts, and the payload definitions,
// which are derived from config. Hand-authored prose blocks are preserved from
// the existing file (loaded above).
$doc = $existing;
$doc['$id'] = $schemaId;
$doc['description'] = $schemaDescription;
$doc['version'] = $schemaVersion;
$doc['contentTypes'] = $contentTypes;
$writeRule = $existing['workflow']['writeRule'] ?? '';
$doc['workflow'] = array_merge($workflow, [
  'writeRule' => is_string($writeRule) ? $writeRule : '',
]);
$doc['$comment'] = 'The root schema selects exactly one strict logical-node payload. These are not JSON:API wire payloads (which split attributes and relationships); use #/$defs/{bundle} to validate a known content type directly. Reference fields use UUID arrays.';
$doc['oneOf'] = array_map(
  static fn (string $bundle): array => ['$ref' => '#/$defs/' . $bundle],
  $bundles,
);
$doc['$defs'] = $payloadDefs;

$json = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";

if ($check) {
  $current = is_file($outFile) ? file_get_contents($outFile) : '';
  // Compare only the generator-owned slices to avoid whitespace churn noise.
  $workflowKeys = array_keys($workflow);
  $workflowSlice = static function (mixed $value) use ($workflowKeys): array {
    if (!is_array($value)) {
      return [];
    }
    $slice = [];
    foreach ($workflowKeys as $key) {
      $slice[$key] = $value[$key] ?? null;
    }
    return $slice;
  };
  $a = json_encode([
    'contentTypes' => $contentTypes,
    'workflow' => $workflow,
    'id' => $schemaId,
    'description' => $schemaDescription,
    'version' => $schemaVersion,
    'comment' => $doc['$comment'],
    'oneOf' => $doc['oneOf'],
    'defs' => $payloadDefs,
  ]);
  $cur = json_decode($current ?: '{}', true) ?: [];
  $b = json_encode([
    'contentTypes' => $cur['contentTypes'] ?? null,
    'workflow' => $workflowSlice($cur['workflow'] ?? null),
    'id' => $cur['$id'] ?? null,
    'description' => $cur['description'] ?? null,
    'version' => $cur['version'] ?? null,
    'comment' => $cur['$comment'] ?? null,
    'oneOf' => $cur['oneOf'] ?? null,
    'defs' => $cur['$defs'] ?? null,
  ]);
  if ($a !== $b) {
    fwrite(STDERR, "DRIFT: docs/api/content-model.schema.json is out of date with config/.\n");
    fwrite(STDERR, "Run: php tools/generate-content-model-schema.php\n");
    exit(1);
  }
  fwrite(STDERR, "OK: content-model.schema.json matches config/.\n");
  exit(0);
}

file_put_contents($outFile, $json);
fwrite(STDERR, "Wrote $outFile\n");
