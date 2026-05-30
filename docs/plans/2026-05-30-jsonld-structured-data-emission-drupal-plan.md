# GEO Starter Rendered JSON-LD / Structured-Data Emission — Drupal Implementation Plan

> **For Claude:** Use drupal-planner protocol. Invoke `drupal-critic` at each checkpoint marked **Review checkpoint**.
> **Drupal Version:** Drupal CMS (Drupal 11 core)
> **Companion skills:** brainstorming, test-driven-development, drupal-critic, drupal-coding-standards, executing-plans
> **Consumes:** `docs/SCHEMA_MAP.md` (node-type → schema.org map + eligibility gates) and `docs/plans/2026-05-30-paragraph-component-library-content-model-plan.md` §6 (paragraph → schema.org map). This plan reconciles section-level and page-level emission into one node-level `@graph`.
>
> **Review revisions (2026-05-30, post multi-critic + core-philosophy review):**
> - **M2 (recipe-module boundary):** purged all "module carried in the recipe / `modules/` directory" language from §1 and the Contract Appendix. §6 was already correct; §1 now matches it — the module ships as its own composer package `drupal/geo_starter_jsonld` that the recipe `require`s. "Zero new dependencies" reframed honestly as "one owned, unpatched package vs. three patch-prone contrib modules."
> - **M1 (Drupal-correctness bug):** added universal guard #5 — emit only on the full canonical node view (`$view_mode === 'full'` + canonical route). Without it, `hook_node_view_alter` would attach JSON-LD on teasers/search/embeds and the last-rendered teaser would win the `html_head` dedup key. Added a "teaser emits nothing" negative test to §7.
> - **C1 (parity reconciliation):** §2.1 now explicitly documents the fate of the visibly-rendered-but-unmapped Service fields `field_problem_solved` and `field_eligibility`, and acknowledges the resulting `Service` object is intentionally thin.
> - **M4 (Article author):** Article has a dedicated `field_author_name` (confirmed in config) — `author` now maps to it; `field_reviewed_by_name` maps to `reviewedBy`/`review`.
> - **M3 (staging):** §8 split into Phase A (T1–T3, ships against current config) and Phase B (T4–T6, HARD-blocked on the Paragraph bundles existing). The "node-level runs in parallel" framing was misleading — the marquee FAQPage/HowTo work is last and gated.

**Feature:** Emit valid, parity-correct schema.org JSON-LD in the `<head>` of rendered node pages (Service, Answer, Article, Evidence Source) so the recipe's governed structured content becomes machine-inspectable by retrieval systems and answer engines — closing the single highest-leverage gap in the alpha.
**Risk Level:** Medium–High. Not data-model risk (no new entities, no migration). The risk is correctness: invalid or HTML-mismatched JSON-LD is *worse than none* for GEO, and the emission must respect conditional gates, resolve cross-node `@id` references, and invalidate precisely on cache. Marketplace dependency posture is a hard constraint.
**Existing Architecture:** Recipe installs node, paragraphs, entity_reference_revisions, jsonapi, content_moderation, workflows, olivero, views (see `recipe.yml:14-40`). `metatag` and `schema_metatag` are **NOT** installed. Node types: service, answer, article, page, evidence_source. The Service view display already renders every GEO field (`config/core.entity_view_display.node.service.default.yml:27-123`) — visible-HTML parity for Service is therefore already satisfiable today.

---

## Feature Overview

GEO Starter ships a governed, sourceable content model but currently emits no JSON-LD (`docs/LIMITATIONS.md:11`, `docs/SCHEMA_MAP.md:30` mark it deferred). The structured meaning encoded in fields like `field_direct_answer`, `field_evidence_sources`, and `field_reviewed_date` is invisible to machines. This plan makes that meaning inspectable as schema.org JSON-LD, emitted only where visible rendered HTML supports it (the non-negotiable parity rule in `SCHEMA_MAP.md:3-12`).

The technical approach is a **lean custom module, shipped as its own composer package** (`drupal/geo_starter_jsonld`) and declared as a dependency of `drupal/geo_starter`, that builds a single per-node `@graph` through a normalizer service and attaches it as a cacheable render element via `hook_node_view_alter`. The module is purely additive: it reads existing fields, emits nothing the page does not already show, makes **no ranking or rich-result promises**, and adds **exactly one new required dependency — a single Zivtech-owned, stable, unpatched module** (not three patch-prone contrib modules) — satisfying the spirit of the Marketplace blocker "stable, supported required dependencies with no patches" (`docs/LIMITATIONS.md:30`). See §6 for why a recipe cannot simply bundle a module on disk, and why this composer-package path is the correct shipping mechanism.

The hardest design problem is **conditional, composed emission**: a Service node containing a `section_faq` paragraph must emit a `Service` object AND a gated `FAQPage`/`mainEntity` graph — but only when the FAQ has ≥2 valid reviewed Q&A pairs. Token-based contrib mapping cannot express this imperative gating; a normalizer service can. That is the core of the approach decision below.

---

## 1. APPROACH DECISION (the load-bearing call)

Three candidate approaches were evaluated against five weighted criteria. The decision is a **custom lean module shipped as its own composer package** (`drupal/geo_starter_jsonld`), required by the recipe — see §6 for why a recipe cannot discover a bundled `modules/` directory.

### Criteria (in priority order)

1. **Marketplace dependency posture** — `docs/LIMITATIONS.md:30`: "stable, supported required dependencies with no patches." Hard gate.
2. **Conditional/gated/composed emission** — can the approach express "emit FAQPage only when ≥2 reviewed Q&A pairs exist on a published node," aggregate paragraph-level types into a page-level graph, and resolve cross-node `@id` references?
3. **Recipe leanness** — `recipe.yml` is deliberately minimal; every added module is a maintenance and security-surface cost the recipe owner inherits.
4. **Parity enforceability** — `SCHEMA_MAP.md:3` requires JSON-LD to mirror visible HTML exactly; the approach must make it easy to emit only rendered fields.
5. **Validation/testability** — fits the established DDEV `recipe apply` + `php:eval` + JSON:API-probe discipline and adds extractable, validatable JSON-LD.

### Option A — `schema_metatag` + `metatag` + `token` stack

