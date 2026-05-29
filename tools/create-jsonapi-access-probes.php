<?php

declare(strict_types=1);

use Drupal\node\Entity\Node;

$uuids = [
  'resident' => '3dcd92ee-13c9-4da0-a525-92fbfc4216a5',
  'eligibility' => '15354b34-fe0b-4470-8f1f-8b5f1dddfa0f',
  'benefits' => '051a0930-6e66-446b-a28f-fef4d5640bdc',
  'evidence' => '40000000-0000-4000-8000-000000000001',
  'service' => '41000000-0000-4000-8000-000000000001',
  'answer' => '42000000-0000-4000-8000-000000000001',
];

deleteProbeNodes([
  '44000000-0000-4000-8000-000000000001',
  '44000000-0000-4000-8000-000000000002',
  '44000000-0000-4000-8000-000000000003',
  '44000000-0000-4000-8000-000000000004',
]);

$resident = termId($uuids['resident']);
$eligibility = termId($uuids['eligibility']);
$benefits = termId($uuids['benefits']);
$evidence = nodeId($uuids['evidence']);
$service = nodeId($uuids['service']);
$answer = nodeId($uuids['answer']);

$nodes = [
  createDraftProbe([
    'uuid' => '44000000-0000-4000-8000-000000000001',
    'type' => 'service',
    'title' => 'Draft JSON API probe service',
    'field_direct_answer' => textValue('Draft probe service that should not be anonymous JSON:API content.'),
    'field_summary' => textValue('Draft probe service summary.'),
    'field_audience' => [['target_id' => $resident]],
    'field_service_area' => [['target_id' => $benefits]],
    'field_topic' => [['target_id' => $eligibility]],
    'field_next_action' => ['uri' => 'https://example.org/draft-probe', 'title' => 'Draft probe'],
    'field_evidence_sources' => [['target_id' => $evidence]],
    'field_reviewed_date' => '2026-05-29',
  ]),
  createDraftProbe([
    'uuid' => '44000000-0000-4000-8000-000000000002',
    'type' => 'answer',
    'title' => 'Draft JSON API probe answer',
    'field_direct_answer' => textValue('Draft probe answer that should not be anonymous JSON:API content.'),
    'body' => textValue('Draft probe expanded answer.'),
    'field_topic' => [['target_id' => $eligibility]],
    'field_audience' => [['target_id' => $resident]],
    'field_related_services' => [['target_id' => $service]],
    'field_evidence_sources' => [['target_id' => $evidence]],
    'field_reviewed_date' => '2026-05-29',
  ]),
  createDraftProbe([
    'uuid' => '44000000-0000-4000-8000-000000000003',
    'type' => 'article',
    'title' => 'Draft JSON API probe article',
    'field_summary' => textValue('Draft probe article summary.'),
    'body' => textValue('Draft probe article body that should not be anonymous JSON:API content.'),
    'field_topic' => [['target_id' => $eligibility]],
    'field_audience' => [['target_id' => $resident]],
    'field_author_name' => 'GEO Starter Probe',
    'field_related_services' => [['target_id' => $service]],
    'field_related_answers' => [['target_id' => $answer]],
    'field_evidence_sources' => [['target_id' => $evidence]],
    'field_reviewed_date' => '2026-05-29',
  ]),
  createDraftProbe([
    'uuid' => '44000000-0000-4000-8000-000000000004',
    'type' => 'evidence_source',
    'title' => 'Draft JSON API probe evidence source',
    'field_publisher' => 'GEO Starter Probe',
    'field_source_url' => ['uri' => 'https://example.org/draft-source-probe', 'title' => 'Draft source probe'],
    'field_source_type' => 'policy',
    'field_credibility_note' => textValue('Draft probe source that should not be anonymous JSON:API content.'),
  ]),
];

foreach ($nodes as $node) {
  echo $node->bundle() . '|' . $node->uuid() . '|' . $node->label() . PHP_EOL;
}

function deleteProbeNodes(array $uuids): void {
  $storage = \Drupal::entityTypeManager()->getStorage('node');
  foreach ($uuids as $uuid) {
    $nodes = $storage->loadByProperties(['uuid' => $uuid]);
    if ($nodes) {
      $storage->delete($nodes);
    }
  }
}

function createDraftProbe(array $values): Node {
  $node = Node::create($values + [
    'status' => FALSE,
    'moderation_state' => 'draft',
  ]);
  $node->save();
  return $node;
}

function textValue(string $value): array {
  return [
    'value' => $value,
    'format' => 'content_format',
  ];
}

function termId(string $uuid): int {
  $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['uuid' => $uuid]);
  return (int) reset($terms)->id();
}

function nodeId(string $uuid): int {
  $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['uuid' => $uuid]);
  return (int) reset($nodes)->id();
}
