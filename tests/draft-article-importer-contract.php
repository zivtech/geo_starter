<?php

declare(strict_types=1);

require __DIR__ . '/../tools/draft-article-contract.php';

$valid = json_decode((string) file_get_contents(__DIR__ . '/../docs/api/examples/draft-article.valid.json'), true, 512, JSON_THROW_ON_ERROR);
if (geo_starter_validate_draft_article($valid) !== []) {
  throw new RuntimeException('Valid draft Article fixture failed PHP contract validation.');
}
$fingerprint = null;
$read = geo_starter_read_draft_article(__DIR__ . '/../docs/api/examples/draft-article.valid.json', $fingerprint);
if ($read !== $valid || $fingerprint !== hash_file('sha256', __DIR__ . '/../docs/api/examples/draft-article.valid.json')) {
  throw new RuntimeException('Artifact reader did not fingerprint the exact validated bytes.');
}

foreach (glob(__DIR__ . '/../docs/api/examples/draft-article.invalid-*.json') ?: [] as $fixture) {
  $invalid = json_decode((string) file_get_contents($fixture), true, 512, JSON_THROW_ON_ERROR);
  if (geo_starter_validate_draft_article($invalid) === []) {
    throw new RuntimeException("Invalid fixture passed PHP contract validation: $fixture");
  }
}

[$artifact, $actorUid, $apply, $argumentErrors] = geo_starter_import_arguments([
  '--artifact=/tmp/article.json',
  '--actor-uid=42',
  '--apply',
]);
if ($artifact !== '/tmp/article.json' || $actorUid !== 42 || !$apply || $argumentErrors !== []) {
  throw new RuntimeException('Valid importer arguments did not parse exactly.');
}

$invalidArgumentSets = [
  ['--actor-uid=0'],
  ['--actor-uid=2', '--actor-uid=3'],
  ['--artifact=a.json', '--artifact=b.json'],
  ['--apply', '--apply'],
  ['--publish'],
  ['unexpected-positional-argument'],
];
foreach ($invalidArgumentSets as $arguments) {
  if (geo_starter_import_arguments($arguments)[3] === []) {
    throw new RuntimeException('Invalid importer arguments were accepted: ' . implode(' ', $arguments));
  }
}

fwrite(STDOUT, "PHP draft Article contract fixtures passed.\n");
