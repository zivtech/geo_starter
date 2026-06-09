<?php

declare(strict_types=1);

namespace Drupal\geo_starter_mcp;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Creates and updates GEO Starter nodes — always into the Draft state.
 *
 * The MCP write tools (`geo.create_node`, `geo.update_node`) delegate here. The
 * security contract (see docs/MCP.md and SECURITY.md): an agent may author
 * content, but it may NOT publish. Every write lands in moderation_state
 * `draft` and status FALSE; the `published` transition is reserved for a human
 * editor (or a role explicitly granted it). This keeps agent writes inside the
 * existing geo_starter_editorial workflow rather than bypassing governance.
 *
 * Transport-independent and kernel-testable without mcp_server.
 */
final class DraftWriter {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ContentModel $contentModel,
  ) {}

  /**
   * Creates a new Draft node.
   *
   * @param string $bundle
   *   One of ContentModel::BUNDLES.
   * @param array $values
   *   Flat field map (machineName => value), including `title`.
   *
   * @return array
   *   { uuid, nid, moderation_state } on success.
   *
   * @throws \InvalidArgumentException
   *   If the payload fails content-model validation.
   */
  public function create(string $bundle, array $values): array {
    $problems = $this->contentModel->validate($bundle, $values);
    if ($problems) {
      throw new \InvalidArgumentException('Invalid payload: ' . implode(' ', $problems));
    }
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entityTypeManager->getStorage('node')->create(
      ['type' => $bundle] + $values + $this->draftDefaults()
    );
    $this->forceDraft($node);
    $node->save();
    return $this->summary($node);
  }

  /**
   * Updates an existing node, creating a new Draft revision.
   *
   * @param string $uuid
   *   The node UUID.
   * @param array $values
   *   Flat field map of fields to change.
   */
  public function update(string $uuid, array $values): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $nodes = $storage->loadByProperties(['uuid' => $uuid]);
    /** @var \Drupal\node\NodeInterface|null $node */
    $node = $nodes ? reset($nodes) : NULL;
    if (!$node) {
      throw new \InvalidArgumentException(sprintf('No node with uuid "%s".', $uuid));
    }
    foreach ($values as $name => $value) {
      $node->set($name, $value);
    }
    // New revision in Draft — never silently re-publish on edit.
    $node->setNewRevision(TRUE);
    $this->forceDraft($node);
    $node->save();
    return $this->summary($node);
  }

  private function draftDefaults(): array {
    return ['status' => FALSE, 'moderation_state' => 'draft'];
  }

  private function forceDraft(NodeInterface $node): void {
    if ($node->hasField('moderation_state')) {
      $node->set('moderation_state', 'draft');
    }
    $node->setUnpublished();
  }

  private function summary(NodeInterface $node): array {
    return [
      'uuid' => $node->uuid(),
      'nid' => (int) $node->id(),
      'moderation_state' => $node->hasField('moderation_state')
        ? $node->get('moderation_state')->value
        : 'draft',
    ];
  }

}
