# GEO Starter Specialized Paragraph Component Library — Content Model Plan

> **For Claude:** Use drupal-content-model-planner protocol. Invoke content-model-critic at review checkpoint. Hand the schema.org mapping section to the downstream JSON-LD/structured-data planner.
> **Drupal Version:** Drupal CMS (Drupal 11 core)
> **Composition Pattern:** Paragraphs (entity_reference_revisions) enriching structured nodes. Canvas is a separate, non-mixed lane — out of scope here.

**Scope:** Design the specialized Paragraph bundle library that `docs/AUTHORING_MODEL.md` documents as "future" but unbuilt — step list, FAQ/accordion, card grid, CTA, contact/action panel, alert/callout, media/text — implementable as recipe config and prioritized by GEO/authoring value.
**Complexity:** High (cross-planner schema.org deliverable, nested-paragraph patterns, per-node-type enablement governance, parity-avoidance discipline against existing node fields).

> **Review revisions (2026-05-30, post content-model + proposal-critic review):**
> - **C1 (wrong baseline):** §1 corrected — the existing `geo_starter_section` paragraph has **three** fields, not two; `field_section_kicker` (string, optional) was missing. Added here and to the §4 field-storage reuse note as a third shareable storage. Confirmed in `config/field.field.paragraph.geo_starter_section.field_section_kicker.yml`.
> - **C2 / M1 (schema gates were presence-based):** §6 FAQ/HowTo gates rewritten to be **content-based** (≥2 items with non-empty *stripped* question/answer or step name/text), not child-count-based — a `<p>&nbsp;</p>` answer satisfies `required` but yields an empty `acceptedAnswer`, which would emit a parity-violating claim. §6 also now states the **parent traversal reference fields** (`field_section_items`, `field_section_steps`) the downstream JSON-LD planner must read.
> - **M3 (re-rank):** `section_cta` moved from P2 to **P3** — it emits no schema.org type and carries the highest parity-collision risk with `field_next_action`. Its existence still depends on the §4.4 scope rule.

---

## 1. Grounding: Verified Current State

Read from the repo (not assumed):

**Existing Paragraph type** — `paragraphs.paragraphs_type.geo_starter_section` (THREE fields — verified against `config/`):
- `field_section_heading` — `string`, cardinality 1, **required**.
- `field_section_body` — `text_long`, cardinality 1, optional.
- `field_section_kicker` — `string`, cardinality 1, optional (eyebrow/label above the heading). *Was missing from the prior draft (C1) — confirmed in `config/field.field.paragraph.geo_starter_section.field_section_kicker.yml` + `field.storage.paragraph.field_section_kicker.yml`.*
- Established field naming convention: **`field_section_*`** prefix on paragraph fields.
- **Three** shareable storages already exist for reuse: `field_section_heading`, `field_section_body`, `field_section_kicker`. New bundles should reuse `field_section_kicker` where an eyebrow/label fits (e.g., `section_cta`, `section_alert`) rather than minting a new storage.

**The composition field** — `field.storage.node.field_sections`:
- `entity_reference_revisions`, target_type `paragraph`, cardinality **-1** (unlimited), translatable.
- Field instances on **service**, **answer**, **article** only (`field.field.node.{service,answer,article}.field_sections.yml`).
- Each instance currently allows **only** `geo_starter_section` in both `handler_settings.target_bundles` and `target_bundles_drag_drop`.

**Node fields that are the source of truth** (must NOT be duplicated by paragraphs):

| Field | Type | Cardinality | Meaning |
| --- | --- | --- | --- |
| `field_direct_answer` | text_long | 1 | The canonical direct answer (Answer/Service) |
| `field_summary` | text_long | 1 | Page summary / teaser |
| `field_evidence_sources` | entity_reference → node | -1 | Citations to Evidence Source nodes |
| `field_next_action` | link | 1 | The page's single canonical primary action |
| `field_reviewed_date` | datetime (date) | 1 | Provenance: last reviewed |
| `field_topic` | entity_reference → taxonomy_term | -1 | Topic classification |
| `field_audience` | entity_reference → taxonomy_term | -1 | Audience classification |

