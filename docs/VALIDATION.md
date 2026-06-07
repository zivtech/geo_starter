# Validation

> **Current evidence: see "Corrected-Taxonomy Acceptance Proof (2026-05-29)" immediately
> below.** That run is the authoritative validation of the shipping model. The older
> sections after it were captured against the earlier, inverted taxonomy (`topic` held
> page-aspect terms; subjects lived in a `service_area` vocabulary) and are kept only as
> history — their term counts and field checks do **not** reflect the corrected model.

## Corrected-Taxonomy Acceptance Proof (2026-05-29)

The breaking taxonomy correction (subjects moved into `topic`; `service_area`,
`field_service_area`, and the page-aspect terms removed) was validated on a fresh Drupal CMS
install. This run also surfaced and fixed a stale content dependency graph: nine demo nodes
referenced the new `topic` terms in `field_topic` without declaring them in `depends`, which
aborted content import with `field_topic=This value should not be null`. The missing term
dependencies were added and the install was re-run clean.

- DDEV project: `geostarter-reval-20260529-171053`
- Drupal CMS package: `drupal/cms`; recipe required as `drupal/geo_starter:@dev` via a local
  Composer path repository.
- Recipe installed via `drush site:install recipes/geo_starter` (run three times clean after
  the fix).

Passed checks:

- `drush site:install recipes/geo_starter --account-pass=admin -y` → `Installation complete.`
- 22 nodes imported (21 published, 1 unpublished — the Privacy policy page).
- Node bundles: `service=4`, `answer=8`, `article=3`, `evidence_source=6`, `page=1`.
- Taxonomy: `topic=4`, `audience=5`. No `service_area` vocabulary; `field_service_area` and
  its storage absent; `field_topic` present.
- The `topic` vocabulary returns the four subject terms: Benefits and assistance, Community
  programs, Housing and utilities, Permits and records.
- Front page `/` returns `200`; the sample Service alias
  `/apply-emergency-food-and-utility-assistance` returns `200`.

JSON:API access checks (anonymous):

| Endpoint | Expected | Actual |
| --- | ---: | ---: |
| Published Service collection (`/jsonapi/node/service`) | `200`, 4 items | `200`, 4 items |
| Individual published Service node | `200` | `200` |
| Draft Service node (created at runtime) | `403` | `403` |
| Draft node present in anonymous collection | no | no |

What this proves:

- The recipe applies cleanly into a fresh Drupal CMS site with the corrected taxonomy and
  `field_topic` required on Service and Answer.
- A `topic` query returns subjects, not page sections.
- Anonymous JSON:API serves published content and protects drafts.

What this run did **not** re-exercise:

- The `tools/` generator scripts (development-only; they delete and re-create demo content
  and are not run by the recipe install).
- Rendered JSON-LD/schema, sitemap, search, accessibility, performance, and Canvas
  component composition (unchanged from the limits noted in the older sections below).

---

## Service FAQ Paragraph Slice Proof (2026-05-30)

The first specialized Paragraph slice was applied on the existing DDEV validation site:
`geostarter-reval-20260529-171053`.

Passed checks:

- `drush recipe /var/www/html/recipes/geo_starter -y` → `GEO Starter applied successfully`.
- `section_faq` and `section_faq_item` Paragraph types load.
- Service `field_sections` allows `section_faq`; it does not allow `section_faq_item` directly.
- Answer and Article `field_sections` do not allow `section_faq` in this slice.
- The emergency assistance Service node references one `section_faq` paragraph with two
  nested `section_faq_item` children.
- The rendered Service page returns `200` and includes the FAQ heading, both questions, and
  both answers.
- JSON:API for the published Service returns `200` and exposes the `field_sections`
  relationship as `paragraph--section_faq`.
- Draft JSON:API probe Service returns `403`, and the published Service collection remains
  at four items with no draft leakage.

What this proves:

- The Service-only FAQ Paragraph configuration applies through the recipe, renders visible
  FAQ content, and preserves the existing published/draft JSON:API access behavior.
- This is not a JSON-LD proof. Structured-data emission remains deferred until the
  `geo_starter_jsonld` package exists and its parity gates are validated.

---

## Historical: Pre-Correction Smoke-Test Evidence

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

## External Structured-Data Validation — WS-D Phase 1 (2026-06-05)

Hosted-validator sweep of rendered JSON-LD for **all four node types** (plus a
second Service page), per the beta plan WS-D Phase 1 (snippet mode — no public
demo URL exists yet). Source: fresh `site:install` of the recipe on the reval
DDEV workbench; full rendered page HTML curled anonymously, the
`application/ld+json` blocks extracted verbatim into minimal HTML snippet
documents.

### Schema Markup Validator (validator.schema.org) — PASS, zero errors

Method: each snippet POSTed to the hosted validator's own `/validate`
endpoint (the same service the UI calls). Entity detection confirmed non-zero
on every run, so the zeros are real passes, not empty parses.

| Page (type) | Types detected | Errors | Warnings |
| --- | --- | --- | --- |
| Service, flagship (`/apply-emergency-food-and-utility-assistance`) | WebPage, Service, FAQPage, HowTo, ItemList (4 triple groups) | 0 | 0 |
| Service, water bill (`/get-help-past-due-water-bill`) | WebPage, Service | 0 | 0 |
| Answer (`/who-can-apply-emergency-assistance`) | WebPage, Question | 0 | 0 |
| Article (`/how-answer-hub-keeps-public-service-pages-reviewable`) | WebPage, Article | 0 | 0 |
| Evidence Source (`/demo-city-benefits-guide`) | WebPage, CreativeWork | 0 | 0 |

