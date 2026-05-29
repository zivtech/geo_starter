# Provenance

GEO Starter is a standalone Drupal CMS site-template recipe package authored from the `ai-initiative-modules` research workspace.

## Included Here

- The `geo_starter` Drupal recipe package.
- Content-model configuration for the Community alpha.
- Public-service sample content for the Community alpha.
- Planning posture for a migration-oriented starter; no importer automation is included in this package.
- Helper scripts under `tools/` used to regenerate sample content and JSON:API access probes during local smoke tests.

## Not Included Here

This repository does not vendor or mirror the externally hosted Drupal modules that were present in the broader local workspace. Those modules remain in their upstream Drupal.org repositories.

External dependencies are declared in `composer.json` as Drupal packages, not copied into this repository:

- `drupal/drupal_cms_admin_ui`
- `drupal/drupal_cms_media`
- `drupal/drupal_cms_privacy_basic`
- `drupal/drupal_cms_seo_basic`
- `drupal/canvas`
- `drupal/paragraphs`
- `drupal/entity_reference_revisions`

## Known Placeholder

`screenshot.webp` is a temporary alpha placeholder copied from the Drupal CMS Site Template Starter Kit so Drupal CMS installer discovery can parse the template during smoke tests. Replace it with a representative screenshot before any public release or Marketplace submission.
