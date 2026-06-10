# Provenance

GEO Starter is a standalone Drupal CMS site-template recipe package authored from the `ai-initiative-modules` research workspace.

## Included Here

- The `geo_starter` Drupal recipe package.
- Content-model configuration for the 1.x stable line.
- Public-service sample content (fictional; see `docs/CONTENT_LICENSES.md`).
- Planning posture for a migration-oriented starter; no importer automation is included in this package.
- Helper scripts under `tools/` used to regenerate sample content, run JSON:API access probes, and lint the content dependency graph during development.

## Not Included Here

This repository does not vendor or mirror the externally hosted Drupal modules that were present in the broader local workspace. Those modules remain in their upstream Drupal.org repositories.

External dependencies are declared in `composer.json` as Drupal packages, not copied into this repository:

- `drupal/canvas`
- `drupal/drupal_cms_admin_ui`
- `drupal/drupal_cms_media`
- `drupal/drupal_cms_privacy_basic`
- `drupal/drupal_cms_seo_basic`
- `drupal/entity_reference_revisions`
- `drupal/geo_starter_jsonld` (the companion module, its own Drupal.org project)
- `drupal/mercury`
- `drupal/office_hours`
- `drupal/paragraphs`
- `drupal/simple_sitemap`

## Screenshot Provenance

`screenshot.webp` is a representative screenshot of the composed C-01 homepage (Canvas + Mercury) captured on a fresh install (refreshed 2026-06-05; an earlier version showed the sample Service page). Replace it with final Marketplace imagery before Marketplace submission.