**Node types:** service, answer, article, page, evidence_source. Only service/answer/article carry `field_sections` today.

**Editorial workflow** — `workflows.workflow.geo_starter_editorial`:
- States: `draft`, `needs_review`, `published`, `archived` (default `draft`).
- Transitions: send_to_review, publish, archive, create_new_draft.
- Applies to **node** entity type only (service, answer, article, evidence_source). **Paragraphs are NOT independently moderated** — they inherit the access and revision state of the parent node. This is the basis for the JSON:API 200/403 acceptance probe (probe the parent node, not the paragraph).

**Installed modules supporting this plan:** paragraphs, entity_reference_revisions, field, text, link, image, media, media_library, datetime, options, jsonapi, content_moderation, workflows. No new module dependencies required.

---

## 2. Design Principles (the discipline that keeps this from becoming entity sprawl)

1. **Paragraphs enrich; node fields own.** No paragraph may re-store a direct answer, summary, evidence-source list, reviewed date, or the page's primary next action. Those live on the node. Every bundle below is checked against this rule.
2. **Adding a bundle is not free.** Each new Paragraph type requires editing **all three** `field.field.node.*.field_sections.yml` instances to add the bundle to `target_bundles` and `target_bundles_drag_drop`. Per-node-type enablement is a deliberate editorial decision, justified per bundle below — never "all bundles on all node types."
3. **Justify or kill.** The brief forbids inventing a paragraph per conceivable pattern. Decisions made explicitly: alert+callout collapsed into one bundle; `section_media_text` deferred with rationale; `geo_starter_section` kept as a documented generic fallback.
4. **Schema.org parity rule (from SCHEMA_MAP.md):** structured data must match visible rendered HTML. The schema mapping in §6 is a forward-looking spec for the downstream JSON-LD planner — **not** a recipe requirement to emit now. JSON-LD is deferred until visible-HTML parity is validated.
5. **Naming:** continue `section_*` for bundle machine names (matches `section_cta`/`section_card_grid` already named in AUTHORING_MODEL) and `field_section_*` for fields (matches existing `field_section_heading`/`field_section_body`).

---

## 3. Bundle Inventory & Existence Justification

| Bundle | Exists because | Kill test result |
| --- | --- | --- |
| `geo_starter_section` (existing) | Generic untyped section for content that fits no specialized bundle. **Keep**, document as fallback. | Survives — removing it would strand the alpha's proof content. |
| `section_faq` | FAQ/accordion is the highest-GEO pattern; maps to schema.org Question/acceptedAnswer. Distinct from `field_direct_answer` (which is ONE canonical answer, not a Q/A set). | Survives — no node field stores multiple Q/A pairs. |
| `section_step_list` | Procedural how-to content; maps to schema.org HowTo/HowToStep. No node field models ordered steps. | Survives. |
| `section_card_grid` | Named in AUTHORING_MODEL. Curated related-content grid distinct from `field_topic`/`field_evidence_sources` (which are classification/citation, not editorial curation). | Survives — but see §4 cardinality cap to keep it from becoming a CMS-in-a-field. |
| `section_cta` | Named in AUTHORING_MODEL. Contextual mid-page calls-to-action, **scoped against `field_next_action`** (see §5). | Survives only with the scope rule. Without it, it is parity with `field_next_action` and should be killed. |
| `section_alert` | Alert/callout merged into one bundle with a `severity` option. | Survives as a merged bundle; two separate alert+callout bundles would NOT survive. |
| `section_contact_panel` | Contact/action panel for office hours, phone, address — maps to schema.org ContactPoint. No node field models this. | Survives. |
| `section_media_text` | **DEFERRED, not killed.** `field_section_body` (text_long) + a media reference would cover most cases; `geo_starter_section` plus inline media in the body covers the rest. Build only if editor testing shows a real need for a structured media+text layout primitive. | Does not clearly survive now — defer to P3 contingent on demand evidence. |

**Net result:** 6 new specialized bundles recommended (1 of which, `section_media_text`, is conditional), plus the retained generic `geo_starter_section`. Two patterns from the vocabulary table (alert + callout) collapse into one bundle.

---

## 4. Per-Bundle Field Specifications

