<?php

declare(strict_types=1);

/** @file Local validation helpers shared by the draft-Article validator/importer. */

const GEO_STARTER_ARTIFACT_MAX_BYTES = 262144;
const GEO_STARTER_ARTIFACT_ALLOWED = [
  'artifactVersion', 'entityUuid', 'bundle', 'moderationState', 'title',
  'field_summary', 'body', 'field_author_name', 'field_publication_date',
  'field_reviewed_date', 'field_reviewed_by_name', 'field_topic',
  'field_audience', 'field_evidence_sources', 'field_related_services',
  'field_related_answers', 'reviewLinks',
];
const GEO_STARTER_ARTIFACT_REQUIRED = [
  'artifactVersion', 'entityUuid', 'bundle', 'moderationState', 'title',
  'field_summary', 'body', 'field_publication_date', 'field_reviewed_date',
  'field_evidence_sources', 'reviewLinks',
];
const GEO_STARTER_ARTIFACT_UUID_FIELDS = [
  'field_topic', 'field_audience', 'field_evidence_sources',
  'field_related_services', 'field_related_answers',
];
// Conservative ASCII HTTP(S) URL subset shared with the JSON Schema: no
// credentials, canonical decimal ports 1-65535, and valid percent escapes.
const GEO_STARTER_HTTP_URL_PATTERN = '`\Ahttps?://[A-Za-z0-9](?:[A-Za-z0-9.-]*[A-Za-z0-9])?(?::(?:[1-9][0-9]{0,3}|[1-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5]))?(?:/(?:[A-Za-z0-9._~!$&()*+,;=:@-]|%[0-9A-Fa-f]{2})*)*(?:\?(?:[A-Za-z0-9._~!$&()*+,;=:@/?-]|%[0-9A-Fa-f]{2})*)?(?:#(?:[A-Za-z0-9._~!$&()*+,;=:@/?-]|%[0-9A-Fa-f]{2})*)?\z`D';

/** @return array{0:?string,1:?int,2:bool,3:list<string>} */
function geo_starter_import_arguments(array $arguments): array {
  $artifact = null;
  $actorUid = null;
  $apply = false;
  $errors = [];
  foreach ($arguments as $argument) {
    if (str_starts_with($argument, '--artifact=')) {
      if ($artifact !== null) {
        $errors[] = 'Duplicate --artifact argument.';
      }
      else {
        $artifact = substr($argument, strlen('--artifact='));
      }
    }
    elseif (str_starts_with($argument, '--actor-uid=')) {
      $rawUid = substr($argument, strlen('--actor-uid='));
      if ($actorUid !== null) {
        $errors[] = 'Duplicate --actor-uid argument.';
      }
      elseif (!ctype_digit($rawUid) || (int) $rawUid < 1) {
        $errors[] = '--actor-uid must be a positive integer.';
      }
      else {
        $actorUid = (int) $rawUid;
      }
    }
    elseif ($argument === '--apply') {
      if ($apply) {
        $errors[] = 'Duplicate --apply argument.';
      }
      else {
        $apply = true;
      }
    }
    elseif ($argument !== '--') {
      $errors[] = "Unknown argument: $argument";
    }
  }
  return [$artifact, $actorUid, $apply, $errors];
}

/** @return list<string> */
function geo_starter_validate_draft_article(array $payload): array {
  $errors = [];
  geo_starter_validate_keys($payload, $errors);
  geo_starter_validate_constants($payload, $errors);
  geo_starter_validate_article_text($payload, $errors);
  geo_starter_validate_article_dates($payload, $errors);
  geo_starter_validate_article_references($payload, $errors);
  geo_starter_validate_review_links($payload, $errors);
  return $errors;
}

/**
 * Read one artifact and optionally return the SHA-256 of the exact bytes read.
 *
 * @return array<string,mixed>
 */
