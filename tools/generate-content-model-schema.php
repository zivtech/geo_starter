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
$bundles = ['service', 'answer', 'article', 'evidence_source'];
$schemaOrg = [
  'service' => ['Service', 'WebPage', 'BreadcrumbList'],
  'answer' => ['Question', 'Answer', 'WebPage'],
  'article' => ['Article', 'WebPage', 'BreadcrumbList'],
  'evidence_source' => ['CreativeWork'],
];

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
  foreach (glob("$configDir/field.field.node.$bundle.*.yml") as $file) {
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

// --- editorial workflow ---
$wf = $load('workflows.workflow.geo_starter_editorial') ?? [];
$states = [];
foreach ($wf['type_settings']['states'] ?? [] as $id => $s) {
  $states[$id] = ['label' => $s['label'] ?? $id, 'published' => (bool) ($s['published'] ?? false)];
}

fwrite(STDERR, sprintf(
  "Parsed %d content types, %d field storages, %d workflow states from config/.\n",
  count($contentTypes), count($storage), count($states)
));

// The full document (metadata blocks mirror the committed schema; the
// generator owns contentTypes + workflow.states, which are derived from config.
// Hand-authored prose blocks are preserved from the existing file (loaded above).
$doc = $existing;
$doc['contentTypes'] = $contentTypes;
if (isset($doc['workflow'])) {
  $doc['workflow']['states'] = $states;
}

$json = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";

if ($check) {
  $current = is_file($outFile) ? file_get_contents($outFile) : '';
  // Compare only the generator-owned slices to avoid whitespace churn noise.
  $a = json_encode(['contentTypes' => $contentTypes, 'states' => $states]);
  $cur = json_decode($current ?: '{}', true) ?: [];
  $b = json_encode(['contentTypes' => $cur['contentTypes'] ?? null, 'states' => $cur['workflow']['states'] ?? null]);
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
