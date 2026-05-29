# Validation

## Current Smoke-Test Evidence

The original lean alpha package was validated in a disposable Drupal CMS DDEV install before the Canvas and Paragraphs dependency expansion.

Passed checks:

- `composer validate --strict`
- Fresh Drupal CMS install with the recipe selected
- Import of 4 Services, 8 Answers, 3 Articles, 6 Evidence Sources, 5 Audience terms, 8 Topic terms, and 4 Service area terms
- Anonymous JSON:API detail endpoints return `200` for published sample content
- Anonymous JSON:API detail endpoints return `403` for draft probe content
- JSON:API collection endpoints exclude draft probe content

After adding the required Canvas, Paragraphs, and Entity Reference Revisions package constraints on 2026-05-29, the lightweight package checks were rerun:

- `composer validate --strict`
- `git diff --check`

After the follow-up controlled-vocabulary and GEO readiness copy revisions on 2026-05-29, static checks were rerun:

- `composer validate --strict`
- `git diff --check`
- Ruby YAML parse across 124 YAML files
- Config dependency and sample-content field reference consistency check

## Fresh GEO Starter Follow-Up Acceptance Proof (2026-05-29)

The controlled-vocabulary and GEO readiness follow-up was validated in a clean Drupal CMS DDEV project after commit `e72b881`.

- Project path: `/Users/AlexUA_1/Documents/Codex/ddev-tests/geo_starter_acceptance_20260529152151`
- DDEV project: `geo-starter-152151`
- Local URL: `https://geo-starter-152151.ddev.site`
- Drupal CMS package: `drupal/cms` 2.1.2
- Drupal core: 11.3.11
- Drush: 13.7.3.0

The current recipe was copied into the project as a local Composer path repository and required as `drupal/geo_starter:1.0.0-alpha1@alpha`.

Passed checks:

- `ddev composer create-project drupal/cms . --no-interaction`
- `ddev composer require 'drupal/geo_starter:1.0.0-alpha1@alpha' --no-interaction`
- Composer installed `drupal/geo_starter`, `drupal/canvas` 1.4.1, `drupal/paragraphs` 1.20.0, and `drupal/entity_reference_revisions` 1.14.0.
- Composer reported `No security vulnerability advisories found.`
- `ddev drush site:install recipes/geo_starter --account-pass=admin --site-name='GEO Starter Followup Validation' -y`
- Drupal reported `Installation complete. (Admin)`.
- Enabled module check confirmed `canvas`, `paragraphs`, `entity_reference_revisions`, `jsonapi`, `content_moderation`, `workflows`, and `media_library`.
- Route checks returned `200` for `/geo-starter` and `/apply-emergency-food-and-utility-assistance`.
- YAML lint passed for all 124 recipe YAML files.

Imported content counts:

| Type | Count |
| --- | ---: |
| Service nodes | 4 |
| Answer nodes | 8 |
| Article nodes | 3 |
| Evidence Source nodes | 6 |
| Audience terms | 5 |
| Topic terms | 8 |
| Service Area terms | 4 |
| Canvas Page entities | 1 |

Field checks:

| Field | Exists |
| --- | --- |
| `node.service.field_service_area` | Yes |
| `node.service.field_topic` | Yes |
| `node.service.field_audience` | Yes |
| `node.evidence_source.field_topic` | Yes |

JSON:API detail checks:

| Endpoint content | Expected | Actual |
| --- | ---: | ---: |
| Published Service | `200` | `200` |
| Published Answer | `200` | `200` |
| Published Article | `200` | `200` |
| Published Evidence Source | `200` | `200` |
| Draft Service probe | `403` | `403` |
| Draft Answer probe | `403` | `403` |
| Draft Article probe | `403` | `403` |
| Draft Evidence Source probe | `403` | `403` |

JSON:API collection checks filtered by each draft probe title returned zero matching items for Service, Answer, Article, and Evidence Source collections.

## Fresh GEO Starter `1.0.0-alpha1` Acceptance Proof (2026-05-29)

The GEO rename, basic Olivero public theme, and authoring configuration were validated in a clean Drupal CMS DDEV project:

- Project path: `/Users/AlexUA_1/Documents/Codex/ddev-tests/geo_starter_acceptance_20260529134806`
- DDEV project: `geo-starter-134806`
- Local URL: `https://geo-starter-134806.ddev.site`
- Drupal CMS package: `drupal/cms` 2.1.2
- Drupal core: 11.3.11
- Drush: 13.7.3.0