function geo_starter_read_draft_article(string $path, ?string &$fingerprint = null): array {
  $size = is_file($path) ? filesize($path) : false;
  if ($size === false || $size > GEO_STARTER_ARTIFACT_MAX_BYTES) {
    throw new \InvalidArgumentException('Artifact must be a readable JSON file no larger than 256 KiB.');
  }
  $raw = file_get_contents($path);
  if ($raw === false) {
    throw new \InvalidArgumentException('Artifact could not be read.');
  }
  if (strlen($raw) > GEO_STARTER_ARTIFACT_MAX_BYTES) {
    throw new \InvalidArgumentException('Artifact must be a readable JSON file no larger than 256 KiB.');
  }
  $fingerprint = hash('sha256', $raw);
  try {
    $document = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
    $payload = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
  }
  catch (\JsonException) {
    throw new \InvalidArgumentException('Artifact is not valid JSON.');
  }
  if (!$document instanceof \stdClass || !is_array($payload)) {
    throw new \InvalidArgumentException('Artifact root must be a JSON object.');
  }
  foreach ([...GEO_STARTER_ARTIFACT_UUID_FIELDS, 'reviewLinks'] as $field) {
    if (property_exists($document, $field) && !is_array($document->{$field})) {
      throw new \InvalidArgumentException("$field must be a JSON array, not an object.");
    }
  }
  return $payload;
}

/** @param list<string> $errors */
function geo_starter_validate_keys(array $payload, array &$errors): void {
  $unknown = array_diff(array_keys($payload), GEO_STARTER_ARTIFACT_ALLOWED);
  if ($unknown !== []) {
    $errors[] = '$: unknown keys: ' . implode(', ', $unknown);
  }
  $missing = array_diff(GEO_STARTER_ARTIFACT_REQUIRED, array_keys($payload));
  if ($missing !== []) {
    $errors[] = '$: missing required keys: ' . implode(', ', $missing);
  }
}

/** @param list<string> $errors */
function geo_starter_validate_constants(array $payload, array &$errors): void {
  if (($payload['artifactVersion'] ?? null) !== '1.0.0') {
    $errors[] = 'artifactVersion: must equal 1.0.0';
  }
  if (!geo_starter_is_uuid($payload['entityUuid'] ?? null)) {
    $errors[] = 'entityUuid: must be an RFC 4122 UUID';
  }
  if (($payload['bundle'] ?? null) !== 'article') {
    $errors[] = 'bundle: must equal article';
  }
  if (($payload['moderationState'] ?? null) !== 'draft') {
    $errors[] = 'moderationState: must equal draft; publishing is not permitted';
  }
}

/** @param list<string> $errors */
function geo_starter_validate_article_text(array $payload, array &$errors): void {
  geo_starter_validate_string($payload['title'] ?? null, 'title', 255, $errors, true);
  geo_starter_validate_text_value($payload['field_summary'] ?? null, 'field_summary', 10000, false, $errors);
  geo_starter_validate_text_value($payload['body'] ?? null, 'body', 100000, true, $errors);
  foreach (['field_author_name', 'field_reviewed_by_name'] as $field) {
    if (array_key_exists($field, $payload)) {
      geo_starter_validate_string($payload[$field], $field, 255, $errors, false);
    }
  }
}

/** @param list<string> $errors */
function geo_starter_validate_text_value(mixed $value, string $path, int $maximum, bool $allowsSummary, array &$errors): void {
  if (!is_array($value)) {
    $errors[] = "$path: must be an object";
    return;
  }
  $allowed = $allowsSummary ? ['value', 'format', 'summary'] : ['value', 'format'];
  $unknown = array_diff(array_keys($value), $allowed);
  if ($unknown !== []) {
    $errors[] = "$path: unknown keys: " . implode(', ', $unknown);
  }
  geo_starter_validate_string($value['value'] ?? null, "$path.value", $maximum, $errors, true);
  if (($value['format'] ?? null) !== 'content_format') {
    $errors[] = "$path.format: must equal content_format";
  }
  if ($allowsSummary && array_key_exists('summary', $value)) {
    geo_starter_validate_string($value['summary'], "$path.summary", 10000, $errors, false);
  }
}

