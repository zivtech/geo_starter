# Validation

> **Current evidence: see "Stable 1.0.0 Released-Artifact Proof (2026-06-08)"** —
> the authoritative validation of the shipping `1.0.0` package, run against real
> drupal.org artifacts at the default `stable` Composer floor. The
> "Released-Artifact Install Proof — WS-E Precondition (2026-06-07)" section
> below records the beta2-era run that established the method and found the
> install-breaking defects. Earlier sections are kept as history; in particular,
> everything before "Corrected-Taxonomy Acceptance Proof (2026-05-29)" predates
> the corrected taxonomy and does **not** reflect the shipping model.

## Stable 1.0.0 Released-Artifact Proof (2026-06-08)

The stable-readiness gates (Phases 3–5 of
`docs/plans/2026-06-08-stable-1.0-readiness-plan.md`) were run against packaged
artifacts, never a working-tree rsync (the beta1 masking method):

- **Phase 3 — tag-tree install rehearsal** (recipe `a38e31b`, module `ad1d8ab`,
  built via `git archive`): fresh `drush site:install` clean (exit 0);
  `content-graph-lint.py` OK — 49 entities, 145 depends edges, no cycles, all
  field entity-refs declared; JSON-LD probe **23/23**; `/`, Service page, and
  `/sitemap.xml` return 200 anonymously; `composer audit` clean. Stable-floor
  resolution is not locally testable (path repositories bypass
  `minimum-stability`), so that assertion was deferred to Phase 5.
- **Phase 5 — real-artifact proof**: fresh `composer create-project drupal/cms`
  under `minimum-stability: stable`, pulling `drupal/geo_starter_jsonld
  1.0.0-rc1` **from drupal.org** (dist zip + drupalcode source). Fresh
  `site:install` clean; JSON-LD probe **23/23**; content-graph-lint OK;
  `/`, Service page, `/sitemap.xml` → 200; JSON-LD present; `composer audit`
  clean. Composer resolved canvas 1.5.0 → 1.5.1 (within the `>=1.4 <1.6` cap)
  and all checks stayed green, validating the bound on canvas 1.5.1 as well.
- **Stable-floor resolution proven**: after `geo_starter_jsonld 1.0.0` was
  published stable on drupal.org (release page 200, composer facade carries
  `1.0.0`), a default-stability project's `drupal/geo_starter_jsonld:^1.0`
  (no `@rc`) dry-run resolved `1.0.0-rc1 => 1.0.0`. The module's `1.0.0` is
  byte-identical to `rc1` and `beta1` (zero-diff assertion), so the Phase 5
  install/probe/render results carry to the stable artifact.

Together these prove what the `1.0.0` release claims: a site at the default
`stable` Composer floor can require the recipe's dependency set without any
stability override, install cleanly from the packaged tag tree, import all
sample content acyclically, render all pages, and emit validated JSON-LD.

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

## WS-F — JSON:API re-proof, sitemap, a11y (2026-06-07)

Run on the reval workbench (`geostarter-reval-20260529-171053`) against the
current recipe (post WS-B markup + the simple_sitemap addition below).

### JSON:API access re-proof (Track-3 gate 6) — PASS

The last published-200/draft-403 proof predated every surface beta added
(Canvas pages, the new paragraph samples, the markup layer). Re-run anonymously
with runtime draft probes (4 draft nodes via `tools/create-jsonapi-access-probes.php`,
plus a draft `canvas_page` and a `section_faq` paragraph attached to an
unpublished parent; all probes deleted after).

| Check | Expected | Actual |
| --- | --- | --- |
| Published node detail — service/answer/article/evidence_source | `200` | `200` (4/4) |
| Draft node detail — all four types | `403` | `403` (4/4) |
| Draft node absent from anonymous collection `data[]` | yes | yes (4/4) |
| Published `canvas_page` detail | `200` | `200` |
| Draft `canvas_page` detail | `403` | `403` |
| Draft `canvas_page` absent from collection `data[]` | yes | yes |
| Paragraph of an **unpublished** parent absent from `paragraph/section_faq` `data[]` | yes | yes |
| Paragraph of a **published** parent present (positive contrast) | yes | yes |

