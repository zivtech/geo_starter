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
- Core Olivero as the basic public frontend theme for alpha rendering.
- MVP content model config for Service, Answer, Article, Evidence Source, Audience and Topic.
- Public-service sample content: 4 Services, 8 Answers, 3 Articles, 6 Evidence Sources, and seeded taxonomy terms.
- Planning docs now frame the starter as a migration destination for headless/composable and page/post CMS patterns; importer automation is not included yet.
- Representative `screenshot.webp` captured from the rendered sample Service page in the alpha acceptance site.

## GEO Readiness

This section shows what the Community alpha supports now, what is partial, and what is still planned. It focuses on capabilities a content platform needs when search engines and AI systems decide what to cite.

**Content structure and operations**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Structured content model | Yes | Typed Service, Answer, Article, and Evidence Source entities with shared fields and controlled Topic and Audience vocabularies. |
| Composable content operations | Partial | Typed content, taxonomy, entity references, moderation, JSON:API, and one reusable `geo_starter_section` Paragraph type. Views and editor dashboards are not built yet. |
| Editorial governance | Yes | Draft → needs-review → published → archived moderation workflow across all four content types, with reviewed-date provenance. |

**Retrieval and rendering output**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Rendered semantic HTML | Partial | Structured node pages and a Paragraph section render through core Olivero. Final theme and semantic-template review remain open. |
| Schema.org / metadata from fields | Planned | A schema map exists; JSON-LD is deferred until rendered-content parity is proven. The recipe does not emit structured-data claims the visible page cannot support. |
| Structured API access | Yes | JSON:API (core) with published/draft access proven on a fresh install. This is a machine-readable integration surface — not, by itself, the channel through which answer engines form citations (those read rendered pages and the public web). |

**Agent and ownership readiness**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Agent / tool protocol story | Planned | Drupal AI and agent paths are documented as future integration surfaces; no write-capable agent workflow ships in the alpha. |
| AI provider choice | Open | The recipe configures no AI provider; provider choice stays an open Drupal AI integration decision, with no proprietary runtime. |
| Open ownership / no lock-in | Yes | Distributed as an open Drupal recipe with no proprietary runtime dependency. |

**Migration readiness**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Migration destination for headless/composable and page/post stacks | Partial | A destination content model and migration map exist; turnkey importer automation is out of scope for the alpha. |

**Scope and honest limits**

- AI citation, ranking, rich results, and answer-engine placement are never guaranteed. This recipe builds the structural foundation that helps; it does not promise outcomes.
- AI engines do not behave identically, so "AI visibility" is not a single switch. GEO Starter strengthens the rendered-HTML, structured-content, and cited-source path that documentation- and answer-citing engines favor. It does not, by itself, address community/forum or homepage-led citation patterns. Identify the engines your audience actually uses and measure per engine.

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

- GEO-specific theme/design-system implementation.
- Component-composed Canvas pages and shared component implementation.
- Specialized Paragraph bundles beyond the alpha `geo_starter_section` proof type.
- Views/editor dashboards for content operations.
- Turnkey source-CMS import automation.
- Rendered JSON-LD/schema templates.
- Final representative Marketplace/listing screenshot.
- Required AI provider, agent, or credential setup.
- Marketplace submission metadata, final support commitments, or preview URL.

Those remain gated by the content-model critic, schema-map, access/security checks, and Marketplace readiness plan.
