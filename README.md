# GEO Starter

GEO Starter is a Drupal CMS site-template scaffold for teams moving from headless/composable or legacy page/post CMS stacks into a governed foundation for Generative Engine Optimization (GEO). It provides sourceable services, answers, articles, and evidence records designed to be readable by humans and inspectable by retrieval systems.

This scaffold is intentionally lean. The Starter Kit/DDEV spike proved that `drush site:export` can generate a valid site template after a Drupal CMS site is installed, but raw export output from the default non-interactive Starter template was too broad for the Community alpha. This package starts from the verified `type: Site` and `drupal-recipe` shape instead.

## Status

Community alpha scaffold (`1.0.0-alpha4`). Not Marketplace-ready.

## Current Scope

- Drupal CMS recipe package shape.
- Core modules needed for content modeling, moderation, JSON:API retrieval proof, media, taxonomy, paths, and views.
- Drupal CMS admin, media, privacy, and basic SEO recipes.
- Required package dependencies for Canvas and Paragraphs authoring lanes.
- Schema.org JSON-LD emission via the required companion module `drupal/geo_starter_jsonld` (its own composer package — a recipe cannot bundle a module): `Service` with provider `ContactPoint`/`PostalAddress`/`hoursAvailable`, `Question`/`Answer`, `Article`, `CreativeWork` evidence records with resolvable cross-page `citation` `@id`s, and gated `FAQPage`, `HowTo`, and `ItemList` from section content. Full canonical published pages only; never beyond what the visible page renders.
- A ten-bundle section library attached to Service, Answer, and Article nodes via `field_sections`: `geo_starter_section`, `section_faq`(+`_item`), `section_step_list`(+`_item`), `section_card_grid`, `section_contact_panel` (structured `office_hours`), `section_cta`, `section_alert`, `section_media_text`.
- One Canvas Page shell for the visual-page authoring lane.
- Core Olivero as the basic public frontend theme for alpha rendering.
- MVP content model config for Service, Answer, Article, Evidence Source, Audience and Topic.
- Public-service sample content: 4 Services, 8 Answers, 3 Articles, 6 Evidence Sources, seeded taxonomy terms, and worked FAQ, step-list, card-grid, and contact-panel sections that exercise the JSON-LD path on install.
- Planning docs now frame the starter as a migration destination for headless/composable and page/post CMS patterns; importer automation is not included yet.
- Representative `screenshot.webp` captured from the rendered sample Service page in the alpha acceptance site.

## GEO Readiness

This section shows what the Community alpha supports now, what is partial, and what is still planned. It focuses on capabilities a content platform needs when search engines and AI systems decide what to cite.

**Content structure and operations**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Structured content model | Yes | Typed Service, Answer, Article, and Evidence Source entities with shared fields and controlled Topic and Audience vocabularies. |
| Composable content operations | Partial | Typed content, taxonomy, entity references, moderation, JSON:API, and the ten-bundle section library. Views and editor dashboards are not built yet. |
| Editorial governance | Yes | Draft → needs-review → published → archived moderation workflow across all four content types, with reviewed-date provenance. |

**Retrieval and rendering output**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Rendered semantic HTML | Partial | Structured node pages and section paragraphs render through core Olivero. Final theme and semantic-template review remain open. |
| Schema.org / metadata from fields | Yes, with a validation boundary | `drupal/geo_starter_jsonld` emits one parity-correct schema.org `@graph` per full canonical published page, from the same fields the page renders. Covered by PHPUnit suites in Drupal.org CI, a full-surface acceptance probe (23/23 on a fresh install), and an offline schema.org domain-correctness check. External validator / rich-results checks are still pending, so no rich-result eligibility is claimed. |
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
- Sample content for the `section_cta`, `section_alert`, and `section_media_text` bundles, and a design pass on section rendering.
- Views/editor dashboards for content operations.
- Turnkey source-CMS import automation.
- External schema.org validator / rich-results checks on the emitted JSON-LD.
- Final representative Marketplace/listing screenshot.
- Required AI provider, agent, or credential setup.
- Marketplace submission metadata, final support commitments, or preview URL.

Those remain gated by the content-model critic, schema-map, access/security checks, and Marketplace readiness plan.