The current recipe was copied into the project as a local Composer path repository and installed as `drupal/geo_starter:1.0.0-alpha1@alpha`.

Passed checks:

- `composer require 'drupal/geo_starter:1.0.0-alpha1@alpha' --no-interaction`
- Composer installed `drupal/geo_starter`, `drupal/paragraphs` 1.20.0, and `drupal/entity_reference_revisions` 1.14.0.
- Composer reported `No security vulnerability advisories found.`
- `drush site:install recipes/geo_starter --account-pass=admin --site-name='GEO Starter Alpha Readiness' -y`
- Drupal reported `Installation complete. (Admin)`.
- Enabled module check confirmed `canvas`, `paragraphs`, `entity_reference_revisions`, `jsonapi`, `content_moderation`, `workflows`, and `media_library`.
- Public default theme check confirmed `olivero`.
- The unpacked recipe directory contains 123 YAML files, all parsed successfully with Symfony YAML in the Drupal CMS project.
- The empty `recommended.yml` placeholder was removed because the alpha does not ship curated Project Browser add-ons.
- Service, Answer, and Article bundles each expose `field_sections` as an `entity_reference_revisions` field.
- The `geo_starter_section` Paragraph type is installed.
- One Canvas Page entity imports with UUID `45000000-0000-4000-8000-000000000001`.
- `/geo-starter` returns `200`.
- A runtime Paragraph section probe can be created, attached to the sample Service node, saved, and rendered.
- `/apply-emergency-food-and-utility-assistance` returns `200` after the Paragraph section is attached.
- Rendered HTML includes `Migration proof`, `Keep answer-ready facts together`, and the section body text.
- `screenshot.webp` was captured from the rendered sample Service page at 1280x900 and converted to WebP.
- JSON:API published/draft checks were rerun after the authoring config and still passed.

Imported content counts:

| Type | Count |
| --- | ---: |
| Service nodes | 4 |
| Answer nodes | 8 |
| Article nodes | 3 |
| Evidence Source nodes | 6 |
| Audience terms | 5 |
| Topic terms | 8 |
| Service Area terms | 4 |
| Canvas Page entities | 1 |

JSON:API detail checks:

| Endpoint content | Expected | Actual |
| --- | ---: | ---: |
| Published Evidence Source | `200` | `200` |
| Published Service | `200` | `200` |
| Published Answer | `200` | `200` |
| Published Article | `200` | `200` |
| Draft Service probe | `403` | `403` |
| Draft Answer probe | `403` | `403` |
| Draft Article probe | `403` | `403` |
| Draft Evidence Source probe | `403` | `403` |

JSON:API collection checks filtered by each draft probe title returned `200` with zero matching items for Service, Answer, Article, and Evidence Source collections.

What this proves:

- The package can be required into a fresh Drupal CMS project through Composer.
- The recipe can be selected as the installation recipe for a fresh Drupal CMS site.
- The current dependency set is resolvable without patches or exact pins.
- The starter content imports with the expected counts.
- Anonymous JSON:API access protects draft node content for the current content model.
- The recipe installs a real Paragraph authoring lane for structured content.
- A Canvas Page shell can be imported and served.

What this does not prove:

- A finished GEO-specific public theme or design system implementation.
- Component-composed Canvas landing pages authored from the recipe.
- Manual editor UI create/edit/reorder behavior for Paragraph sections.
- Paragraph access behavior across a broader set of Paragraph bundles.
- Rendered JSON-LD/schema output.
- Sitemap/internal search behavior.
- Full responsive screenshot, accessibility, or performance acceptance.

## Local Helper Scripts

The helper scripts are not part of Drupal runtime behavior. They are included to make the alpha smoke-test process reproducible:

- `tools/create-alpha-sample-content.php`
- `tools/create-jsonapi-access-probes.php`

Run them from a Drupal root with Drush, for example:

```bash
drush php:script /path/to/tools/create-alpha-sample-content.php
drush php:script /path/to/tools/create-jsonapi-access-probes.php
```

## Not Proven Yet

- Final GEO-specific theme implementation
- Rendered JSON-LD/schema output
- Accessibility review
- Internal search behavior
- Sitemap behavior
- Component-composed Canvas pages
- Manual editor UI/reorder proof for Paragraph sections
- Source-CMS import automation or migration execution
- Marketplace submission readiness