All paragraph fields use the `field_section_*` naming convention. Cardinality, required, and widget are specified for each. Nested-paragraph bundles use child Paragraph types (the standard Drupal pattern) rather than parallel multi-value fields, because the children carry multiple correlated values (a question AND its answer) that must stay aligned.

### 4.1 `section_faq` (FAQ / Accordion) — nested
Parent bundle holds an optional intro and an unlimited list of Q/A item paragraphs.

| Field | Type | Cardinality | Required | Widget |
| --- | --- | --- | --- | --- |
| `field_section_heading` | string | 1 | No | textfield |
| `field_section_items` | entity_reference_revisions → paragraph (`section_faq_item`) | -1 | Yes | paragraphs |

Child bundle **`section_faq_item`**:

| Field | Type | Cardinality | Required | Widget |
| --- | --- | --- | --- | --- |
| `field_section_question` | string | 1 | Yes | textfield |
| `field_section_answer` | text_long | 1 | Yes | text_textarea (CKEditor5) |

**Decision (nested vs flat):** nested wins — a Q and its A are a correlated pair; parallel multi-value `field_question[]` + `field_answer[]` fields can desynchronize and break the schema.org pairing. The `section_faq_item` child is never placed directly in `field_sections`; it is reachable only inside `section_faq`.

### 4.2 `section_step_list` (Step List / How-To) — nested
| Field | Type | Cardinality | Required | Widget |
| --- | --- | --- | --- | --- |
| `field_section_heading` | string | 1 | No | textfield |
| `field_section_steps` | entity_reference_revisions → paragraph (`section_step_item`) | -1 | Yes | paragraphs |

Child bundle **`section_step_item`**:

| Field | Type | Cardinality | Required | Widget |
| --- | --- | --- | --- | --- |
| `field_section_step_name` | string | 1 | Yes | textfield |
| `field_section_step_text` | text_long | 1 | Yes | text_textarea (CKEditor5) |
| `field_section_step_image` | entity_reference → media (image) | 1 | No | media_library_widget |

Step order = paragraph delta order (drag-and-drop), not a stored integer — avoids renumbering bugs. Schema.org HowToStep `position` is derived from delta at render time.

### 4.3 `section_card_grid` (Curated related content) — reference
| Field | Type | Cardinality | Required | Widget |
| --- | --- | --- | --- | --- |
| `field_section_heading` | string | 1 | No | textfield |
| `field_section_cards` | entity_reference → node | **6 (capped)** | Yes | entity_reference_autocomplete |

**Target bundles:** service, answer, article (the public canonical content types). **Excludes** evidence_source (sources are cited via `field_evidence_sources`, not displayed as cards) and page. **Cardinality capped at 6** to keep this a curation primitive, not a hand-rolled listing engine — large dynamic lists belong in Views, not Paragraphs.

### 4.4 `section_cta` (Contextual call-to-action) — scoped
| Field | Type | Cardinality | Required | Widget |
| --- | --- | --- | --- | --- |
| `field_section_heading` | string | 1 | No | textfield |
| `field_section_body` | text_long | 1 | No | text_textarea (reuses existing storage if shareable; otherwise new instance) |
| `field_section_link` | link | 1 | Yes | link_default |

**Scope rule (the reason this bundle is allowed to exist):** `field_next_action` is the node's **single canonical primary action** (rendered prominently, e.g., page header/footer CTA). `section_cta` is for **secondary, contextual, mid-body** actions tied to a specific section's argument. Editorial guidance must state: *do not duplicate the primary action as a `section_cta`.* If an author finds themselves restating `field_next_action`, they are misusing the bundle.

### 4.5 `section_alert` (Alert / Callout — merged)
| Field | Type | Cardinality | Required | Widget |
| --- | --- | --- | --- | --- |
| `field_section_heading` | string | 1 | No | textfield |
| `field_section_body` | text_long | 1 | Yes | text_textarea (CKEditor5) |
| `field_section_severity` | list_string (`info`, `success`, `warning`, `critical`) | 1 | Yes | options_select |

One bundle with a severity option replaces separate alert/callout bundles. Visual treatment is theme-driven off `field_section_severity`.