Nuance worth recording: Drupal JSON:API withholds inaccessible items via
`meta.omitted` (disclosing *that* items exist + their self-URLs, never their
content) rather than silently dropping them — the secure, by-design pattern. A
naive whole-body substring check false-flags this; the real assertion is
membership in `data[].id`, which excludes every draft.

> The matrix was executed before `simple_sitemap` was added to the recipe.
> `simple_sitemap` touches only sitemap generation — it does not alter node,
> canvas_page, or paragraph entity access — so the result stands for the final
> recipe. Stated explicitly rather than left to inference.

### XML sitemap (`simple_sitemap`) — PASS (config-complete; populates on cron)

`drupal_cms_seo_basic` (this Drupal CMS version) ships pathauto/redirect/
easy_breadcrumb but **no XML sitemap module**. Added `simple_sitemap` to the
recipe (on-thesis: answer engines can only cite what they can discover) with
per-bundle index config for the four canonical node types + `canvas_page`
(`config/simple_sitemap.bundle_settings.default.*`, priority 0.5, weekly).

Fresh `site:install` → generate (`drush simple-sitemap:generate`) →
`/sitemap.xml` **200, 26 `<loc>` entries**: all four content types
(service/answer/article/evidence_source), all three aliased Canvas pages
(homepage, topic hub, campaign), front page; the unpublished Privacy page is
excluded. JSON-LD probe re-run **23/23** (emission unaffected by the addition).

- **Recipe-completeness finding:** a recipe `site:install` imports a module's
  *simple* config from `config/install/` but skips its config-*entities* — the
  `default` sitemap variant + `default_hreflang` type never get created during
  the install batch (they do under interactive `drush en`). Fix: the recipe
  ships those four entities verbatim in `config/`
  (`simple_sitemap.sitemap.default`, `…type.default_hreflang`, the index pair);
  the recipe's config-import phase runs after module install with entity types
  available. Verified on a fresh install: entities present, no collision.
- **Cron caveat (honest DoD):** on a truly fresh install `/sitemap.xml` 404s
  until the first `automated_cron` run (Drupal CMS ships it) or a manual
  generate — recipes are config-only and cannot generate. The proof above is
  "config correct + on-demand generate → full coverage," not "live at install."

### Accessibility spot-check — PASS (no failures to fix or ticket)

Keyboard-only walks (agent-browser, real Tab presses) on the homepage and the
flagship Service page (the page carrying the WS-B section markup):

- **Skip-to-main-content** link is the first focusable element on both.
- **Visible focus indicator** (1px outline) on every interactive element at
  every tab stop; focus returns to the document after the last element — **no
  trap**. Logical focus order on both pages.
- The WS-B additions are all reachable with visible focus: the CTA button-link,
  the contact `mailto`. The open `<dl>` FAQ and plain-text phone correctly add
  no spurious tab stops (no hidden disclosure widget; no invented `tel:` link).
  The markup ships **zero JS and zero custom interactive widgets** — there is no
  focus-management surface to get wrong.

Contrast (WCAG 2.1) of every color pair the WS-B CSS ships — all **AA (≥4.5)**:

| Pair | Ratio |
| --- | ---: |
| Alert info heading `#1d4ed8` / `#eff6ff` | 6.16 |
| Alert success heading `#15803d` / `#f0fdf4` | 4.79 |
| Alert warning heading `#a16207` / `#fefce8` | 4.76 |
| Alert danger heading `#b91c1c` / `#fef2f2` | 5.91 |
| Alert body `#1f2937` / `#fefce8` | 14.19 |
| CTA button text `#ffffff` / `#1d4ed8` | 6.70 |

