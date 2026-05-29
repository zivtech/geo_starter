# GEO Starter

GEO Starter is a Drupal CMS site-template scaffold for teams moving from headless/composable or legacy page/post CMS stacks into a governed foundation for Generative Engine Optimization (GEO). It provides sourceable services, answers, articles, and evidence records designed to be readable by humans and inspectable by retrieval systems.

This scaffold is intentionally lean. The Starter Kit/DDEV spike proved that `drush site:export` can generate a valid site template after a Drupal CMS site is installed, but raw export output from the default non-interactive Starter template was too broad for the Community alpha. This package starts from the verified `type: Site` and `drupal-recipe` shape instead.

## Status

Community alpha scaffold. Not Marketplace-ready.

## Current Scope

- Drupal CMS recipe package shape.
- Core modules needed for content modeling, moderation, JSON:API retrieval proof, media, taxonomy, paths, and views.
- Drupal CMS admin, media, privacy, and basic SEO recipes.
- Required package dependencies for Canvas and Paragraphs authoring lanes.
- One Canvas Page shell and one proof Paragraph type, `geo_starter_section`, with `field_sections` attached to Service, Answer, and Article nodes.
- MVP content model config for Service, Answer, Article, Evidence Source, Audience, Topic, and Service area.
- Public-service sample content: 4 Services, 8 Answers, 3 Articles, 6 Evidence Sources, and seeded taxonomy terms.
- Planning docs now frame the starter as a migration destination for headless/composable and page/post CMS patterns; importer automation is not included yet.
- Placeholder `screenshot.webp` copied from the Drupal CMS Site Template Starter Kit so Drupal CMS installer discovery can parse the template during alpha smoke tests.

## Demo Feature Coverage

This maps the Drupal AI-era demo wireframe to what the starter currently proves.

| Wireframe checkbox | Present in GEO Starter | Current proof |
| --- | --- | --- |
| Structured content model | Yes | Service, Answer, Article, Evidence Source, Audience, Topic, and Service area config. |
| Composable content operations | Partial | Typed content, taxonomy, relationships, moderation, JSON:API, and one reusable Paragraph section type. Views/editor dashboards are not built out yet. |
| Editorial governance | Yes | Content moderation workflow for Service, Answer, Article, and Evidence Source. |
| Rendered semantic HTML | Partial | Structured node pages and a Paragraph section render. Final theme and semantic template review remain open. |
| Schema.org / metadata from fields | Planned | Schema map exists, but JSON-LD output is intentionally deferred until rendered-content parity is proven. |
| API access for AI consumption | Yes | JSON:API published/draft access proof passes in a fresh Drupal CMS install. |
| Agent/tool protocol story | Planned | Drupal AI and agent paths are documented as future integration surfaces; no write-capable agent workflow ships in the alpha. |
| AI provider choice | Planned | The starter does not configure an AI provider. Provider choice remains a Drupal AI integration decision. |
| Open ownership / no CMS lock-in | Yes | Drupal recipe package with no proprietary runtime dependency. |
| Contentful-to-Drupal migration wedge | Partial | Migration destination map exists; turnkey importer automation is out of scope for the alpha. |

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
- Specialized Paragraph bundles beyond the alpha `geo_starter_section` proof type.
- Views/editor dashboards for content operations.
- Turnkey source-CMS import automation.
- Rendered JSON-LD/schema templates.
- Final representative Marketplace/listing screenshot.
- Required AI provider, agent, or credential setup.
- Marketplace submission metadata, screenshot, support policy, or preview URL.

Those remain gated by the content-model critic, schema-map, access/security checks, and Marketplace readiness plan.
