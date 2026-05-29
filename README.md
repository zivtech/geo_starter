# AI Visibility Starter

AI Visibility Starter is a Drupal CMS site-template scaffold for teams moving from headless/composable or legacy page/post CMS stacks into a governed, GEO-friendly Drupal foundation. It provides sourceable services, answers, articles, and evidence records designed to be readable by humans and inspectable by retrieval systems.

This scaffold is intentionally lean. The Starter Kit/DDEV spike proved that `drush site:export` can generate a valid site template after a Drupal CMS site is installed, but raw export output from the default non-interactive Starter template was too broad for the Community alpha. This package starts from the verified `type: Site` and `drupal-recipe` shape instead.

## Status

Community alpha scaffold. Not Marketplace-ready.

## Current Scope

- Drupal CMS recipe package shape.
- Core modules needed for content modeling, moderation, JSON:API retrieval proof, media, taxonomy, paths, and views.
- Drupal CMS admin, media, privacy, and basic SEO recipes.
- Required package dependencies for Canvas and Paragraphs authoring lanes.
- One Canvas Page shell and one proof Paragraph type, `ai_visibility_section`, with `field_sections` attached to Service, Answer, and Article nodes.
- MVP content model config for Service, Answer, Article, Evidence Source, Audience, Topic, and Service area.
- Public-service sample content: 4 Services, 8 Answers, 3 Articles, 6 Evidence Sources, and seeded taxonomy terms.
- Planning docs now frame the starter as a migration destination for headless/composable and page/post CMS patterns; importer automation is not included yet.
- Placeholder `screenshot.webp` copied from the Drupal CMS Site Template Starter Kit so Drupal CMS installer discovery can parse the template during alpha smoke tests.

## Provenance

This repository contains the starter package only. It does not vendor the externally hosted Drupal modules that were present in the broader research workspace. See `docs/PROVENANCE.md`.

## Install And Release Readiness

- `docs/INSTALL.md`
- `docs/LIMITATIONS.md`
- `docs/PUBLISHING_AND_ACCEPTANCE_PLAN.md`
- `docs/TECHNICAL_ACCEPTANCE_PLAN.md`
- `docs/PROJECT_PAGE_DRAFT.md`
- `docs/MARKETPLACE_SUBMISSION_PACKET.md`
- `docs/RELEASE_CHECKLIST.md`
- `docs/DEPENDENCIES.md`
- `docs/CONTENT_LICENSES.md`
- `docs/AUTHORING_MODEL.md`
- `docs/MIGRATION_MAP.md`
- `docs/SCHEMA_MAP.md`

## Validation

See `docs/VALIDATION.md` for the current smoke-test evidence and the helper scripts used to reproduce the sample-content and JSON:API checks.

## Not In This Scaffold Yet

- Theme implementation.
- Component-composed Canvas pages and shared component implementation.
- Specialized Paragraph bundles beyond the alpha `ai_visibility_section` proof type.
- Turnkey source-CMS import automation.
- Rendered JSON-LD/schema templates.
- Final representative Marketplace/listing screenshot.
- Required AI provider, agent, or credential setup.
- Marketplace submission metadata, screenshot, support policy, or preview URL.

Those remain gated by the content-model critic, schema-map, access/security checks, and Marketplace readiness plan.