This satisfies the WS-D DoD's hard bar: **zero errors on
validator.schema.org for all four types**.

Observation (not a violation): the emitted `Service.provider.name` follows
`system.site` name — on the workbench it reads "Drush Site-Install" (the
drush default). Correct module behavior; reads properly on any named install.

### Google Rich Results Test (code-snippet mode) — run 2026-06-07, one violation found and fixed

The snippet-mode RRT sweep ran (authenticated session; headless code-mode is
auth-walled — see note). It **caught a real rich-result violation that
validator.schema.org did not**, which is precisely why the beta plan made the
RRT sweep a hard Phase-1 gate.

**Findings (pre-fix):**

| Page (type) | Detected items | Result |
| --- | --- | --- |
| Service, flagship | FAQ, Carousels, Review snippets | FAQ ✅ valid · Carousels ✅ valid · **Review snippets ❌ invalid (1 critical issue)** |
| Service, water bill | Review snippets | **❌ invalid (1 critical issue)** |

The **FAQ rich result is valid and eligible** (the marquee GEO outcome). The
invalid item was **Review snippets**, on every node carrying a reviewer.

**Root cause:** `geo_starter_jsonld`'s `schemaReviewedBy()` emitted both
`reviewedBy` (Person — correct provenance) *and* a paired `review` → `Review`
object with `author` + `dateModified` but **no `reviewRating`**. Google reads
the bare `Review` as a star-rating snippet attempt and rejects it as invalid
without a rating. The schema is valid (validator.schema.org passed it), but the
rich result is not — the snippet-mode RRT is the only gate that surfaces this.

**Fix (`geo_starter_jsonld`, commit `672a07e`):** the `review` object is
dropped at its single source; only `reviewedBy` is emitted. Provenance intent
is fully preserved — `reviewedBy` (person) plus the `dateModified` each
normalizer already emits on its primary entity (Service→WebPage, Answer→
Question, Article→Article). A graph-level regression guard in
`ReviewedByPlacementTest` now asserts no node emits a `review` property.

**Post-fix re-validation (2026-06-07):**

- validator.schema.org re-run on all five corrected snippets: **0 errors,
  0 warnings**, entity detection non-empty (real passes).
- `tools/jsonld-probe.php` on the live install: **23/23** (parity held through
  the emission change).
- Kernel suite green (`ReviewedByPlacementTest` + `JsonLdGraphBuilderTest`,
  9 tests / 126 assertions); live emission confirmed `review`-free with
  `reviewedBy` retained on every type.

**Post-fix RRT eligibility re-confirmation: deferred to WS-D Phase 2 URL mode**
(maintainer decision, 2026-06-07). The sole invalid item's cause is structurally
eliminated and regression-guarded, and validator.schema.org is green post-fix;
the snippet-mode RRT re-submission is auth-walled for automation, so the
positive eligibility re-confirm rolls into the plan's already-scheduled Phase 2
URL-mode run against the WS-E public demo. Per the honest-claims rule, the
observed pre-fix findings are recorded as above and no post-fix eligibility
result is *claimed* until re-observed in Phase 2.

> **Headless note:** code-mode RRT submission requires a logged-in Google
> session. Anonymous headless and `--profile Default` (which did not carry the
> session — page showed "Sign in") both hit Google's "Log in and try again"
> wall on TEST CODE. validator.schema.org (POST endpoint) remains the reliable
> headless schema gate; RRT eligibility is a URL-mode/authenticated concern.

## Local Helper Scripts

The helper scripts are not part of Drupal runtime behavior and are **not run by the recipe
install**. They are development-only generators, kept so the demo content can be
regenerated.

- `tools/create-alpha-sample-content.php`
- `tools/create-jsonapi-access-probes.php`

> **Destructive — do not run on a fresh install.** The recipe already imports the demo
> content from `content/`. `create-alpha-sample-content.php` calls `deleteExistingNodes()`
> on the Service, Answer, Article, and Evidence Source bundles and re-creates them, which
> collides with the bundled content. Run these only against a site where that content is
> absent (for example, when regenerating `content/` for export).

```bash
drush php:script /path/to/tools/create-alpha-sample-content.php
drush php:script /path/to/tools/create-jsonapi-access-probes.php
```

## Not Proven Yet

- Final GEO-specific theme implementation (Mercury stock styling is the beta
  ceiling; the WS-B rendering/design pass for the ten section bundles remains
  open)
- Google Rich Results Test **post-fix eligibility re-confirmation** (the
  snippet-mode run happened and found+fixed one violation — see WS-D Phase 1
  above; the positive re-confirm is deferred to Phase 2 URL mode against the
  WS-E demo). FAQ rich result observed valid/eligible pre-fix.
- Accessibility review
- Internal search behavior
- Sitemap behavior
- Manual editor UI/reorder proof for Paragraph sections
- Source-CMS import automation or migration execution
- Marketplace submission readiness

*(Removed 2026-06-05 as now proven: rendered JSON-LD/schema output —
validated externally above; component-composed Canvas pages — C-01..C-04
shipped and fresh-install gated.)*
