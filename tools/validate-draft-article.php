<?php

declare(strict_types=1);

/** @file Dependency-free CLI validator for one draft Article handoff. */

require_once __DIR__ . '/draft-article-contract.php';

if ($argc !== 2) {
  fwrite(STDERR, "Usage: php tools/validate-draft-article.php PATH_TO_ARTIFACT.json\n");
  exit(2);
}

try {
  $payload = geo_starter_read_draft_article($argv[1]);
}
catch (\InvalidArgumentException $exception) {
  fwrite(STDERR, "ERROR: {$exception->getMessage()}\n");
  exit(2);
}

$errors = geo_starter_validate_draft_article($payload);
if ($errors !== []) {
  fwrite(STDERR, "INVALID draft Article artifact:\n- " . implode("\n- ", $errors) . "\n");
  exit(1);
}

fwrite(STDOUT, "VALID draft Article artifact (draft-only; no content was changed).\n");