| Criterion | Assessment |
| --- | --- |
| Dependency posture | **Fails the gate as a default.** Adds 3 new required modules (metatag, schema_metatag, token) to a recipe that ships none of them today. metatag has a history of recommended patches in some Drupal CMS / distribution contexts; even the *possibility* of a patched required dependency is disqualifying for the Marketplace blocker. token is transitive but still surface. |
| Conditional/composed emission | **Weakest fit.** schema_metatag maps fields to schema properties via token strings on a per-bundle "metatag defaults" config. It is fundamentally declarative. Expressing "emit FAQPage only if ≥2 `section_faq_item` children exist, all on a published node, aggregated across multiple `section_faq` instances, with positions from delta order" is not a token mapping — it is imperative logic. You would end up writing a custom schema_metatag property plugin or a custom token anyway, i.e., custom code *plus* the three dependencies. Nested paragraph traversal and delta-derived `position` are especially poor token fits. |
| Recipe leanness | Worst — 3 modules + per-bundle metatag config entities. |
| Parity | Neutral. Tokens pull field values, but the gating that enforces "don't emit what isn't shown" still has to be hand-built. |
| Validation | Neutral. Output is standard JSON-LD; same validation applies. |

**Verdict:** Rejected primarily on the dependency gate and the conditional-emission misfit. The marquee GEO patterns (gated FAQPage/HowTo from paragraphs) are exactly where token mapping breaks down, forcing custom code regardless — so we'd pay the dependency cost *and* still write the hard part by hand.

### Option B — Custom lean module shipped as its own composer package **(RECOMMENDED)**

A single small module `geo_starter_jsonld`, **published as its own composer package `drupal/geo_starter_jsonld` and declared in the recipe's `composer.json` `require`** (a Drupal 11 recipe is a config artifact and cannot discover a bundled `modules/` directory — see §6 for the full mechanism). It provides:
- a `JsonLdGraphBuilder` service that builds a per-node `@graph` array from confirmed fields,
- per-bundle normalizer plugins (Service, Answer, Article, EvidenceSource) and per-paragraph-bundle contributors (FAQ, StepList, ContactPanel, CardGrid),
- `hook_node_view_alter()` to attach the graph as a `#type => 'html_tag'` `<script type="application/ld+json">` element in `#attached['html_head']`, with full cache metadata.

| Criterion | Assessment |
| --- | --- |
| Dependency posture | **Passes the gate.** Exactly one new required dependency — a single Zivtech-owned `drupal/geo_starter_jsonld` package — versus Option A's three patch-prone contrib modules. Uses only core (serialization, `Component\Render\JsonEncode`, entity API) and modules already installed; **nothing to patch**, and we own the release cadence. GPL-2.0-or-later, consistent with `docs/CONTENT_LICENSES.md:7`. The "no patches" Marketplace concern is about *patched contrib*; an owned, unpatched module is the cleanest possible posture. |
| Conditional/composed emission | **Best fit.** Imperative gating is trivial: a contributor returns `[]` when its gate fails, the builder simply omits it. Page-level aggregation (one `FAQPage` with `mainEntity` collected from all `section_faq` instances) is a normal array merge. `@id` minting and cross-node resolution are explicit. Delta-order `position` is a loop index. |
| Recipe leanness | Excellent — one focused, owned module; no contrib chain. Shipped as a composer package the recipe requires; recipes can't discover a bundled module — see §6. |
| Parity | Best — each contributor reads exactly the fields the view display renders; a shared "emit only non-empty, only published" rule is enforced in one place. |
| Validation | Best — output shape is fully under our control, making golden-file and Rich-Results assertions deterministic. |

**Verdict:** Recommended.

### Option C — Theme-level approach (preprocess/Twig template emitting JSON-LD)

Emit JSON-LD from an Olivero sub-theme preprocess hook or a `html.html.twig` override.

| Criterion | Assessment |
| --- | --- |
| Dependency posture | Passes (no modules), but `docs/LIMITATIONS.md:7` states no GEO-specific theme exists yet and the alpha runs core Olivero. Putting structured-data logic in a theme couples a *retrieval* concern to a *presentation* layer that is explicitly slated to change before Marketplace. |
| Conditional/composed emission | Workable but wrong home. Business/gating logic in preprocess violates the "thin preprocess, logic in services" rule (`~/.claude/rules/php/patterns.md`) and the planner's own failure-mode list. Reuse across JSON:API or future surfaces becomes impossible. |
| Recipe leanness | Neutral but fragile — theme swap (planned) would drop the feature. |
| Parity | Same as B if disciplined. |
| Validation | Same as B. |

**Verdict:** Rejected. The keystone feature must survive the planned theme replacement and must be testable as a service independent of any theme. A module owns the logic; a theme may later *style* the visible Q&A/steps, but JSON-LD emission does not belong there.

### Decision

