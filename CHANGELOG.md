# Changelog

## 1.0.0-beta2 - 2026-06-08

Supersedes the `1.0.0-beta1` tag, which was pushed but never released on
drupal.org: the released-artifact install proof (run before the demo
deployment, `docs/VALIDATION.md` → "Released-Artifact Install Proof") found
three install-breaking defects in it. Do not install the beta1 tag. The
stability contract and feature set described under 1.0.0-beta1 carry
forward unchanged.

- Fixed a default-content dependency cycle introduced by the card-grid
  sample (service → card grid → answers → the same service). Core's recipe
  content importer has no cycle detection: depending on filesystem
  iteration order the install either crashed fatally
  (`entity_reference_revisions` `onChange()` on NULL) or silently dropped
  one of the two card references — every previously green install had been
  importing with one card missing. The cards now point at cycle-free
  answers, and a dev-only lint (`tools/content-graph-lint.py`) guards
  depends completeness + acyclicity going forward.
- Ship `canvas.component.*` config for all nine Mercury components the
  Canvas page trees use (previously only the three chrome components were
  exported). On current Drupal CMS installer stacks, Canvas's
  component-config auto-generation runs after recipe content import — and
  skips `cta`/`section` entirely for Mercury 1.0.5 — so canvas_page import
  failed validation with "config does not exist". Shipping the configs
  (the byte/haven site-template pattern) makes them exist
  deterministically at import time.
- Mercury 1.0.5 compatibility: its new required `overlay_opacity` CTA prop
  is set on every CTA tree item and defined in the shipped cta component
  config; the `drupal/mercury` floor rises to `^1.0.5` (the committed
  trees and shipped component configs encode 1.0.5 prop schemas).
- Re-validated end-to-end the way real users install: fresh
  `composer create-project drupal/cms` with `drupal/geo_starter_jsonld
  1.0.0-beta1` resolved from drupal.org (`^1.0@beta` under a stable
  floor; plain `^1.0` is refused), canvas 1.5.0, mercury 1.0.5 — clean
  install, JSON-LD probe 23/23, all four Canvas pages and the front page
  render, Service page emits `Service` + `FAQPage` JSON-LD.

## 1.0.0-beta1 - 2026-06-07 (tagged, not released — superseded by beta2)

First beta. Enters the stability contract (README → "Stability contract"):
fresh-install-only, additive-only content model, breaking changes documented.
**Validated on Drupal CMS 2.1.x / core 11.3.x** via fresh `site:install`
acceptance on DDEV. Beta installs require a `minimum-stability` floor of `beta`
or looser (the recipe requires `drupal/geo_starter_jsonld:^1.0@beta`).

- **Visible section rendering (WS-B):** the recipe now enables the
  `geo_starter_jsonld_markup` submodule (shipped inside the already-required
  `drupal/geo_starter_jsonld` package — one `install:` line, no new require),
  giving the ten section bundles semantic, lightly-styled HTML (open `<dl>`
  FAQ, `<ol>` steps, `<address>` contact, accented alert/CTA, card grid,
  media/text) instead of classless div-soup. Adds a `geo_starter_section`
  sample on the `who-can-apply-emergency-assistance` answer and relabels the
  provenance fields ("Reviewed by" / "Last reviewed" / "Author").
- **Editorial dashboard (WS-C):** a `geo_content` View at `/admin/content/geo`
  (gated on `access content overview`) for the governed content types.
- **XML sitemap (WS-F):** adds `drupal/simple_sitemap` indexing the four
  canonical node types + Canvas pages, for crawler/agent discoverability.
  Populates on first cron (recipes cannot generate); unpublished excluded.
- **JSON-LD companion → `1.0.0-beta1`:** the recipe requires the module's
  first beta, which drops an invalid rich-result `Review` object (see the
  module CHANGELOG). No recipe content-model change from it.
- Re-validated on a fresh install: 21/21 rendering assertions, JSON:API
  published-200/draft-403 matrix across nodes + Canvas pages + paragraphs,
  JSON-LD probe 23/23, validator.schema.org 0/0 and offline domain check 0/0
  on all four node types, a11y keyboard + AA-contrast spot-check
  (`docs/VALIDATION.md`).
- **Security-advisory posture:** as a Community project the recipe is not yet
  covered by the Drupal Security Team; treat betas as fresh-install previews.

## 1.0.0-alpha4 - 2026-06-02

- Replaced the contact panel's free-text `field_section_hours` with a structured
  `drupal/office_hours` field (a new recipe dependency), so `geo_starter_jsonld`
  emits schema.org `hoursAvailable` (`OpeningHoursSpecification`) on the Service's
  `ContactPoint`. The sample contact panel now ships Mon–Fri 09:00–17:00.
  **Fresh installs only:** the string-to-`office_hours` storage change is not an
  in-place migration; existing `1.0.0-alpha2`/`1.0.0-alpha3` installs that
  re-apply the recipe must drop and recreate `field_section_hours`.