Not covered: the editorial dashboard (`/admin/content/geo`) is admin-gated and
rendered by core Views + the admin theme with no custom project markup —
keyboard behavior is inherited from core and not spot-checked here. Mercury's
own base-theme WCAG conformance is the theme's responsibility, not a
recipe-level gate.

## Released-Artifact Install Proof — WS-E Precondition (2026-06-07)

First install ever built the way a real user gets the packages, instead of
rsyncing the local checkout: fresh DDEV project (`geostarter-beta1-proof`),
`composer create-project drupal/cms` (template-era `drupal_cms_*` 2.1.3,
core 11.3.11, default `minimum-stability: stable` + `prefer-stable`), then
the recipe's dependency set required from packages.drupal.org —
`drupal/geo_starter_jsonld 1.0.0-beta1` via a root `^1.0@beta` constraint
(plain `^1.0` empirically refused: "does not match your minimum-stability"),
canvas 1.5.0, mercury 1.0.5, paragraphs 1.20.0, entity_reference_revisions
1.14.0, simple_sitemap 4.2.3, office_hours 1.29.0, and the four
`drupal_cms_*` recipes at 2.1.3 (a bare `drupal/cms` project does NOT carry
them — they are install-time selections; the manual path must require them).
The recipe itself was placed in `recipes/` from the repo: **site templates
are not served by the packages.drupal.org composer facade** (control:
`drupal/haven` 404s identically); the documented install path is the
Drupal CMS installer flow.

**Three ship-blocking defects found, each masking the next** (none had ever
been visible because every prior install rsynced a tree whose directory
order happened to dodge them):

1. **Default-content dependency cycle** — service `41…0001` →
   (`field_sections`, ERR) → card-grid ¶`46…0031` → (`field_section_cards`)
   → answers `42…0001/0002` → (`field_related_services`) → the same
   service. Core's `DefaultContent\Importer` has no cycle detection and
   passes unresolved dependencies to `setValue()` as NULL
   (`Importer.php:318`): with a cycle, **every** import order silently
   drops at least one reference, and when the broken edge is an
   entity_reference_revisions field the install crashes fatally
   (`EntityReferenceRevisionsItem::onChange()` calls `getRevisionId()` on
   NULL — ERR 1.14, line 230). The previously "green" acceptance installs
   were importing with 1 of 2 card references silently missing. Fixed in
   `ebdfc26` (cards retargeted to cycle-free answers); guarded by
   `tools/content-graph-lint.py` (`2c8f1bc`) which enforces depends
   completeness + acyclicity — run it before any release.