**Build a lean custom module, `geo_starter_jsonld`, shipped as its own composer package (`drupal/geo_starter_jsonld`) that the recipe `require`s** (not bundled in the recipe's `modules/` — see §6). It is the only option that simultaneously passes the no-patched-dependency Marketplace gate, expresses the conditional/composed/gated paragraph-derived emission cleanly, keeps the recipe lean, and survives the planned theme replacement.

**Where my confidence drops (stated honestly):**
- **Service vs. GovernmentService:** confidence is genuinely mixed (see §2.1). `SCHEMA_MAP.md:17` says `Service`. The demo content is public-service answers, which *could* warrant `GovernmentService`, but that subtype asserts a government provider the recipe cannot guarantee for arbitrary adopters. I recommend `Service` and explain why, but this is a defensible coin-flip that a copy/proposal review (`SCHEMA_MAP.md:40`) should ratify.
- **`@id` URL stability:** minting `@id`s from the node's canonical URL is standard, but if adopters change path aliases the `@id` changes. Acceptable for alpha (canonical URL is the natural identifier); flag for the maintainer.
- **HowTo deprecation nuance:** Google deprecated HowTo *rich-result display*; the markup is still valid for retrieval/agent surfaces (paragraph plan §6, `2026-05-30-paragraph...md:180`). We emit it for machine-readability and make no rich-result claim. This is a judgment call, defensible, but a reviewer may prefer to suppress HowTo entirely. Left as a config flag (§5).
- **Contrib drift risk:** if Drupal CMS later bundles schema_metatag *core-supported and unpatched*, the calculus could shift. Re-evaluate at that point; not today.

---

## 2. JSON-LD Graph Structure Per Node Type

All nodes emit a single `<script type="application/ld+json">` containing one object with `@context: "https://schema.org"` and an `@graph` array. One graph per page; paragraph-derived objects are folded into this graph (never a second script tag). This avoids the "duplicate field content into multiple conflicting representations" non-goal.

### `@id` strategy (cross-reference resolution)

- Every emitted top-level object gets a stable `@id` derived from the node's **canonical absolute URL** plus a fragment:
  - Page object: `{canonical_url}` (no fragment) — the `WebPage`.
  - Primary entity object: `{canonical_url}#service` / `#answer` / `#article` / `#evidence-source`.
  - FAQPage: `{canonical_url}#faq`; each Question: `{canonical_url}#faq-q{delta}`.
  - HowTo: `{canonical_url}#howto`; each step: `{canonical_url}#howto-step{delta}`.
- **Node-to-node references resolve by `@id` = the referenced node's canonical URL.** When node A's `field_evidence_sources` points at Evidence Source node B, A emits `citation: { "@id": "{B_canonical_url}" }`. If B is published and renders its own page, B's own JSON-LD declares the full `CreativeWork` at that `@id`; A only needs the reference. If B is unpublished, see §3 (the reference is suppressed, not dangling).
- `WebPage.mainEntity` points (by `@id`) at the primary entity object so the graph is internally linked, not flat.

### 2.1 Service node → `Service` + `WebPage`

Per `SCHEMA_MAP.md:17`. **Recommend `Service`, not `GovernmentService`** — `GovernmentService` asserts a governmental provider that the recipe cannot guarantee for every adopter (a nonprofit or utility could use this content type); over-claiming the provider type would itself be a parity violation. Adopters who *are* governments can override the type via the §5 setting.

| schema.org property | Source (confirmed machine name) | Notes |
| --- | --- | --- |
| `@type` | (literal) `Service` | Overridable via setting (§5). |
| `@id` | `{canonical_url}#service` | |
| `name` | `node.title` | |
| `description` | `field_summary` (text_long, required — `field.field.node.service.field_summary.yml:14`) | Stripped to plain text. |
| `serviceType` / `about` | `field_topic` (taxonomy_term, -1) | Term name(s); `about` as `Thing` with `name`. |
| `audience` | `field_audience` (taxonomy_term, -1) | `{ "@type":"Audience", "audienceType": term_name }`. |
| `potentialAction` | `field_next_action` (link, required — `field.field.node.service.field_next_action.yml:14`) | `{ "@type":"Action", "name": link_title, "target": link_url }`. The page's single canonical action. |
| (eligibility text) | `field_eligibility` (text_long, optional) | No clean schema.org property; include as `description`-adjacent `eligibleRegion`/free text only if a faithful mapping exists, else **omit** rather than misuse. Default: omit (parity-safe). |
| (problem solved) | `field_problem_solved` (text_long, optional) | No faithful schema.org property; **omit** from JSON-LD (it renders visibly in HTML; absence in JSON-LD is fine — JSON-LD need not mirror *every* field, it must not *exceed* visible content). |
| `citation` | `field_evidence_sources` → nodes | Array of `{ "@id": evidence_canonical_url }`; see §4. |
| `dateModified` | `field_reviewed_date` (datetime, required — `field.field.node.service.field_reviewed_date.yml:14`) | ISO 8601. Also a freshness signal. |
| `provider` / reviewer | `field_reviewed_by_name` (string, optional) | See §2.6 reviewed-by mapping. |
| `provider` (org) | site name / configured org (§5) | `{ "@type":"Organization", "name": site_name }`. |

`WebPage`: `@id` = canonical URL, `name` = title, `url` = canonical URL, `mainEntity` = `{ "@id": "...#service" }`, `dateModified` = `field_reviewed_date`, optional `breadcrumb` (see §2.7).

**Parity reconciliation — rendered-but-unmapped Service fields (drupal/proposal-critic C1).** `field_problem_solved` and `field_eligibility` (both `text_long`, both rendered in `core.entity_view_display.node.service.default.yml`) have **no faithful schema.org property** on `Service`. The parity rule (`SCHEMA_MAP.md:3`) is a *ceiling* — JSON-LD must not **exceed** visible HTML, but it may show **less**; omitting a visibly-rendered field is parity-safe. **Decision: omit both, deliberately**, rather than misuse `eligibilityToWorkRequirement`/`serviceOutput` (which assert semantics the content does not carry) — a wrong property is a worse GEO signal than an absent one. **Acknowledged consequence:** the emitted `Service` object is intentionally thin (name, description, action, topic, audience, dateModified, citations) and does **not** encode what the service does or who qualifies in machine-readable form. If, after the copy/proposal review (`SCHEMA_MAP.md:40`), richer Service semantics are wanted, the faithful path is to evaluate `audience.audienceType` / `eligibleRegion` mappings explicitly — not to retrofit a loose property here. This tradeoff is logged, not silent.

### 2.2 Answer node → `WebPage` + (marquee) `Question`/`Answer`

This is the marquee GEO pattern. Per `SCHEMA_MAP.md:18`, **broad `FAQPage` is avoided on answer pages by default**; the single canonical direct answer is the value.

| schema.org property | Source | Notes |
| --- | --- | --- |
| Page `@type` | `WebPage` | |
| Primary entity `@type` | `Question` with `acceptedAnswer` (`Answer`) | `@id` `{url}#answer`. |
| `Question.name` | `node.title` (the question the page answers) | The answer page's title is the question. |
| `acceptedAnswer.text` | **`field_direct_answer`** (text_long, required — `field.field.node.answer.field_direct_answer.yml:15`) | Stripped to plain text. The canonical direct answer. |
| `acceptedAnswer.citation` | `field_evidence_sources` | Array of `{ "@id": evidence_url }`. |
| `dateModified` | `field_reviewed_date` (required) | |
| reviewer | `field_reviewed_by_name` (optional) | §2.6. |
| `about` / `audience` | `field_topic` / `field_audience` | As in §2.1. |

A page-level `FAQPage` is emitted **only** when the Answer node carries `section_faq` paragraph(s) that pass the gate (§3) — and even then per `SCHEMA_MAP.md:18` the maintainer may disable FAQPage on answers via §5 flag. The single direct-answer `Question` above is *not* the same as a multi-item `FAQPage` and is always safe to emit (it mirrors the page's whole purpose).

### 2.3 Article node → `Article` + `WebPage`

Per `SCHEMA_MAP.md:19`. Use `Article` (not `NewsArticle` unless time-sensitive — flag via §5).

| schema.org property | Source | Notes |
| --- | --- | --- |
| `@type` | `Article` | `@id` `{url}#article`. |
| `headline` | `node.title` | |
| `description` | `field_summary` (text_long, required — `field.field.node.article.field_summary.yml:15`, confirmed) | Stripped to plain text. |
| `author` | **`field_author_name`** (string — `field.field.node.article.field_author_name.yml`, confirmed; rendered in the Article view display) | The Article's actual author field. `{ "@type":"Person", "name": <field_author_name> }`. Corrected per drupal/proposal-critic M4 — the prior draft wrongly routed `author` to the *reviewer* field. |
| `reviewedBy` / `review` | `field_reviewed_by_name` (string — `field.field.node.article.field_reviewed_by_name.yml`) | §2.6 — reviewer, distinct from author. |
| `dateModified` | `field_reviewed_date` (datetime, required — `field.field.node.article.field_reviewed_date.yml:14`) | |
| `datePublished` | `node.published_at` (first-publish transition); else `node.created` | Prefer the content_moderation first-published timestamp; fall back to `created` only when `published_at` is unset. Specify the exact source in the normalizer so two implementers don't diverge (m2). |
| `citation` | `field_evidence_sources` | Array of `{ "@id": evidence_url }`; §4. |
| `about` / `audience` | `field_topic` / `field_audience` | |

### 2.4 Evidence Source node → `CreativeWork` (citation target)

Per `SCHEMA_MAP.md:20`. Makes sources machine-inspectable (a core GEO/provenance value, `docs/PROVENANCE.md`).

| schema.org property | Source (confirmed) | Notes |
| --- | --- | --- |
| `@type` | `CreativeWork` | Default; `Article`/`Dataset` overridable per §5 if a type field is later added. `@id` = canonical URL. |
| `name` | `node.title` | |
| `url` / `sameAs` | **`field_source_url`** (link, required — `field.field.node.evidence_source.field_source_url.yml:14`) | The external source. `url` = source link; emit `sameAs` to the external authority so the citation is inspectable. |
| `publisher` | **`field_publisher`** (string, required — `field.field.node.evidence_source.field_publisher.yml:11`) | `{ "@type":"Organization", "name": publisher }`. |
| `isAccessibleForFree` | (literal true, optional) | Only if visibly stated; default omit. |

Evidence Source has its own rendered page (it is a node type) so its full `CreativeWork` lives at its `@id`; referencing nodes point at that `@id` (§2's `@id` strategy). This is the resolution mechanism that makes node-to-node `field_evidence_sources` references emit cleanly as `citation`.

### 2.5 Page node

`page` node type has no GEO fields and no `field_sections` (paragraph plan §7, line 207). Emit a minimal `WebPage` (`@id`, `name`, `url`) only — or nothing if title-only adds no value. Default: minimal `WebPage`. No primary entity object.

### 2.6 reviewed_by / reviewed_date mapping (GEO trust signal)

`field_reviewed_by_name` is a **string** (a name, not a User reference). Map conservatively:
- `dateModified` ← `field_reviewed_date` (ISO 8601) on every type. This is the freshness signal.
- When `field_reviewed_by_name` is non-empty, emit a `review`:
  `"review": { "@type":"Review", "author": { "@type":"Person", "name": <field_reviewed_by_name> }, "dateModified": <field_reviewed_date> }`
  and/or `"reviewedBy": { "@type":"Person", "name": <name> }`.
- Do **not** invent a `Person` `@id` or `sameAs` — we only have a name string; over-asserting identity would be a parity violation.
- On Article, `author` comes from the dedicated **`field_author_name`** (confirmed present in config), and `field_reviewed_by_name` maps to `reviewedBy`/`review`. These are two distinct fields — do not conflate them (drupal/proposal-critic M4). If a site leaves `field_author_name` empty, omit `author` (do not fall back to the reviewer).

### 2.7 BreadcrumbList (optional, gated on visible nav)

`SCHEMA_MAP.md:17,19,21` lists `BreadcrumbList`, but it must "match visible navigation" (`SCHEMA_MAP.md:21`). Olivero renders breadcrumbs; emit `BreadcrumbList` **only** if the rendered page actually shows a breadcrumb trail for that node. **Defer to a P3 add-on** — it depends on theme/menu config not yet finalized (`docs/LIMITATIONS.md:7`). Not in the P1 scope.

---

## 3. Conditional Emission Logic (the gates)

Invalid or unsupported JSON-LD is worse than none (`SCHEMA_MAP.md:3,10`). A single shared guard plus per-type gates.

### Universal guards (applied to every object before it enters the graph)

1. **Published-only.** Build JSON-LD only when the node's moderation state is `published` AND `$node->isPublished()`. Draft/needs_review/archived nodes emit **no** JSON-LD. (Ties to the moderation-inheritance model, paragraph plan `2026-05-30-paragraph...md:213`, and the 403-draft probe.) The render builder runs in node-view context; the access/publish check is explicit, not assumed.
2. **Non-empty only.** Any property whose source field is empty is omitted. No empty strings, no empty arrays, no null properties. An object with no required content is dropped entirely.
3. **Parity.** A property is emitted only if its source field is **present (not hidden) in the bundle's `default` view display** AND non-empty. The builder reads the view-display config component list (deterministic, cheap — e.g. `core.entity_view_display.node.service.default.yml:27-117` lists exactly the rendered fields), **not** the rendered build (brittle to introspect). A field hidden from display therefore never leaks into JSON-LD. Decision: commit to view-display-config lookup, not render-array introspection.
4. **Valid JSON.** Encode via core JSON encoding with `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`; on any encode failure, emit nothing (fail closed). Text fields are `strip_tags` + whitespace-collapsed before insertion (no markup in JSON-LD string values).
5. **Full canonical view only (MANDATORY — drupal-critic M1).** Emit JSON-LD **only when `$view_mode === 'full'` AND the node is being rendered on its own canonical route** (i.e., it is the page's main entity, not a teaser/embed). `hook_node_view_alter()` fires for every node render — teasers in Views listings, `entity_reference` embeds, search-result snippets. Without this gate, a node rendered as a teaser on a listing page would attach its `@graph` to *that listing page's* `<head>`, and with a single stable `html_head` dedup key the last-rendered teaser would win — emitting JSON-LD on a non-canonical page describing the wrong entity (a direct parity violation). Guard: `if ($view_mode !== 'full') { return; }` plus a canonical-route check. A `$view_mode === 'full'` negative test (teaser-on-listing emits nothing) is a required acceptance check (§7 item 5b).

### `section_faq` → `FAQPage` gate (from paragraph plan §6, `2026-05-30-paragraph...md:179`)

Emit a page-level `FAQPage` (with `mainEntity` = array of `Question`/`acceptedAnswer`) **only when ALL hold**:
- (a) parent node is `answer` or `service` (paragraph §7 enablement);
- (b) **≥2** `section_faq_item` children with **both** `field_section_question` and `field_section_answer` non-empty;
- (c) parent node is `published` (universal guard 1);
- (d) the page is not already declaring a conflicting primary rich-result type, and the §5 `faqpage_on_answer` flag is on (default: on for service, configurable for answer per `SCHEMA_MAP.md:18`).
- If any fails → render visible Q&A HTML + `WebPage` only; **no `FAQPage`**. One FAQPage per page; `mainEntity` aggregates Q&A across *all* qualifying `section_faq` instances. `Question.name` ← `field_section_question`; `acceptedAnswer.text` ← `field_section_answer` (stripped); `position`/order ← paragraph delta.

### `section_step_list` → `HowTo` gate (`2026-05-30-paragraph...md:180`)

Emit `HowTo` (with `step` = array of `HowToStep`) **only when**:
- (a) **≥2** `section_step_item` children with non-empty `field_section_step_name` AND `field_section_step_text`;
- (b) node published;
- (c) §5 `emit_howto` flag on (default on; allows a reviewer to suppress given Google's display deprecation — see §1 confidence note).
- `HowToStep.name` ← `field_section_step_name`; `.text` ← `field_section_step_text` (stripped); `.image` ← `field_section_step_image` media URL as `ImageObject` (only if the media renders); `position` ← delta. No rich-result claim is made.

### `section_contact_panel` → `ContactPoint` (`2026-05-30-paragraph...md:181`)

Emit nested under the parent Service's `Service`/`Organization` `contactPoint` — **never standalone**. Only emit sub-properties whose fields are non-empty AND visibly rendered: `telephone` ← `field_section_phone`; `email` ← `field_section_email`; `PostalAddress` ← `field_section_address`; `openingHours` ← `field_section_hours` only if it parses to a valid format, else omit. Contact panel is service-only (paragraph §7, line 204).

### `section_card_grid` → `ItemList` (`2026-05-30-paragraph...md:182`)

Emit `ItemList` with `itemListElement` = `ListItem` per referenced node, each `{ position: delta+1, url: card_node_url, name: card_node_title }`. Only published referenced nodes become list items (drop unpublished targets). If zero published targets remain, omit the `ItemList`.

### Non-emitting paragraph types

`section_cta`, `section_alert`, `geo_starter_section` → **no distinct schema type** (paragraph §6 lines 184-186). Their content renders as visible body under `WebPage`; the builder ignores them for JSON-LD. (`section_cta` is explicitly flagged-and-deferred in the paragraph plan; do not emit an `Action` for it.)

---

## 4. Provenance / Citation Emission

Provenance and inspectable sources are a core GEO value (`docs/PROVENANCE.md`, `docs/CONTENT_LICENSES.md`).

- `field_evidence_sources` (entity_reference → evidence_source node, cardinality -1, storage `field.storage.node.field_evidence_sources.yml:15`) is emitted as schema.org **`citation`** on the referencing node (`Service`, `Article`, or `Answer`'s `acceptedAnswer`).
- Each citation is `{ "@id": <evidence_node_canonical_url> }`. The Evidence Source node's own page declares the full `CreativeWork` at that `@id` (§2.4), with `sameAs`/`url` = `field_source_url` (the external authority) and `publisher` = `field_publisher`. This is what makes the source **inspectable**: a machine following the `@id` reaches a page whose JSON-LD names the external source and publisher.
- **Suppression rule:** if a referenced evidence_source node is unpublished, the citation reference is **omitted** (no dangling `@id` to a 403/404). The reference set is filtered through the published guard.
- `sameAs` is used on the Evidence Source object (pointing to `field_source_url`) rather than on the citing node, so the external link is asserted exactly once, at the source, matching the visibly rendered source link (`SCHEMA_MAP.md:38` "Visible source links").
- No `citation` is emitted for a node whose `field_evidence_sources` is empty (universal guard 2).

---

## 5. Caching / Render-Pipeline Correctness

Maps to the "cache behavior" release gate (`docs/LIMITATIONS.md:13`). JSON-LD must invalidate when the node OR any referenced entity changes.

**Attachment mechanism:** `hook_node_view_alter()` builds the graph via the `JsonLdGraphBuilder` service and attaches it as:
- a render element of `#type => 'html_tag'`, `#tag => 'script'`, `#attributes => ['type' => 'application/ld+json']`, `#value` = the JSON string, placed in `$build['#attached']['html_head']` (so it lands in `<head>`, deduplicated by a stable key like `geo_starter_jsonld`).

**Cache metadata** (bubbled onto `$build`, so Dynamic Page Cache and the render cache invalidate correctly):

| Cacheable item | Cache tags | Cache contexts | Max-age | Invalidation trigger / rationale |
| --- | --- | --- | --- | --- |
| Node JSON-LD graph | `node:{nid}` (the node itself) | `url.path` | permanent (`-1`) | Node save/delete invalidates `node:{nid}` automatically. The `url.path` context is added **specifically because the graph embeds the absolute canonical URL as `@id`s** (which vary by path/alias), not as belt-and-suspenders — node render already keys on the node, but the absolute-URL-in-`@id` case needs path in the cache key. Graph is otherwise a pure function of node + references; cache until a tag invalidates. |
| + each referenced evidence_source | `node:{evidence_nid}` for every node in `field_evidence_sources` | — | — | If an evidence source is unpublished/edited, the citing page's JSON-LD must recompute (citation may need suppression). **This is the cross-entity invalidation the brief calls out.** |
| + each referenced card node | `node:{card_nid}` for `section_card_grid.field_section_cards` targets | — | — | A card target unpublished → its `ListItem` must drop. |
| + paragraph revisions | covered by `node:{nid}` | — | — | Paragraphs are revisioned with the parent (entity_reference_revisions); editing a paragraph creates a new node revision → `node:{nid}` invalidates. No separate paragraph tag needed. |
| + config dependency | `config:geo_starter_jsonld.settings` | — | — | The §5 settings (type overrides, flags) affect output; changing them must invalidate. |
| + media (step image) | `media:{mid}` for any `field_section_step_image` emitted as `ImageObject` | — | — | Media swap → image URL changes. |

- **Cache contexts:** because the published guard means draft nodes emit nothing, and access is uniform for anonymous on published content, **no `user`/permission context is needed** for the JSON-LD itself (it is identical for everyone who can see the published page). Add `user.permissions` only if a future requirement varies output by role (not in scope). Avoiding an unnecessary `user` context keeps the page cacheable for anonymous traffic — the GEO/crawler audience.
- **No max-age:0.** The graph is deterministic from entities; relying on tag invalidation (not time) is correct and performant.
- **Collect cacheability via `CacheableMetadata`** in the builder service and `->applyTo($build)` so tags bubble even when a referenced entity is loaded mid-build. Every entity the builder loads (referenced evidence sources, card nodes, media) MUST have its cache tags merged in — this is the single most error-prone spot and is a named drupal-critic checkpoint.

---

## 6. Shipping as Recipe Config

The recommended approach is a custom module **shipped as its own composer package and depended on by the recipe**.

- **Shipping mechanism (corrected — recipes do NOT discover a bundled module).** In Drupal 11, a recipe is a *config artifact*: a module named in `recipe.yml` `install:` must already exist on disk (composer-installed or in `modules/custom/`). A `drupal/...-recipe` package does **not** register a `modules/` subdirectory for Drupal's module discovery. There is a contrib bridge — **Recipe Code Installer** — that extracts a recipe's `code/` subdirectory into the site's custom-module directory on apply, **but** (a) it forces the bundled module's machine name to equal the recipe name, and (b) it is itself an unsanctioned contrib dependency, which would re-introduce exactly the patch-prone-dependency problem the approach decision exists to avoid. **Therefore the correct, Marketplace-safe path is:**
  1. Publish `geo_starter_jsonld` as its own composer package `drupal/geo_starter_jsonld` (Zivtech-owned, GPL-2.0-or-later).
  2. Add it to `drupal/geo_starter`'s `composer.json` `require` — this is the **one** intentional dependency change for this feature (see §1; honest framing: not "zero new dependencies," but "one owned, unpatched package vs. three patch-prone contrib modules").
  3. List `geo_starter_jsonld` in `recipe.yml` `install:` (after `node`). When the recipe is composer-installed, the dependency resolves and the module is present on disk before apply.
- The module's `.info.yml` declares `core_version_requirement: ^11` and dependencies on only already-installed modules (node, paragraphs, link, text, datetime — all present in `recipe.yml`).
- **Module contents:**
  - `geo_starter_jsonld.info.yml`
  - `geo_starter_jsonld.module` — `hook_node_view_alter()` (thin: calls the service, attaches result + cache metadata).
  - `geo_starter_jsonld.services.yml` — registers `JsonLdGraphBuilder` and the contributor plugin manager.
  - `src/JsonLdGraphBuilder.php` — orchestrates: resolves bundle normalizer, runs paragraph contributors, applies universal guards, assembles `@graph`, returns string + `CacheableMetadata`.
  - `src/Plugin/GeoJsonLd/*` — per-bundle normalizers (Service, Answer, Article, EvidenceSource, Page) and per-paragraph contributors (Faq, StepList, ContactPanel, CardGrid) as plugins (extensible, testable in isolation, per `~/.claude/rules/php/patterns.md` "Plugins over hooks").
  - `config/install/geo_starter_jsonld.settings.yml` — simple config (see classification below).
  - `config/schema/geo_starter_jsonld.schema.yml` — config schema.
  - `tests/src/Kernel/`, `tests/src/Functional/` — see §7.
- **Config classification:**

  | Config item | Type | Schema | Exportable? | Why here |
  | --- | --- | --- | --- | --- |
  | `geo_starter_jsonld.settings` | **Simple config** | `service_type` (string, default `Service`), `article_type` (string, default `Article`), `faqpage_on_answer` (bool, default false per `SCHEMA_MAP.md:18`), `faqpage_on_service` (bool, default true), `emit_howto` (bool, default true), `organization_name` (string, default empty → falls back to site name), `evidence_default_type` (string, default `CreativeWork`) | Yes (`config/install`) | Site-tunable feature flags / type overrides; not per-environment, not runtime state. Simple config (not a config entity) because there is a single fixed settings object with no CRUD/multiple-instances need. |
  | (none) State API | — | — | No | Nothing runtime/non-exportable here; no import timestamps or counters. Explicitly *not* using State API. |

- **Composer change:** exactly one line added to `drupal/geo_starter`'s `require` — `drupal/geo_starter_jsonld` (Zivtech-owned). No third-party contrib is added (no metatag/schema_metatag/token). This is the concrete payoff of the approach decision: one owned, unpatched package instead of three patch-prone external ones, consistent with the `docs/PROVENANCE.md:17` "declared as Drupal packages, not copied" posture.

---

## 7. Validation Plan

Two layers: the established recipe/DDEV discipline, plus JSON-LD-specific validation.

### Layer 1 — Recipe apply + `php:eval` (established pattern, paragraph plan §11)

1. **Recipe applies:** `drush recipe apply <path>` exits 0 on a throwaway Drupal CMS install; `drush cr`. Module `geo_starter_jsonld` is enabled.
2. **Service resolves:** `php:eval` → `\Drupal::service('geo_starter_jsonld.graph_builder')` is not null.
3. **Settings present:** `php:eval` → `\Drupal::config('geo_starter_jsonld.settings')->get('service_type') === 'Service'`.

### Layer 2 — JSON-LD-specific validation (the new discipline)

For each node type, on the throwaway install:

4. **Published node emits valid JSON-LD:** create + publish a Service node with `field_direct_answer`/`field_summary`/`field_next_action`/`field_reviewed_date`/`field_evidence_sources` populated and a `section_faq` with ≥2 valid Q&A. Request the canonical URL (anonymous). Extract the `<script type="application/ld+json">` payload from the HTML.
   - **(a) Valid JSON:** `json_decode` succeeds (no trailing commas, valid encoding).
   - **(b) Graph shape:** assert `@context == https://schema.org`, a `Service` object with the expected `@id`/`name`/`description`/`potentialAction`, a `WebPage` whose `mainEntity.@id` resolves to the Service `@id`, a `FAQPage` with `mainEntity` length == number of valid Q&A pairs.
   - **(c) Parity:** every JSON-LD string value also appears in the rendered visible HTML (assert `field_summary` text, each FAQ question, the next-action label are all present in the page body). This is the `SCHEMA_MAP.md:3` rule made executable.
   - **(d) Rich-Results / schema.org expectations:** validate the extracted JSON-LD against schema.org type/property expectations (Google Rich Results structured-data test rules where applicable). Confirm required FAQPage/Question/acceptedAnswer properties are present; confirm no unknown/misused properties. (Can run via the Schema Markup Validator API or a vendored validator in CI; for local DDEV, a `php:eval` shape assertion + JSON validity is the gate, with the external validator run before any public claim per `SCHEMA_MAP.md:39-40`.)
5. **Gate negative tests:**
   - **(view-mode, M1)** A published Service rendered as a **teaser on a listing/Views page** (not its canonical route) emits **no** `application/ld+json` — assert the listing page's `<head>` contains exactly zero (or only the canonical page's) JSON-LD, never a teaser's graph. This is the regression test for universal guard #5.
   - Service node with a `section_faq` of **1** Q&A → **no** `FAQPage` in output (gate b fails), Q&A still visible in HTML.
   - `section_step_list` with 1 step → **no** `HowTo`.
   - Node with empty `field_evidence_sources` → **no** `citation` key.
6. **Draft does not leak (ties to existing 200/403 probe discipline):**
   - Set the node to `draft`. Anonymous request of the canonical URL → page is 403/not-visible AND contains **no** `application/ld+json` script. Assert absence explicitly.
   - Re-run the existing JSON:API probe: `GET /jsonapi/node/service/{uuid}` published → 200; draft → 403 (paragraph plan §11 items 6-7). JSON-LD must mirror this access posture.
7. **Citation suppression:** publish a Service citing an evidence_source, confirm `citation: [{ "@id": <evidence_url> }]`; then unpublish the evidence_source, re-request the Service page → the citation `@id` is **absent** and the page's `node:{evidence_nid}` cache tag drove invalidation (assert by re-fetch after `drush cr`-free tag invalidation).
8. **Evidence Source page:** request a published evidence_source canonical URL → `CreativeWork` with `sameAs`/`url` == `field_source_url` and `publisher.name` == `field_publisher`.
9. **Cache invalidation:** edit the node's `field_summary`, re-request → JSON-LD `description` reflects the change (proves `node:{nid}` tag works). Edit a referenced evidence source's `field_publisher`, re-request the *citing* page → no stale data (proves cross-entity tag).

### Unit/Kernel/Functional split (per `~/.claude/rules/php/testing.md`)

- **Kernel:** `JsonLdGraphBuilder` against real entities — gate logic, `@id` minting, citation suppression, cache-metadata collection (assert tags include referenced node ids). Fast, no browser.
- **Functional (BrowserTestBase):** full page request, extract `<script>`, JSON validity + parity + draft-absence. Covers items 4-9.
- **Unit:** pure helpers (text stripping, ISO date formatting, HowTo/FAQ shape builders) with mocked inputs.

---

## 8. Implementation Tasks (TDD, prioritized)

Priority follows GEO payoff (matches paragraph plan §9: FAQ/HowTo first).

> **Staging — read before sequencing (drupal/proposal-critic M3).** This plan splits into two phases with a **hard cross-plan dependency**:
> - **Phase A (T1–T3): ships against the CURRENT content model.** Service/WebPage, Answer Question/acceptedAnswer, Article, and Evidence Source citations all read node fields that exist today. No dependency on the Paragraph library plan.
> - **Phase B (T4–T6): HARD-BLOCKED on the Paragraph library.** FAQPage (T4), HowTo (T5), ContactPoint + ItemList (T6) read paragraph bundles (`section_faq`/`section_faq_item`, `section_step_list`/`section_step_item`, `section_contact_panel`, `section_card_grid`) that **do not exist yet** — they are deliverables of `2026-05-30-paragraph-component-library-content-model-plan.md` (its Tasks 1–3/6). Phase B cannot start, build, or be tested until those bundles ship. The earlier "node-level emitters run in parallel" framing was misleading: the marquee FAQPage/HowTo value sits at the END of the chain (Paragraph-T1/T2 → JSON-LD-T1 → JSON-LD-T4/T5), and it is the hardest work (T3/T4 rated high effort). Do not start T4–T6 against non-existent bundles.

### Task 1: Module skeleton + builder service + Service/WebPage normalizer (P1)
**Review checkpoint** (drupal-critic: hook thinness, service DI, cache-metadata collection, published guard).
**Files:** new package `drupal/geo_starter_jsonld`: `geo_starter_jsonld.{info.yml,module,services.yml}`, `src/JsonLdGraphBuilder.php`, `src/Plugin/GeoJsonLd/ServiceNormalizer.php`, `config/install/geo_starter_jsonld.settings.yml`, `config/schema/geo_starter_jsonld.schema.yml`; add `drupal/geo_starter_jsonld` to the recipe's `composer.json` require; add `geo_starter_jsonld` to `recipe.yml` install list.
**Structure:** `hook_node_view_alter()` → builder → `@graph` with `Service` + `WebPage`; attach to `html_head` with `node:{nid}` + config tag + `url.path` context.
**Tests:** Kernel (graph shape, published guard, cache tags), Functional (valid JSON, parity, draft-absence).
**Cache:** §5 row 1, 4. **Permissions:** none new (read-only, published-only).

### Task 2: Answer direct-answer (Question/acceptedAnswer) + Article normalizers (P1)
**Review checkpoint** (drupal-critic: field machine-name accuracy, reviewed_by/dateModified mapping, no over-claiming Person identity).
**Files:** `src/Plugin/GeoJsonLd/{AnswerNormalizer,ArticleNormalizer}.php`.
**Tests:** Kernel (acceptedAnswer ← field_direct_answer; Article dateModified ← field_reviewed_date; review block), Functional parity.

### Task 3: Evidence Source CreativeWork + citation/@id resolution + suppression (P1)
**Review checkpoint** (drupal-critic: cross-entity cache tags, unpublished-source suppression, `sameAs` correctness).
**Files:** `src/Plugin/GeoJsonLd/EvidenceSourceNormalizer.php`, citation logic in builder.
**Tests:** Kernel (citation `@id` set; suppression on unpublish; cache tag includes evidence nid), Functional (§7 items 7-8).

### Task 4: `section_faq` → gated FAQPage contributor (P1)
**Review checkpoint** (drupal-critic: gate correctness — ≥2 valid pairs, published, flag; page-level aggregation across instances; child paragraph traversal).
**Files:** `src/Plugin/GeoJsonLd/FaqContributor.php`.
**Tests:** Kernel (gate pass/fail at 1 vs 2 pairs; aggregation), Functional (FAQPage present/absent; parity of each question).

### Task 5: `section_step_list` → gated HowTo contributor (P1/P2)
**Review checkpoint** (drupal-critic: delta-order positions, image as ImageObject only when rendered, emit_howto flag).
**Files:** `src/Plugin/GeoJsonLd/StepListContributor.php`.
**Tests:** Kernel (≥2 step gate, position order), Functional.

### Task 6: `section_contact_panel` → ContactPoint + `section_card_grid` → ItemList (P2)
**Review checkpoint** (drupal-critic: ContactPoint nested not standalone; ItemList drops unpublished card targets + their cache tags).
**Files:** `src/Plugin/GeoJsonLd/{ContactPanelContributor,CardGridContributor}.php`.
**Tests:** Kernel (nesting, unpublished-card drop, media tag), Functional.

### Task 7: Validation harness + external structured-data validation + copy/proposal review (P2)
**Review checkpoint** (drupal-critic + copy review per `SCHEMA_MAP.md:40`).
**Files:** a `tools/` JSON-LD extraction + validation script **matching the JSON:API published/draft probe discipline** described in the paragraph plan §11 (items 6-7) and `docs/PROVENANCE.md:11` ("helper scripts under `tools/`"). Note: locate the actual existing probe script under `tools/` first and extend it if present; otherwise author a sibling script following the same 200-published/403-draft pattern. CI wiring.
**Tests:** the full §7 Layer 2 suite as repeatable harness; run schema.org/Rich-Results validation; update `docs/LIMITATIONS.md:11` and `SCHEMA_MAP.md:30` status once green.

### Deferred (P3): BreadcrumbList (§2.7), NewsArticle/GovernmentService overrides, `section_media_text` ImageObject (only if that paragraph bundle is built).

---

## 9. Review Checkpoint Plan

| Checkpoint | After Task | drupal-critic / cross focus |
| --- | --- | --- |
| Module + Service emission | 1 | Hook thinness, DI, cache-metadata bubbling, published guard, parity |
| Answer/Article mapping | 2 | Field machine-name accuracy, reviewed_by mapping, no identity over-claim |
| Citation + @id resolution | 3 | Cross-entity cache tags, suppression of unpublished sources |
| FAQPage gate | 4 | Gate logic (≥2, published, flag), page-level aggregation |
| HowTo gate | 5 | Delta positions, image gating, deprecation posture |
| ContactPoint/ItemList | 6 | Nesting, unpublished-target drop, media tags |
| Full validation + claims | 7 | External validator green; copy review before any public GEO/structured-data claim (`SCHEMA_MAP.md:40`) |

## 10. Non-Goals (held)

- No required AI provider, AI Agents, MCP, RDF, hypermedia API, or agent-write workflow (`docs/LIMITATIONS.md:21`). JSON-LD only; **no RDFa**.
- No guaranteed AI citations, rankings, rich results, or answer-engine placement (`docs/LIMITATIONS.md:22`). The recipe enables inspectability; it promises no outcomes.
- No new required production dependency; no patched dependency (`docs/LIMITATIONS.md:30`).
- No duplication of node field content into conflicting representations — one `@graph` per page, each fact asserted once at its canonical `@id`.
- No JSON-LD emitted beyond visible rendered HTML (`SCHEMA_MAP.md:3`).

## 11. Next Steps
**Execute with:** `/drupal-config-executor` for the recipe `config/install` settings + schema; module PHP authored under TDD (`test-driven-development`).
**Review with:** `/drupal-critic` at each checkpoint; copy/proposal review before publishing any GEO claim.

---
### Contract Appendix (spec-kitty-bridge)

**Architecture Overview:** One custom module `geo_starter_jsonld`, **shipped as its own composer package `drupal/geo_starter_jsonld` that the recipe `require`s** (recipes cannot discover a bundled `modules/` directory — §6); a `JsonLdGraphBuilder` service + per-bundle normalizer plugins + per-paragraph contributor plugins build a single per-node schema.org `@graph`, attached via `hook_node_view_alter` to `html_head` with full cache metadata, **only on the full canonical node view** (guard #5). One owned, unpatched required package (not three patch-prone contrib modules) — the Marketplace dependency posture. Conditional gates suppress invalid/empty/draft/non-canonical structured data.

**Implementation Tasks:** Tasks 1-7 above. Effort: T1 medium, T2 medium, T3 high (cross-entity cache + suppression), T4 high (gating + aggregation), T5 medium, T6 medium, T7 medium. **Depends-on:** T2–T3 depend on T1 (Phase A, ships against current config). **T4–T6 (Phase B) additionally depend on the Paragraph library plan's bundles existing** (`section_faq`/`section_step_list`/`section_contact_panel`/`section_card_grid`) — hard cross-plan blocker, see §8 staging note. T7 depends on T1-T6.

**Test Strategy:** Kernel (builder logic, gates, cache tags), Functional (page extraction, JSON validity, parity, draft-absence, 200/403 mirror), Unit (formatters); external schema.org/Rich-Results validation in T7.

**Acceptance Criteria:** valid JSON-LD on published nodes; no JSON-LD on drafts; FAQPage only on ≥2 reviewed Q&A; HowTo only on ≥2 steps; citations resolve by `@id` and suppress on unpublish; cache invalidates on node + referenced-entity change; output never exceeds visible HTML.

**Failure Modes:** stale cross-entity citations (mitigated by referenced-node cache tags); invalid JSON (fail-closed encode guard); draft leakage (published guard + functional absence test); over-claiming rich results (no claims; copy review gate); dependency creep (zero contrib added — the whole point of the approach decision).