### 4.6 `section_contact_panel` (Contact / action panel)
| Field | Type | Cardinality | Required | Widget |
| --- | --- | --- | --- | --- |
| `field_section_heading` | string | 1 | No | textfield |
| `field_section_contact_name` | string | 1 | No | textfield |
| `field_section_phone` | telephone | 1 | No | telephone_default (core `telephone` — confirm enabled; else `string`) |
| `field_section_email` | email | 1 | No | email_default |
| `field_section_address` | text_long | 1 | No | text_textarea |
| `field_section_hours` | string | 1 | No | textfield |
| `field_section_link` | link | 1 | No | link_default |

Note: core `telephone` module is not in the recipe `install` list. **Decision:** either add `telephone` to recipe install or use `string` for the phone field. Recommend `string` to avoid a new dependency in alpha; revisit if validation is needed.

### 4.7 `section_media_text` (DEFERRED — P3, conditional)
If built: `field_section_heading` (string, 1, optional), `field_section_body` (text_long, 1, required), `field_section_media` (entity_reference → media image/document, 1, required), `field_section_media_position` (list_string `left`/`right`, 1, required). **Do not build until editor UAT demonstrates `geo_starter_section` + inline body media is insufficient.**

### Field-storage reuse note
`field_section_heading`, `field_section_body`, **and `field_section_kicker`** storages already exist for `geo_starter_section` (three, not two — C1 correction). Drupal field storage on the `paragraph` entity type is shared across paragraph bundles, so new bundles **reuse** these storages (only new `field.field.paragraph.<bundle>.field_section_*.yml` instance configs are needed). `field_section_kicker` is available to any bundle wanting an eyebrow/label. New storages required only for genuinely new fields: `field_section_items`, `field_section_steps`, `field_section_cards`, `field_section_link`, `field_section_severity`, `field_section_question`, `field_section_answer`, `field_section_step_name`, `field_section_step_text`, `field_section_step_image`, and the contact-panel fields. This is the single biggest duplication-avoidance lever in the plan.

---

## 5. Reuse vs. Duplication Ledger (against existing node fields)

| Proposed paragraph field | Collides with node field? | Resolution |
| --- | --- | --- |
| `section_faq` Q/A items | `field_direct_answer` (1 canonical answer) | No collision — FAQ is a multi-item set, not the canonical answer. Editorial rule: the canonical answer stays in `field_direct_answer`; FAQ is supplementary. |
| `section_cta.field_section_link` | `field_next_action` (1 primary action) | **Managed collision** — scope rule in §4.4. Primary = node field; contextual = paragraph. |
| `section_card_grid.field_section_cards` | `field_evidence_sources`, `field_topic` | No collision — cards = editorial curation; evidence = citations; topic = classification. Distinct intents. |
| `section_contact_panel` | none | New capability; no node field models contact data. |
| any `field_section_body` | `field_summary`, `body` | No — summary/body are node-level; section bodies are per-section context. |
| reviewed date | `field_reviewed_date` | No paragraph stores dates — provenance stays node-level. |

---

## 6. SCHEMA.ORG STRUCTURED-DATA MAPPING (first-class cross-planner deliverable)

> **For the downstream JSON-LD/structured-data planner.** This is a forward-looking spec. Per `SCHEMA_MAP.md` and `LIMITATIONS.md`, JSON-LD is **deferred** until visible-HTML parity is validated. Emit nothing until the rendered page shows the content. Structured data must mirror visible HTML exactly — no hidden claims.

