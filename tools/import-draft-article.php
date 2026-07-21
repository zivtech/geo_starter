<?php

declare(strict_types=1);

/**
 * @file
 * Drush php:script helper that creates one validated, unpublished draft only.
 *
 * Usage:
 *   drush php:script tools/import-draft-article.php -- --artifact=/absolute/path/article.json --actor-uid=2 [--apply]
 */

require_once __DIR__ . '/draft-article-contract.php';

$scriptArguments = isset($extra) && is_array($extra)
  ? $extra
  : array_slice($_SERVER['argv'] ?? [], 1);
[$artifactPath, $actorUid, $apply, $argumentErrors] = geo_starter_import_arguments($scriptArguments);
if ($argumentErrors !== []) {
  geo_starter_import_fail($argumentErrors, 2);
}
if ($artifactPath === null || $actorUid === null) {
  geo_starter_import_fail(['Usage: --artifact=/absolute/path/article.json --actor-uid=EDITOR_UID [--apply]'], 2);
}

try {
  $artifactFingerprint = null;
  $payload = geo_starter_read_draft_article($artifactPath, $artifactFingerprint);
}
catch (\InvalidArgumentException $exception) {
  geo_starter_import_fail([$exception->getMessage()], 2);
}
$errors = geo_starter_validate_draft_article($payload);
if ($errors !== []) {
  geo_starter_import_fail($errors, 1);
}

if (!class_exists(\Drupal::class)) {
  geo_starter_import_fail(['Drupal is not bootstrapped. Run through drush php:script on an installed site.'], 2);
}

$actor = geo_starter_import_actor($actorUid);
$accountSwitcher = \Drupal::service('account_switcher');
$accountSwitcher->switchTo($actor);
$references = geo_starter_resolve_article_references($payload, $actor);
$storage = \Drupal::entityTypeManager()->getStorage('node');
$existing = $storage->loadByProperties(['uuid' => $payload['entityUuid']]);
if ($existing !== []) {
  geo_starter_import_fail(['entityUuid already exists; refusing to update or duplicate content.'], 1);
}

if (!$apply) {
  $accountSwitcher->switchBack();
  fwrite(STDOUT, sprintf(
    "DRY RUN: artifact uuid=%s sha256=%s is valid, references are viewable by actor uid %d, create access is allowed, and UUID is unused.\n",
    $payload['entityUuid'],
    $artifactFingerprint,
    $actor->id(),
  ));
  return;
}

$node = $storage->create(geo_starter_article_values($payload, $references, (int) $actor->id()));
$node->setUnpublished();
$node->set('moderation_state', 'draft');
$node->setNewRevision(true);
$node->setRevisionUserId((int) $actor->id());
$node->setRevisionLogMessage(sprintf(
  'Created from draft Article artifact %s; SHA-256: %s.',
  $payload['entityUuid'],
  $artifactFingerprint,
));
$violations = $node->validate();
if ($violations->count() > 0) {
  geo_starter_import_fail(array_map(
    static fn ($item): string => sprintf('%s: %s', $item->getPropertyPath(), $item->getMessage()),
    iterator_to_array($violations),
  ), 1);
}
$node->save();
$accountSwitcher->switchBack();
fwrite(STDOUT, sprintf(
  "CREATED: Article %d uuid=%s artifact_sha256=%s as unpublished moderation state draft.\n",
  $node->id(),
  $node->uuid(),
  $artifactFingerprint,
));

function geo_starter_import_actor(int $actorUid): \Drupal\user\UserInterface {
  $actor = \Drupal::entityTypeManager()->getStorage('user')->load($actorUid);
  if (!$actor instanceof \Drupal\user\UserInterface || !$actor->isActive()) {
    geo_starter_import_fail(['--actor-uid must identify an active Drupal user.'], 1);
  }
  $access = \Drupal::entityTypeManager()
    ->getAccessControlHandler('node')
    ->createAccess('article', $actor, [], TRUE);
  if (!$actor->hasPermission('create article content') || !$access->isAllowed()) {
    geo_starter_import_fail(['The selected actor is not allowed to create Article content.'], 1);
  }
  return $actor;
}

/** @return array<string,list<int>> */
function geo_starter_resolve_article_references(array $payload, \Drupal\Core\Session\AccountInterface $actor): array {
  $targets = [
    'field_topic' => ['taxonomy_term', ['topic']],
    'field_audience' => ['taxonomy_term', ['audience']],
    'field_evidence_sources' => ['node', ['evidence_source']],
    'field_related_services' => ['node', ['service']],
    'field_related_answers' => ['node', ['answer']],
  ];
  $resolved = [];
  foreach ($targets as $field => [$entityType, $bundles]) {
    $resolved[$field] = [];
    foreach ($payload[$field] ?? [] as $uuid) {
      $entities = \Drupal::entityTypeManager()->getStorage($entityType)->loadByProperties(['uuid' => $uuid]);
      $entity = reset($entities);
      if ($entity === false || !in_array($entity->bundle(), $bundles, true) || !$entity->access('view', $actor)) {
        geo_starter_import_fail(["$field reference $uuid is unresolved or has the wrong bundle."], 1);
      }
      $resolved[$field][] = (int) $entity->id();
    }
  }
  return $resolved;
}

/** @param array<string,list<int>> $references */
function geo_starter_article_values(array $payload, array $references, int $ownerId): array {
  $values = [
    'type' => 'article', 'uuid' => $payload['entityUuid'], 'title' => $payload['title'],
    'status' => false, 'moderation_state' => 'draft', 'uid' => $ownerId, 'revision_uid' => $ownerId,
    'field_summary' => [$payload['field_summary']], 'body' => [$payload['body']],
    'field_publication_date' => [['value' => $payload['field_publication_date']]],
    'field_reviewed_date' => [['value' => $payload['field_reviewed_date']]],
  ];
  foreach (['field_author_name', 'field_reviewed_by_name'] as $field) {
    if (isset($payload[$field])) {
      $values[$field] = [['value' => $payload[$field]]];
    }
  }
  foreach ($references as $field => $targetIds) {
    if ($targetIds !== []) {
      $values[$field] = array_map(static fn (int $id): array => ['target_id' => $id], $targetIds);
    }
  }
  return $values;
}

/** @param list<string> $errors */
function geo_starter_import_fail(array $errors, int $status): never {
  fwrite(STDERR, "DRAFT ARTICLE IMPORT REFUSED:\n- " . implode("\n- ", $errors) . "\n");
  exit($status);
}