2. **Six of nine `canvas.component.*` configs were never exported** to
   `config/` (WS-A shipped only the chrome set: footer/navbar/text). Under
   the 2.1.3-era installer stack, canvas's component-config auto-generation
   runs after recipe content import — and skips `cta`/`section`/
   `hero-billboard` for mercury 1.0.5 entirely — so canvas_page import
   failed validation ("The 'canvas.component.sdc.mercury.section' config
   does not exist"). Fixed in `596a0d5`: all nine components the trees use
   now ship as recipe config (the byte/haven site-template pattern; byte
   ships 41 such files).
3. **Mercury 1.0.5 added a required `overlay_opacity` prop to `cta`.**
   Canvas validates tree items against BOTH the shipped component config
   and the live SDC schema, and discovery does not refresh imported
   configs before content import. Fixed in `596a0d5`: the prop is defined
   in the shipped cta config (default `20%` per the SDC) and set (`0%`,
   visually inert without a background image) on all four CTA tree items;
   `drupal/mercury` floor raised to `^1.0.5`. **Maintainer rule:** when
   the mercury floor moves, re-export the shipped component configs and
   re-validate the trees.

**Final matrix (all fixes applied, canvas 1.5.0 + mercury 1.0.5):**

| Check | Result |
|---|---|
| `drush site:install recipes/geo_starter` | exit 0, zero error lines |
| Entities | 22 nodes (21 recipe + privacy page), 12/12 paragraphs, 4/4 canvas_pages |
| Card-grid ¶`46…0031` references | 2/2 (silent-drop class eliminated) |
| JSON-LD probe (`tools/jsonld-probe.php`) | 23/23 |
| `/` | 200, `path-frontpage`, renders C-01 |
| Service page | one `application/ld+json` block; `Service` + `FAQPage` present |

The fixes postdate the pushed-but-unreleased `1.0.0-beta1` tag; they ship
as `1.0.0-beta2` (release node held until WS-D Phase 2 runs against the
public demo). Module `geo_starter_jsonld 1.0.0-beta1` was released on
drupal.org earlier the same day and is unaffected.

## Local Helper Scripts

The helper scripts are not part of Drupal runtime behavior and are **not run by the recipe
install**.

- `tools/quickstart.sh` — operator convenience: wraps the verified install
  path (`docs/INSTALL.md`) into one command. It changes nothing about the
  install sequence itself; SQLite default is for local trials only (the
  acceptance proofs ran on MariaDB under DDEV).

  **Live end-to-end run (2026-06-10, pre-`1.0.1`):** `quickstart.sh
  <dir> 1.0.0` on macOS host PHP 8.5.5 / Composer 2.9.7, SQLite default
  path, real network. `composer create-project drupal/cms` (2.1.3, core
  11.3.11, canvas 1.5.1), the 11-package root `require` set, and the
  drupalcode tag clone all resolved; `drush site:install` completed
  (`Installation complete`); cron ran; one-time login link issued; demo
  content probe on the installed site returned 4 Services / 8 Answers /
  3 Articles / 6 Evidence Sources / 4 Canvas pages. The first run attempt
  **failed real**: the stock 128M PHP CLI `memory_limit` OOMs during the
  Canvas module install step — fixed by running drush through its PHP
  front controller (`vendor/drush/drush/drush.php`) at `PHP_MEMORY_LIMIT`
  (default 512M). (`php vendor/bin/drush` is wrong — that file is a shell
  proxy, and PHP prints it instead of executing drush, exiting 0: a false
  green.) The run used the `1.0.0` recipe tag; `config/` and `content/`
  are identical in `1.0.1`, so the result carries.
- `tools/content-graph-lint.py` — dev lint: depends-completeness + acyclicity
  of `content/`. Run before any release.

The two PHP scripts below are development-only generators, kept so the demo
content can be regenerated:

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

- A GEO-specific design system (the WS-B semantic-template pass for the ten
  section bundles is done and assertion-gated; the node field-stack above the
  sections and a full visual design pass remain — see `docs/LIMITATIONS.md`)
- Google Rich Results Test **post-fix eligibility re-confirmation** (the
  snippet-mode run happened and found+fixed one violation — see WS-D Phase 1
  above; the positive re-confirm awaits a URL-mode run against a public
  instance). FAQ rich result observed valid/eligible pre-fix.
- Full accessibility release gate (the WS-F spot-check passed on the homepage
  and Service page — keyboard walk, skip-link, focus, WCAG AA contrast on
  WS-B CSS pairs; the admin dashboard keyboard pass and Mercury's own full
  WCAG conformance remain)
- Responsive, performance, and cache release gates
- Manual editor UI/reorder proof for Paragraph sections
- Source-CMS import automation or migration execution
- Marketplace submission readiness

Internal site search is **not shipped** (deferred by decision, WS-F
2026-06-07 — see `docs/LIMITATIONS.md`), so it is out of validation scope.

*(Removed 2026-06-05 as now proven: rendered JSON-LD/schema output —
validated externally above; component-composed Canvas pages — C-01..C-04
shipped and fresh-install gated. Removed 2026-06-09 as now proven: sitemap
behavior — WS-F, config + generate → 26 URLs, drafts excluded.)*