| Bundle | schema.org type | Emit condition / gate | Mapping detail |
| --- | --- | --- | --- |
| `section_faq` (parent traversal field: **`field_section_items`** → `section_faq_item` children) | `FAQPage` (page-level) with `mainEntity` = array of `Question`, each with `acceptedAnswer` (`Answer`) | **GATED — content-based, not count-based (C2 correction).** Emit `FAQPage` only when ALL hold: (a) parent is an Answer or Service node; (b) **≥2 `section_faq_item` children where BOTH `field_section_question` AND `field_section_answer`, after strip-to-text + whitespace-collapse, have length > 0** — children whose answer strips to empty (e.g. `<p>&nbsp;</p>`) are NOT counted and NOT emitted, because an empty `acceptedAnswer` is a parity-violating false claim; (c) parent node `published`; (d) the page is not already claiming a conflicting rich-result type. If not eligible → render visible Q/A HTML + `WebPage` only, **no** FAQPage. | Traverse `field_section_items`; per qualifying child: `field_section_question` → `Question.name`; `field_section_answer` (stripped) → `acceptedAnswer.text`. Position from delta order (among qualifying items). |
| `section_step_list` (parent traversal field: **`field_section_steps`** → `section_step_item` children) | `HowTo` with `step` = array of `HowToStep` | **GATED — content-based (C2 correction).** Emit `HowTo` only when (a) **≥2 `section_step_item` children where BOTH `field_section_step_name` AND `field_section_step_text` strip-to-text length > 0** (empty-text steps are not counted, not emitted); (b) steps are genuinely sequential/procedural (not a generic bullet list); (c) parent node `published`. If not eligible → no HowTo, render visible steps + `WebPage`. Note: Google's HowTo rich result is deprecated for *display*, but the markup remains valid structured data for retrieval/agent surfaces — emit for machine-readability, make no rich-result claim. | Traverse `field_section_steps`; per qualifying child: `field_section_step_name` → `HowToStep.name`; `field_section_step_text` → `HowToStep.text`; `field_section_step_image` → `HowToStep.image` (`ImageObject`, only if media renders); position from delta. |
| `section_contact_panel` | `ContactPoint` | Emit nested under the parent page's `Organization`/`Service`/`LocalBusiness` node, not standalone. Only emit fields that are visibly rendered. | `field_section_phone` → `telephone`; `field_section_email` → `email`; `field_section_address` → `PostalAddress`; `field_section_hours` → `openingHours` (best-effort parse, else omit). |
| `section_card_grid` | `ItemList` | Emit **only** when the grid is a meaningful curated list (it always is, given manual curation). Each card → `ListItem` with `url` + `name` of the referenced node. | `field_section_cards` deltas → `ItemList.itemListElement` with `position`. |
| `section_media_text` (if built) | `ImageObject` for the media component only | Emit `ImageObject` for the referenced image; the text carries no distinct type. | `field_section_media` → `ImageObject` (url, caption from alt). |
| `section_cta` | **No distinct type.** | Renders as visible body/link content. Could theoretically map to an `Action` subtype, but this is overkill for a contextual link and risks claim/visible mismatch. **Flag and defer** — do not emit. | — |
| `section_alert` | **No distinct type.** | Renders as visible callout body content under the page's `WebPage`. No schema.org alert type warrants emission. | — |
| `geo_starter_section` (generic) | **No distinct type.** | Renders as body content under `WebPage`. | — |

**Page-level context for the planner:** these section types compose *inside* nodes whose page-level schema is already mapped in `SCHEMA_MAP.md` (Service → `Service`+`WebPage`; Answer → `WebPage`, `FAQPage` only when eligible; Article → `Article`+`WebPage`). A `section_faq` on an Answer node is the natural trigger for the Answer page's gated `FAQPage` — the planner should reconcile section-level and page-level emission so `FAQPage` is declared once at page level with `mainEntity` aggregated from all `section_faq` instances on the page.

---

## 7. Per-Node-Type Enablement Matrix (governance)

Each "yes" = adding the bundle to that node type's `field_sections` instance config (`target_bundles` + `target_bundles_drag_drop`). Deliberate, not default.

| Bundle | service | answer | article | Rationale |
| --- | --- | --- | --- | --- |
| `geo_starter_section` | yes | yes | yes | Existing; generic fallback everywhere. |
| `section_faq` | yes | yes | yes | FAQs fit all three; strongest on Answer. |
| `section_step_list` | yes | yes | yes | How-to fits Service & Answer; Article explainers too. |
| `section_card_grid` | yes | no | yes | Curated related content for Service hubs and Articles. **Off Answer** — an Answer should stay a focused single answer, not a link farm. |
| `section_cta` | yes | yes | yes | Contextual actions relevant on all three. |
| `section_alert` | yes | yes | yes | Notices relevant everywhere. |
| `section_contact_panel` | yes | no | no | Contact/office info is a Service concern. **Off Answer/Article** — they are informational, not service-delivery surfaces. |
| `section_media_text` (if built) | yes | no | yes | Visual explainer layout for Service/Article; Answer stays text-focused. |