/** @param list<string> $errors */
function geo_starter_validate_article_dates(array $payload, array &$errors): void {
  foreach (['field_publication_date', 'field_reviewed_date'] as $field) {
    if (!geo_starter_is_date($payload[$field] ?? null)) {
      $errors[] = "$field: must be a real ISO date (YYYY-MM-DD)";
    }
  }
}

/** @param list<string> $errors */
function geo_starter_validate_article_references(array $payload, array &$errors): void {
  foreach (GEO_STARTER_ARTIFACT_UUID_FIELDS as $field) {
    if (!array_key_exists($field, $payload)) {
      continue;
    }
    $value = $payload[$field];
    if (!is_array($value) || !array_is_list($value)) {
      $errors[] = "$field: must be an array of UUIDs";
      continue;
    }
    if ($field === 'field_evidence_sources' && $value === []) {
      $errors[] = "$field: must contain at least one UUID";
    }
    if (count($value) > 50 || count($value) !== count(array_unique($value, SORT_REGULAR))) {
      $errors[] = "$field: must contain at most 50 unique UUIDs";
    }
    foreach ($value as $index => $uuid) {
      if (!geo_starter_is_uuid($uuid)) {
        $errors[] = "$field.$index: must be an RFC 4122 UUID";
      }
    }
  }
}

/** @param list<string> $errors */
function geo_starter_validate_review_links(array $payload, array &$errors): void {
  $links = $payload['reviewLinks'] ?? null;
  if (!is_array($links) || !array_is_list($links) || $links === [] || count($links) > 25) {
    $errors[] = 'reviewLinks: must contain 1 to 25 links';
    return;
  }
  $seen = [];
  foreach ($links as $index => $link) {
    $path = "reviewLinks.$index";
    if (!is_array($link) || array_diff(array_keys($link), ['label', 'uri']) !== [] || count($link) !== 2) {
      $errors[] = "$path: must contain exactly label and uri";
      continue;
    }
    // JSON Schema uniqueItems compares objects by value, not source key order.
    // Canonicalize the fixed two-key object before using it as a duplicate key.
    $key = geo_starter_canonical_json($link);
    if (isset($seen[$key])) {
      $errors[] = "$path: must not duplicate another review link";
    }
    $seen[$key] = true;
    geo_starter_validate_string($link['label'] ?? null, "$path.label", 255, $errors, true);
    if (!geo_starter_is_http_url($link['uri'] ?? null)) {
      $errors[] = "$path.uri: must be an absolute HTTP(S) URL up to 2048 characters";
    }
  }
}

/** @param list<string> $errors */
function geo_starter_validate_string(mixed $value, string $path, int $maximum, array &$errors, bool $nonEmpty): void {
  if (!is_string($value) || geo_starter_unicode_length($value) > $maximum || ($nonEmpty && preg_match('/\S/u', $value) !== 1)) {
    $errors[] = "$path: must be " . ($nonEmpty ? 'a non-empty ' : 'a ') . "string up to $maximum characters";
  }
}

function geo_starter_unicode_length(string $value): int {
  if (function_exists('mb_strlen')) {
    return mb_strlen($value, 'UTF-8');
  }
  preg_match_all('/./u', $value, $characters);
  return count($characters[0]);
}

/** @param array<string,mixed> $value */
function geo_starter_canonical_json(array $value): string {
  ksort($value, SORT_STRING);
  return (string) json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

function geo_starter_is_uuid(mixed $value): bool {
  return is_string($value) && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $value) === 1;
}

function geo_starter_is_date(mixed $value): bool {
  if (!is_string($value) || preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value) !== 1 || (int) substr($value, 0, 4) < 1) {
    return false;
  }
  $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
  return $date !== false && $date->format('Y-m-d') === $value;
}

function geo_starter_is_http_url(mixed $value): bool {
  return is_string($value)
    && geo_starter_unicode_length($value) <= 2048
    && preg_match(GEO_STARTER_HTTP_URL_PATTERN, $value) === 1;
}