- Added the `section_step_list` / `section_step_item` paragraph bundle with sample
  step content (the source for the JSON-LD `HowTo`).
- Added the `section_card_grid` paragraph bundle (source for the JSON-LD
  `ItemList`) and the `section_contact_panel` bundle (source for the provider
  `ContactPoint` + `PostalAddress`), with sample content.
- Added the `section_cta`, `section_alert`, and `section_media_text` authoring
  paragraph bundles (visual page sections; JSON-LD-silent).
- Validated on a fresh install: all ten paragraph bundles import with their
  fields and the companion module's acceptance probe (`tools/jsonld-probe.php`)
  passes 23/23.

## 1.0.0-alpha3 - 2026-05-30

- Added the `drupal/geo_starter_jsonld` companion module (new required dependency
  of the recipe) emitting parity-correct schema.org JSON-LD on full canonical node
  pages: `Service` + `WebPage`, `CreativeWork` for Evidence Sources, and a gated
  `FAQPage` from `section_faq` (≥2 valid Q&A pairs). Cross-node `citation` `@id`s
  resolve to the Evidence Source's `CreativeWork`; unpublished sources are
  suppressed. Emits only on the full canonical view of a published node, with full
  cache metadata (node + referenced-entity tags, `url.path` context). Validated on
  a fresh install (the module's `tools/jsonld-probe.php`, 11/11). Answer/Article/HowTo and
  external schema.org validation remain to follow.
- Added a Service-only `section_faq` Paragraph slice with nested `section_faq_item`
  question/answer items and bundled sample FAQ content on the emergency assistance
  Service page. This is the first closed Paragraph-to-JSON-LD preparation step.

## 1.0.0-alpha2 - 2026-05-29

- **Breaking (taxonomy correction).** Fixed an inverted taxonomy: the `topic` vocabulary
  previously held page-aspect terms (Eligibility, Costs, Deadlines, …) while the actual
  subject domains lived in a separate `service_area` vocabulary — so a retrieval query for
  "topics" returned page sections, not subjects. The subject terms (Benefits and assistance,
  Permits and records, Community programs, Housing and utilities) now live in `topic`; the
  page-aspect terms, the `service_area` vocabulary, `field_service_area`, and its field
  storage are removed. `field_topic` is now the single subject axis — required on Service
  and Answer, optional on Article (editorial/cross-cutting pieces) and Evidence Source.
  Taxonomy reference widgets no longer allow inline term creation.
- This **supersedes and corrects** the earlier note "Restored Service area configuration
  consistency," which described the pre-correction (still-inverted) model and was inaccurate.
- Fixed a stale content dependency graph left by the taxonomy correction. Nine demo nodes
  (eight Answers and one Article) referenced the new `topic` subject terms in `field_topic`
  but did not list those terms in their `depends` blocks. Because `field_topic` is required
  on Service and Answer, recipe content import aborted with
  `field_topic=This value should not be null`. The missing term dependencies were added, so
  the recipe now applies cleanly.
- The bundled `content/` directory is the canonical demo content imported on install. The
  `tools/` scripts are development-only generators that delete and re-create demo nodes;
  they are not run by `drush recipe`/`site:install`. `docs/INSTALL.md` no longer tells end
  users to run them, and the access-probe script's reference to a removed page-aspect term
  was corrected.
- Existing `1.0.0-alpha1` installs need a manual taxonomy migration; no automated upgrade
  path ships. The corrected model is for fresh installs.
- **Validated on a fresh install (2026-05-29).** Recipe apply succeeds with `field_topic`
  required, the `topic` vocabulary returns the four subject terms, and anonymous JSON:API
  returns `200` for published content and `403` for drafts. See `docs/VALIDATION.md`.
- Updated Drupal.org project-page copy around the narrative "Drupal is the CMS for an age of agents."

## 1.0.0-alpha1 - 2026-05-29

- Initial Community alpha for `drupal/geo_starter`.
- Added Drupal CMS site-template recipe shape with `type: Site`.
- Added MVP Service, Answer, Article, Evidence Source, Audience, Topic, and Service area content model configuration.
- Added sample public-service content for install and JSON:API access proof.
- Added Canvas as the visual-page authoring lane with a Canvas Page shell.
- Added Paragraphs and Entity Reference Revisions with `geo_starter_section` attached to Service, Answer, and Article nodes.
- Added Olivero as the basic public frontend theme for alpha rendering.
- Added clean install, rendered-page, YAML parsing, and JSON:API published/draft access validation evidence.
- Added alpha documentation for installation, limitations, dependencies, content licenses, migration mapping, schema boundaries, support, and security reporting.

## Earlier Alpha Scaffold Work

- Reframed the starter as a governed, GEO-friendly migration foundation for teams moving from headless/composable or legacy page/post CMS stacks.
- Added required package constraints for Canvas, Paragraphs, and Entity Reference Revisions.
- Updated the recipe install list for the required dual-authoring dependency posture.
- Clarified validation gaps after the dual-authoring dependency expansion.