Note: `page` and `evidence_source` node types are intentionally NOT given `field_sections` and are out of scope.

---

## 8. Editorial / Governance Considerations

- **Moderation inheritance:** paragraphs have no independent workflow. A `draft` parent node hides all its sections; publishing the node publishes its sections. The JSON:API access probe therefore targets the **parent node**.
- **Translation:** all paragraph fields set `translatable: true` to match existing convention and node fields. Nested child paragraphs are translated within the parent's translation.
- **Editor guidance doc updates required** (AUTHORING_MODEL.md): the `section_cta` vs `field_next_action` scope rule; the "FAQ supplements, does not replace, `field_direct_answer`" rule; the card-grid cap and "use Views for large lists" rule.
- **Nested-paragraph UX:** `section_faq` and `section_step_list` introduce two-level nesting in the editor. UAT must confirm editors can add/reorder child items without confusion (a known Paragraphs friction point).
- **Form/view display config is mandatory, not optional:** every bundle needs `core.entity_form_display.paragraph.<bundle>.default.yml` and `core.entity_view_display.paragraph.<bundle>.default.yml`, or the render acceptance test is meaningless (an unconfigured field does not render). These are listed as deliverables per task in §10.

---

## 9. Prioritized, Sequenced Roadmap

Sequenced by GEO/retrieval value first (the recipe's reason for existing), then authoring breadth.

**Priority 1 — GEO payoff (build first):**
1. `section_faq` + `section_faq_item` — highest retrieval/AI value (Question/acceptedAnswer).
2. `section_step_list` + `section_step_item` — HowTo machine-readability.

**Priority 2 — authoring breadth, modest GEO:**
3. `section_card_grid` — curation + `ItemList`.

**Priority 3 — round out vocabulary, lowest GEO:**
4. `section_alert` — merged alert/callout.
5. `section_contact_panel` — `ContactPoint`.
6. `section_cta` — contextual actions (scoped). **Re-ranked from P2 to P3 (M3):** emits no schema.org type and carries the highest parity-collision risk with `field_next_action`; build it last and only with the §4.4 scope rule documented.
7. `section_media_text` — **conditional**, only on demonstrated UAT need.

---

## 10. Implementation Tasks (recipe config)

Each task ships as flat `config/` YAML applied via `drush recipe apply`. Per task, deliver:
- `paragraphs.paragraphs_type.<bundle>.yml`
- `field.storage.paragraph.<field>.yml` (only for new fields; reuse `field_section_heading`/`field_section_body`)
- `field.field.paragraph.<bundle>.<field>.yml` (instances)
- `core.entity_form_display.paragraph.<bundle>.default.yml`
- `core.entity_view_display.paragraph.<bundle>.default.yml`
- Edits to `field.field.node.{service,answer,article}.field_sections.yml` per the §7 matrix (add bundle to `target_bundles` + `target_bundles_drag_drop`).

### Task 1: `section_faq` (P1)
Includes child `section_faq_item` (type + 2 field instances + displays), parent `section_faq` (type + heading instance + `field_section_items` storage/instance + displays), and node `field_sections` edits (service/answer/article).
**Review checkpoint:** content-model-critic — verify nested-paragraph reference integrity, no duplication of `field_direct_answer`, FAQ child not exposed directly in `field_sections`.

### Task 2: `section_step_list` (P1)
Child `section_step_item` (incl. `field_section_step_image` media reference), parent, displays, node edits.
**Review checkpoint:** content-model-critic — verify delta-order step semantics, media reference uses media_library not legacy image.

### Task 3: `section_card_grid` (P2)
`field_section_cards` storage (entity_reference → node, cardinality 6), instance with target_bundles service/answer/article, displays, node edits (service/article only).
**Review checkpoint:** content-model-critic — verify cardinality cap, evidence_source excluded from targets.

### Task 4: `section_cta` (P3 — re-ranked from P2, M3)
`field_section_link` storage, reuse body/heading/kicker, displays, node edits. **Plus** AUTHORING_MODEL.md scope-rule documentation. Build last: schema-silent and highest parity-collision risk with `field_next_action`.
**Review checkpoint:** content-model-critic — verify scope rule documented, no parity with `field_next_action`.

### Task 5: `section_alert` (P3)
`field_section_severity` list_string storage with allowed_values, displays, node edits.
**Review checkpoint:** content-model-critic — confirm single merged bundle, not alert+callout pair.

### Task 6: `section_contact_panel` (P3)
Contact fields (phone as `string` unless `telephone` added to recipe install), displays, node edits (service only).
**Review checkpoint:** content-model-critic + confirm module dependency decision.

### Task 7: `section_media_text` (P3, conditional) — build only on UAT demand.

---

## 11. Acceptance Checks (per bundle)

Established DDEV pattern: `drush recipe apply` on a throwaway Drupal CMS install, then verify. Apply this block to **each** bundle:

1. **Recipe applies:** `drush recipe apply <path>` exits 0; `drush cr`.
2. **Type exists:** `php:eval` →
   `\Drupal::entityTypeManager()->getStorage('paragraphs_type')->load('<bundle>')` is not null.
3. **Each field exists:** `php:eval` →
   `\Drupal::service('entity_field.manager')->getFieldDefinitions('paragraph','<bundle>')` contains every specified `field_section_*`.
4. **Create + attach + save:** `php:eval` — create a paragraph of `<bundle>` (with child paragraphs for nested bundles), create/load a **published** Service node, set `field_sections` to reference it, save the node.
5. **Render:** load the node's rendered build / request the canonical URL; assert the section's visible field values appear in HTML output (proves form/view display config is correct).
6. **JSON:API published → 200:** `GET /jsonapi/node/service/<uuid>` on the **published** parent returns `200` and the response includes the `field_sections` relationship / embedded paragraph data.
7. **JSON:API draft → 403:** set the parent node to `draft`, `GET` the same endpoint as an anonymous user returns `403` (confirms moderation inheritance — paragraphs are not independently exposed).
8. **Nested bundles (`section_faq`, `section_step_list`):** additionally assert the child item paragraphs (`section_faq_item` / `section_step_item`) render correctly within the parent and that `field_sections` does **not** accept the child bundle directly.
9. **`field_sections` enablement:** `php:eval` — confirm the bundle appears in `target_bundles` for exactly the node types in the §7 matrix and is absent from the others.

Schema.org / JSON-LD validation is explicitly **NOT** part of these checks — deferred until JSON-LD exists, per SCHEMA_MAP.md.

---

## 12. Non-Goals (held)

- No auto-conversion between Canvas and Paragraphs.
- No mixing Canvas and Paragraphs on the same canonical page.
- Canvas props and Paragraph fields are not one storage model.
- No paragraph for every conceivable pattern: alert+callout merged; `section_media_text` deferred; `geo_starter_section` retained rather than multiplied.
- No JSON-LD emission in this work — §6 is a spec for the downstream planner.

---

## 13. Review Checkpoint Plan

| Checkpoint | After Task | content-model-critic / cross-planner focus |
| --- | --- | --- |
| Nested integrity & parity | Task 1, 2 | Child paragraphs reachable only via parent; no node-field duplication. |
| Curation vs classification | Task 3 | Card grid distinct from topic/evidence; cardinality cap honored. |
| CTA scope | Task 4 | Scope rule documented; no `field_next_action` parity. |
| Schema mapping handoff | After §6 finalized | Hand §6 to JSON-LD/structured-data planner; confirm gating conditions (FAQPage/HowTo eligibility) are implementable. |
| Full library review | After Task 6 | Whole-vocabulary coherence; AUTHORING_MODEL.md updated. |

## 14. Next Steps
**Execute with:** `drupal-config-executor` — generate config YAML per §10 tasks.
**Review with:** `content-model-critic` + `drupal-critic` at each checkpoint.
**Hand off:** §6 to the downstream JSON-LD/structured-data planner.
