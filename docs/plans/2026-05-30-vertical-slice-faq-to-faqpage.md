# GEO Starter — Vertical Slice: `section_faq` → JSON-LD `FAQPage` (on Service) → Homepage Screenshot

> **Status:** Recommended *first* increment, carved out of the three 2026-05-30 domain plans after the multi-critic + core-philosophy review found them over-scoped. **Revised 2026-05-30 (v2)** after a `proposal-critic` pass on v1 returned REVISE with 2 CRITICAL findings — see "v1→v2 changes" below.
> **Drupal:** Drupal CMS / Drupal 11. Stack: Paragraphs, JSON:API, content_moderation, Olivero, Canvas.
> **Parent plans (authoritative detail lives there):**
> - `2026-05-30-paragraph-component-library-content-model-plan.md` (§4.1 `section_faq`, §6 gate)
> - `2026-05-30-jsonld-structured-data-emission-drupal-plan.md` (§2.1 Service, §2.4 EvidenceSource, §3 gates+guard #5, §5 settings, §6 shipping)
> - `2026-05-30-canvas-sample-pages-plan.md` (Phase 0, C-01)

## v1→v2 changes (what the proposal-critic caught)

- **C1 (no-op marquee):** v1 targeted **Answer**, but `faqpage_on_answer` defaults **`false`** (`jsonld plan §5`). An Answer slice would ship a valid graph with **no FAQPage** — the marquee output silently empty. **Fix: re-target to `service`**, where `faqpage_on_service` defaults `true`.
- **C2 (dangling citation @ids):** v1 emitted `citation` `@id`s to evidence_source pages but deferred the EvidenceSource normalizer, so those `@id`s resolved to JSON-LD-less pages — a provenance break in a provenance-positioned product, invisible to validators (they don't follow cross-page `@id`s). **Fix: pull EvidenceSourceNormalizer into the slice.**
- **M1 (target undercut the demo):** Service is the JSON-LD plan's first normalizer (T1), and the C-01 homepage CTA lands on a Service page — so Service-targeting makes the demo flow surface live JSON-LD. Confirmed in config: `service` has both `field_direct_answer` and `field_sections`.
- **M2 (STOP-gate last):** Canvas Phase 0 is a hard STOP gate with no dependency on Steps 1–2. **Fix: Phase 0 runs FIRST.**
- **M3 (blind DoD):** added FAQPage-*presence* and citation-*@id-resolution* assertions — green validation alone is blind to C1 and C2.
- **Content gap:** no sample content carries a `section_faq` (it doesn't exist yet). Added an explicit content-authoring step.

---

## Why this slice (the rationale the reviews forced)

The three plans together are **~3 releases of work**. The skeptic lens named the most likely failure: *the maintainer ships the paragraph library, runs out of energy, and the JSON-LD package — the actual point — never ships.*

This slice inverts that risk. It is the **smallest CLOSED cross-plan contract** (not merely the fewest deliverables — v1's mistake) that produces three durable wins:

1. **A marquee GEO artifact** — a real, non-empty `FAQPage` emitted from governed, reviewed content on a Service page.
2. **A Marketplace artifact** — the C-01 homepage screenshot + demo URL (`LIMITATIONS.md` Marketplace blocker), whose CTA lands on the very Service page that now emits live JSON-LD.
3. **An end-to-end proof of the Paragraph→JSON-LD contract** — `section_faq`'s `field_section_items` traversal, the content-based gate, `@graph` aggregation, the `$view_mode === 'full'` guard, **and cross-node `citation` `@id` resolution** all working on one pattern. The remaining bundles/types are then repetition, not risk.

---

## Target: ONE node type — `service`

`service` (not `answer`) because, all verified in `config/`:
- `service` has `field_sections` (`field.field.node.service.field_sections.yml`) — host for `section_faq`.
- `service` has `field_direct_answer` and `field_evidence_sources` (renders in `core.entity_view_display.node.service.default.yml`).
- `faqpage_on_service` defaults **`true`** (`jsonld plan §5`) — the FAQPage actually emits (Answer would not).
- ServiceNormalizer is the JSON-LD plan's **first** Phase-A task (T1).
- The C-01 homepage CTA targets the emergency-assistance Service — actual alias **`/apply-emergency-food-and-utility-assistance`** (verified in `content/node/41000000-0000-4000-8000-000000000001.yml:43` — note: no `/services/` prefix, no `-for-`) — so the demo surfaces the slice's live JSON-LD instead of leading to an empty page.

Note: on Service, `field_direct_answer` renders visibly but has no faithful schema.org property on `Service` — it is omitted from JSON-LD (parity-safe: less ≤ visible, never more), same treatment as `field_problem_solved`/`field_eligibility` (jsonld plan §2.1). The Service marquee is the `Service` object + `WebPage` + gated `FAQPage` + resolvable `citation`s, not a `Question`/`acceptedAnswer`.

---

## Steps (corrected order: Phase 0 gate → 1 → 2 → 3)

### Step 0 — Canvas Phase 0 (HARD GATE, runs first; M2)

From Canvas plan §"Phase 0". No dependency on Steps 1–2 — front-load it so the riskiest unknown is resolved before building.
- Verify Canvas 1.4.1 stock container inventory (hero/section/card-grid); prove the author→export-YAML→re-import loop round-trips.
- **If containers missing →** flat single-column fallback. **If the loop doesn't round-trip →** STOP, escalate; the C-01 deliverable falls back to screenshotting the working Service page (and that must be stated in `LIMITATIONS.md`).
- Steps 1–2 may proceed in parallel with Step 0; the only hard build dependency is Step 1 → Step 2 Phase-B.

### Step 1 — Paragraph: `section_faq` + `section_faq_item`, enabled on `service` only

From paragraph plan §4.1 / §6 (revised, content-based gate). Ship as recipe `config/` YAML.
- **`section_faq_item`** (child): `field_section_question` (string, req), `field_section_answer` (text_long, req, CKEditor5). Never placed directly in `field_sections`.
- **`section_faq`** (parent): `field_section_heading` (string, opt — reuse existing storage), `field_section_items` (entity_reference_revisions → `section_faq_item`, -1, req).
- **Reuse, do not mint:** `field_section_heading` already exists (one of the three existing `field_section_*` storages — heading/body/**kicker**, per the C1 correction in the paragraph plan).
- **Enablement:** add `section_faq` to `field.field.node.service.field_sections.yml` `target_bundles` + `target_bundles_drag_drop` **only** (leave answer/article untouched in this slice — do NOT mirror the parent plan §7 which enables all three).
- **Mandatory displays** for both bundles, or the render check is meaningless.

**Acceptance:** recipe applies clean; `php:eval` confirms both `paragraphs_type`s + all fields; create a `section_faq` with 2 valid items, attach to a published Service node, save; Q&A renders in HTML; `field_sections` rejects `section_faq_item` directly; JSON:API published→200 / draft→403 on the parent Service.

### Step 1b — Sample content (the gap v1 missed)

No existing content carries a `section_faq`. **Decision (ratified):** extend the existing published Service node at alias `/apply-emergency-food-and-utility-assistance` (UUID `41000000-0000-4000-8000-000000000001`, the homepage-CTA target) — not a new node.

**SINGLE SOURCE OF TRUTH = the recipe's `content/` directory, NOT the PHP script (NEW-C2).** The bundled `content/` is what `drush recipe apply` imports (SESSION_HANDOFF confirms 22 nodes import on a real install). `tools/create-alpha-sample-content.php` is a **destructive dev-only relic** — it calls `deleteExistingNodes(['service','answer','article','evidence_source'])` (`:280`) and re-creates nodes from its own arrays, so any edit to `content/*.yml` would be wiped if the script runs, and INSTALL.md was already corrected to stop recommending it. **Do NOT edit or run that script for this slice.** Author entirely in `content/`:
- add child paragraph entities (`content/paragraph/…` for ≥2 `section_faq_item`) and the parent `section_faq` paragraph, then reference the parent from the Service node's `field_sections`;
- the FAQ must have **≥2** `section_faq_item` children, **each with a non-empty question AND a non-empty answer** (the content-based gate counts only items whose stripped question and answer are both non-empty);
- ensure the Service node has **≥1** `field_evidence_sources` reference to a *published* evidence_source node (so `citation` has a real, resolvable target);
- declare every `depends:` (the paragraph entities, the evidence_source node, any terms) or the content import aborts (VALIDATION.md `depends` lesson);
- **text format:** the `field_section_answer` value must use **`content_format`** (the format the existing sample content uses — 44× across `content/`; Drupal CMS has NO `basic_html` format — available are `content_format`, `canvas_html_block`, `canvas_html_inline`, `plain_text`). Verified empirically during Step 1 acceptance.

> **Paragraph-serialization mechanism (empirically confirmed 2026-05-30 — Step 1b DONE).** Core's DefaultContent **exporter** (`drush content:export --with-dependencies`) **silently drops paragraphs** — exports 0 paragraph files and writes the field as a non-portable raw `target_id`. So author-in-UI→export does NOT work for paragraphs. The **importer** DOES import **hand-authored** `content/paragraph/<uuid>.yml` files referenced via `field_sections: [{entity: <uuid>}]` + `depends: {<uuid>: paragraph}`, including nested parent→child paragraphs. **Validated on a fresh `site:install`** (field_sections populated, parent + 2 children created, Q&A renders). Shipped files: `content/paragraph/46000000-…0001.yml` (section_faq) + `…0011`/`…0012` (items) + the `field_sections`/`depends` edit on `content/node/41000000-…001.yml`. **Do NOT regenerate via `content:export`** — hand-edit.

### Step 2 — JSON-LD module `drupal/geo_starter_jsonld` (Service + EvidenceSource normalizers + FAQPage contributor)

From JSON-LD plan §2.1 / §2.4 / §3 / §5 / §6. **Step 2 Phase-B half hard-depends on Step 1.**
- **Shipping (M2 from the larger review — non-negotiable):** own composer package `drupal/geo_starter_jsonld`, added to `drupal/geo_starter` `composer.json` `require`, listed in `recipe.yml install:`. A recipe cannot discover a bundled `modules/` dir.
- **ServiceNormalizer (Phase A, JSON-LD T1):** `Service` + `WebPage`, `description` ← `field_summary`, `potentialAction` ← `field_next_action`, `dateModified` ← `field_reviewed_date`, `citation` ← `field_evidence_sources` (published targets only).
- **EvidenceSourceNormalizer (Phase A, JSON-LD T3 — PULLED IN to close C2):** `CreativeWork` at the evidence node's `@id`, `url`/`sameAs` ← `field_source_url`, `publisher` ← `field_publisher`. This is what makes the Service's `citation` `@id`s **resolve** instead of dangle. Small — two fields.
- **FaqContributor (Phase B, needs Step 1):** page-level `FAQPage`, `mainEntity` aggregated from `field_section_items`, **content-based gate** (≥2 children with non-empty *stripped* question AND answer; published). **Ship `faqpage_on_service: true`** in `config/install/geo_starter_jsonld.settings.yml` (it's the default, but state it — this is the C1 fix made explicit).
- **Universal guard #5 (the correctness bug):** emit only on `$view_mode === 'full'` + canonical route. Teasers/embeds/search emit nothing.
- **Cache:** `node:{nid}` + each referenced `node:{evidence_nid}` + config tag; `url.path` context. `CacheableMetadata`.
- **Attachment:** `hook_node_view_alter()` → `#attached['html_head']`, single script, stable dedup key.

**Acceptance (two-layer):** recipe applies, module enabled, builder resolves; published Service with a 2-pair `section_faq` + ≥1 evidence source → valid JSON with `Service` + `WebPage` + `FAQPage(mainEntity.length==2)` + `citation` whose `@id` **resolves to a `CreativeWork`** emitted on the evidence_source page; **parity** (every JSON-LD string in visible HTML); **gate negatives** (1-pair → no FAQPage; empty-after-strip answer not counted); **view-mode negative** (Service teaser on a listing emits zero JSON-LD); **draft** → no JSON-LD + JSON:API 403; **citation suppression** on unpublish; external schema.org/Rich-Results validation green **before any public claim** (`SCHEMA_MAP.md` §40).

### Step 3 — Canvas C-01 homepage + screenshot

C-01 composed (Step 0 already proved/constrained the components). Hero "Drupal is the CMS for an age of agents" → value pillars → service/answer card grids → "Every claim is sourced" evidence block → CTA → the Service page from Step 1b (now emitting live JSON-LD + resolvable citations).
- **Demo URL / front page (M4):** `system.site` `page.front` config action (content-before-config ordering) **or** `/geo-starter` alias — decide explicitly; Marketplace blocker.
- **Screenshot:** 1280×900 + 375px. **Fallback** (if Step 0 forced a weak homepage): screenshot the Step-1b Service page (which now shows FAQ + sourced citations) and note in `LIMITATIONS.md`.

**Acceptance:** `php:eval` loads the `canvas_page` with a non-empty tree; front/demo route → 200; rendered HTML contains the headline + the Service title + an Evidence Source title; screenshots captured.

---

## Definition of Done (ALL must hold — strengthened per M3)

1. `section_faq`/`section_faq_item` install via `drush recipe apply` and author cleanly on a published Service; the Step-1b sample Service node ships with a 2-pair FAQ + ≥1 evidence source.
2. `drupal/geo_starter_jsonld` is a composer package the recipe requires; **assert both edits exist** — the package is in `drupal/geo_starter`'s `composer.json` `require` AND `geo_starter_jsonld` is in `recipe.yml` `install:` — and the module is enabled after `drush recipe apply`.
3. The Step-1b Service page emits a valid `@graph`, **AND** (3a — presence, catches C1) the validator output **contains** a `FAQPage` with `mainEntity.length == 2`, **AND** (3b — resolution, catches C2) every emitted `citation.@id` resolves to a page emitting a `CreativeWork` at that `@id`.
4. All gate-negative, view-mode-negative, draft-absence, parity, and citation-suppression tests pass; external structured-data validation green.
5. C-01 homepage (or documented fallback) exists; demo URL resolves 200; **and the CTA href equals the Step-1b node's actual `path.alias` (assert against the loaded node's alias, not a hard-coded string) and that alias returns 200** — catches NEW-C1 alias drift.
6. `docs/LIMITATIONS.md` (the "Rendered JSON-LD has not been implemented or validated" line) and `SCHEMA_MAP.md` (the JSON-LD-deferred row) status updated; `CHANGELOG.md` entry added.

---

## Intentionally NOT in this slice (deferred to parent plans)

- Other paragraph bundles: `section_step_list`, `section_card_grid`, `section_alert`, `section_contact_panel`, `section_cta` (P3), `section_media_text`.
- Other JSON-LD: `Answer` (`Question`/`acceptedAnswer`) and `Article` normalizers (Phase A — next increment, independent), `HowTo`, `ContactPoint`, `ItemList`, `BreadcrumbList`, `GovernmentService`/`NewsArticle` overrides.
- `section_faq` on `answer`/`article` (Service-only here).
- Canvas C-02/C-03/C-04.
- GEO theme/design system, sitemap+search, WCAG/perf/responsive evidence — meta-plan Tiers 3–4 + ops.

> **Note on scope boundary (the v1 lesson):** EvidenceSourceNormalizer is *in* the slice even though it's a separate normalizer, because the slice's own `citation` emission depends on it — the slice de-scopes by **closed contract**, not by deliverable count. Answer/Article normalizers are genuinely independent and stay out.

## Open decisions to ratify before building

1. **`faqpage_on_service`** — confirm shipping `true` (it's the default; the slice relies on it).
2. **Demo URL mechanism** — `system.site` front-page config action vs. `/geo-starter` alias (Step 3 / M4).
3. **Sample-content edit vs. new node** — **RESOLVED:** extend the existing Service at `/apply-emergency-food-and-utility-assistance` (Step 1b) via the recipe `content/` directory only (NOT the destructive PHP script), not a new node — keeps the demo flow coherent and the CTA target emits the live FAQPage.
4. **Canvas 1.4.1 exact pin** — accept for alpha; add "lift pin + re-validate trees" to `RELEASE_CHECKLIST.md`.

## Next steps

- **Build with:** `/drupal-config-executor` (Step 1 config + Step 2 module config/schema); module PHP under TDD; sample content (Step 1b); Canvas authored in-UI then exported (Steps 0/3).
- **Review with:** `content-model-critic` (Step 1), `drupal-critic` (Step 2 — guard #5, cache tags, recipe-boundary, citation @id resolution), `drupal-critic` Canvas skills + `a11y-critic`/`ui-critic` on C-01.
- **Before any public GEO claim:** external schema.org/Rich-Results validation + copy/proposal review (`SCHEMA_MAP.md` §40).
