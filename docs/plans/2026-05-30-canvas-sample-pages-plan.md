# GEO Starter — Canvas Sample Pages Plan (C-01..C-04)

> Planning artifact (drupal-canvas-planner). Companion to the Paragraph component
> library plan and the JSON-LD emission plan, all dated 2026-05-30.
> **Drupal CMS:** 11.3.x / `drupal/cms` 2.1.x. `drupal/canvas` **1.4.1** (pin exact for the alpha). Theme: core Olivero.
> **Component Strategy:** Canvas pages composed from **stock SDC primitives only** with **static props**. No code components, no npm/Workbench build pipeline in the alpha.
>
> **⚠ 2026-06-04 — Phase 0 RAN; this plan is amended by its results**
> (`2026-06-04-canvas-phase0-gate-results.md`): the "stock" primitives do NOT
> ship with `drupal/canvas` (it ships only `canvas:image`) — they ship with
> **Mercury**, Drupal CMS 2.x's default theme. Decision (Alex): geo_starter's
> default theme moved **Olivero → Mercury**; compose C-01..C-04 from
> `mercury:*` components (heading, text, button, card×5, grid, section,
> group, hero-billboard, hero-side-by-side, cta, accordion — all confirmed
> present and enabled). The exact-pin language here and in §0d is superseded
> by the beta plan: **`^1.4` committed, no exact pins** (validated minor:
> 1.4.1). Authoring loop proven: UI → `content:export` → recipe re-import →
> byte-identical tree.

**Scope:** Four component-composed `canvas_page` entities — C-01 Homepage, C-02 Migration landing, C-03 Service-area hub, C-04 Campaign — authored in the Canvas UI on a scratch site, exported as recipe content YAML into `content/canvas_page/`, and applied via `drush recipe apply`. They are marketing/navigation surfaces that link to canonical Service/Answer/Article/Evidence Source nodes; they do not become canonical answer content.

---

## Where the confidence drops (read first)

The single load-bearing unknown is **what the stock Canvas 1.4.1 SDC component library actually ships**. Confirmed via context7: primitives `heading`, `text`, `image` / `canvas:image`, `button`, `button-group`, `card` exist, and components support `props` + `slots` + nested `elements`. **Not confirmed:** that 1.4.1 ships a **hero container**, **card-grid / multi-column layout**, **alert/callout**, or **accordion** container. The whole plan's component composition depends on those containers existing.

Therefore **Phase 0 is a hard gate, not a footnote.** If Phase 0 finds only bare primitives with no layout/grid container, the fallback is a flat single-column composition (acceptable for alpha) — and if even that produces a weak homepage, the honest fallback is to **defer C-01..C-04 and ship the Marketplace screenshot from the already-working rendered Service page** (the current `screenshot.webp` source). Not claiming the four pages are buildable until Phase 0 verifies the container inventory.

A second known limitation, accepted deliberately: Canvas props are **static**. Card grids referencing Service/Answer nodes will hardcode title/summary/URL as props. They will go stale if an editor renames a node. This is the lean-alpha trade; the live-data version (a JSON:API-backed code component) is named as future-state with its cost.

---

## Canvas Context & Requirements

- **Editorial/site-building context:** Site builders compose these four pages visually in Canvas. Editors do not co-edit them in the alpha. Canonical content (Service/Answer/Article) stays in the Paragraphs/node lane — never mixed onto a Canvas page (per AUTHORING_MODEL "Not Supported").
- **Content model dependencies (source of truth — from `tools/create-alpha-sample-content.php`):**
  - **Services (4):** `41…001` Apply for emergency food and utility assistance (alias `/apply-emergency-food-and-utility-assistance` — verified in `content/node/41000000-…001.yml:43`; NO `/services/` prefix, no `-for-`), `41…002` Request a home repair permit record, `41…003` Join a neighborhood skills program, `41…004` Get help with a past-due water bill. *(Confirm each alias against its `content/node/*.yml` before using — do not assume a `/services/` prefix.)*
  - **Answers (8):** `42…001`–`42…008`, aliases `/answers/…`.
  - **Articles (3):** `43…001`–`43…003`, aliases `/articles/…`.
  - **Evidence Sources (6):** `40…001`–`40…006`, aliases `/sources/…`.
  - **Node fields available to surface:** `field_direct_answer`, `field_summary`, `field_problem_solved`, `field_eligibility`, `field_next_action` (link), `field_evidence_sources`, `field_reviewed_by_name`, `field_reviewed_date`, `field_audience`, `field_topic`, `field_related_services`, `field_related_answers`, `body`.
  - **Taxonomy (corrected model per VALIDATION.md):** `topic` vocabulary holds the four **subjects** — Benefits and assistance, Community programs, Housing and utilities, Permits and records. `audience` has 5 terms. `field_service_area` was removed — **do not reference it.**
  - **Existing Canvas shell:** `canvas_page` UUID `45000000-0000-4000-8000-000000000001`, alias `/geo-starter`, no component tree. Treat as the C-01 candidate or retire it (decision in Phase 0/Task C-01).
- **GEO value prop each page must visibly demonstrate:** machine-inspectable structure, visible sources (link to `/sources/…` Evidence nodes), visible review dates/governance, and stable URLs to canonical content.

---

## Phase 0 — Component Inventory Verification + Authoring-Loop Proof (GATE)

This phase blocks C-01..C-04. Do it on a throwaway DDEV Drupal CMS install with the recipe applied.

**0a. Stock component inventory.** In the Canvas UI sidebar, record which components ship in 1.4.1. Confirm presence/absence of, at minimum:

| Need | Likely stock name | Confirmed? | Fallback if missing |
|---|---|---|---|
| Section / column container | (verify) | **MUST verify** | Flat single-column stack |
| Card grid | (verify) | **MUST verify** | Repeated `card` in a section, or stacked cards |
| Hero / banner | (verify) | **MUST verify** | `heading` + `text` + `image` + `button` in a section |
| Heading | `heading` | likely yes | — |
| Rich text | `text` | likely yes | — |
| Image | `canvas:image` / `image` | yes (context7) | — |
| Button / button-group | `button`, `button-group` | yes (context7) | link in text |
| Card | `card` | yes (context7) | heading+text+button |
| Alert / callout | (verify) | **MUST verify** | `card` styled, or text block |
| Accordion / disclosure | (verify) | **MUST verify** | omit; not needed for C-01..C-04 |

**0b. Authoring-loop proof (the spine).** Prove this loop end-to-end on ONE trivial page before building real ones:
1. Compose a 2-component page (heading + text) in the Canvas UI.
2. Export the `canvas_page` entity to content YAML (`drush content:export` or the recipe's content-export path) — capture the serialized component tree.
3. Commit that YAML to `content/canvas_page/<uuid>.yml`.
4. `drush recipe apply` on a *second* fresh install; confirm the tree renders identically and route returns 200.

**0c. Decision output of Phase 0 (write this down):**
- Container components present → proceed with full compositions below.
- Only bare primitives → switch all pages to flat single-column; note in LIMITATIONS.
- Loop not reproducible as YAML (tree not cleanly content-exportable/importable) → **STOP**; escalate. Do not hand-author serialized tree YAML blind.

**0d. Version pin.** Add exact pin `drupal/canvas: 1.4.1` to `composer.json` for the alpha so the committed tree format cannot drift under a minor update. (Document the trade: pin blocks security minors; acceptable for a non-Marketplace alpha, revisit at release.)

**Review checkpoint:** `drupal-critic` → `canvas-component-definition`, `canvas-component-upload`.

---

## Component Inventory (the only components used across all four pages)

Stock SDC primitives, static props. Pending Phase 0 confirmation of container names.

| Logical block | Vocabulary row | Stock composition | Data |
|---|---|---|---|
| Hero / intro | Hero / page intro | hero **or** section[ heading + text + image + button ] | Static props |
| Direct-answer block | Direct answer | section[ heading "Direct answer" + text ] | Static prop (page-level statement, NOT a node's `field_direct_answer` copied — see note) |
| Card grid | Card grid / related content | card-grid **or** section[ card × N ] | Static props per card (title, summary, href) sourced *by hand* from node fields |
| CTA / next action | CTA / next action | button **or** button-group | Static label + href |
| Evidence/source list | Evidence/source list | section[ heading "Sources" + (card or text per source with link) ] | Static; each links to a `/sources/…` alias |
| Alert / callout | Alert / callout | alert **or** card (styled) + text | Static (C-04 only) |
| Media + text | Media/text | section[ image + text ] | Static |

**Note on direct-answer blocks:** On Canvas pages the "direct answer" describes the *page/section purpose*, not a verbatim copy of a node's `field_direct_answer`. Canonical direct answers stay on the node. This preserves the non-goal boundary (Canvas ≠ canonical answer content).

---

## Component Boundary Decisions

| Decision | Verdict | Rationale |
|---|---|---|
| Build custom Canvas Code Components? | **No (alpha)** | Code components require npm + Workbench + a JS build/upload pipeline. Violates "keep the alpha lean / avoid a build pipeline." Stock primitives cover all four pages. |
| Live node data via JSON:API in a code component? | **No (alpha); named future-state** | Cost: component definition + metadata + `getPageData`/JSON:API data fetching + build pipeline + Canvas-data-fetching review. Defer. Static props ship now. |
| One reusable "service card grid" component? | **No** | Static-prop cards are per-page; no shared logic worth extracting without live data. |

**Explicit cost flag:** A `geo_service_grid` code component fetching published Services via JSON:API would keep card grids fresh automatically. Cost ≈ 1 component definition + metadata schema + data-fetching code + Vite/Workbench build + upload step + `canvas-data-fetching` review cycle. **Recommendation: defer to a post-alpha milestone.**

---

## Page Plans (sequenced by priority: C-01 → C-03 → C-02 → C-04)

### C-01 — Homepage *(highest priority — basis for Marketplace screenshot + demo URL)*

- **Purpose / audience:** First impression for evaluators (site builders, decision-makers). Introduces the starter and routes to primary subjects and proof content.
- **Decision:** Reuse the existing shell UUID `45000000-…001`. To make the demo URL the site root, the recipe must set the front page via a **`system.site` config action** on `page.front` (the recipe currently only sets `system.theme`) — and because the front page must point at a *content entity shipped as recipe content*, the content import must precede the config action and the canvas_page's internal path must be known at recipe-author time (drupal-critic M4). **If that ordering is fragile, drop the "site root" framing and use the `/geo-starter` alias as the demo URL instead.** **DECIDED (Alex, 2026-06-05): config action route**, gated on empirical apply-proof on the throwaway DDEV install — note the M4 framing inverts the actual recipe order (config actions run *before* content import within a single recipe), so the gate verifies core tolerates the not-yet-existing `/geo-starter` alias at action time; haven sets `page.front` the same way. Fallback stands as written if the apply breaks. Either way the demo/preview URL is a Marketplace blocker (`LIMITATIONS.md:31`), so it must be explicit, not assumed.
- **Composition (in order):**
  1. **Hero** — heading "Drupal is the CMS for an age of agents"; subhead ("governed, source-backed content people can read and retrieval systems can inspect"); primary button "Explore the content model"; optional image (real alt text).
  2. **Direct-answer block** — heading "What this starter gives you"; three-pillar value prop (structured fields, visible sources, editorial governance).
  3. **Card grid — Primary subjects (4 cards)** — one per `topic` subject, linking to the C-03 hub.
  4. **Card grid — Featured services (4 cards)** — one per Service node; title = node title, summary = `field_summary`, href = node alias.
  5. **Card grid — Answer teasers (3–4 cards)** — selected Answer nodes; title = question, href = `/answers/…`.
  6. **Evidence/source list** — heading "Every claim is sourced"; 2–3 links to `/sources/…` Evidence nodes. The GEO money shot: visible provenance.
  7. **CTA** — button "See a source-backed service page" → `/apply-emergency-food-and-utility-assistance` (actual alias, verified in `content/node/41000000-…001.yml:43` — not `/services/…`).
- **GEO demonstration:** Pillars stated; cards route to structured nodes; explicit Sources block proves visible provenance; CTA lands on a node showing `field_reviewed_date` + sources.
- **Acceptance checks:** php:eval load `canvas_page` UUID `…001`, assert non-empty component tree; curl front page → 200; grep rendered HTML for "age of agents", a Service title, an Evidence Source title; anonymous JSON:API canvas_page collection behaves; screenshot 1280×900 (Marketplace candidate) + responsive 375px.
- **Review checkpoint:** `drupal-critic` → `canvas-component-composability`, `canvas-styling-conventions`; then `a11y-planner`/`ui-critic` for the screenshot-grade hero.

### C-03 — Service-area hub *(second — concretely shows content-relationship value prop; reuses C-01 card patterns)*

- **Purpose / audience:** Residents/partners (and evaluators) seeing how one subject groups related services + answers + sources. Pick **one** subject for the alpha: **Benefits and assistance** (richest).
- **Alias:** `/topics/benefits-and-assistance` (new `canvas_page`, UUID `45000000-…002`).
- **Composition:** Hero "Benefits and assistance" → direct-answer block (what it covers / who for) → card grid Services in subject (emergency assistance `41…001`, water bill `41…004`) → card grid Answers (`42…001`–`…004`) → evidence/source list (Benefits Guide `40…001`, Utility Assistance Policy `40…003`, Appeal Process FAQ `40…006`) → CTA → service `field_next_action` URI.
- **GEO demonstration:** Clearest proof of the structured-relationship thesis — one hub assembling typed, sourced, governed content without duplicating it.
- **Acceptance checks:** php:eval load UUID `…002`; curl `/topics/benefits-and-assistance` → 200; grep for "Benefits and assistance", an answer title, "Appeal Process FAQ"; screenshots desktop + 375px.
- **Review checkpoint:** `drupal-critic` → `canvas-component-composability`.

### C-02 — Migration landing page *(third — ties to README/positioning narrative)*

- **Purpose / audience:** Teams migrating from headless/composable or page/post CMS stacks. Shows source patterns landing in Drupal structures (per MIGRATION_MAP).
- **Alias:** `/migrate` (new `canvas_page`, UUID `45000000-…003`).
- **Composition:** Hero "Bring your content into a governed home" → direct-answer block ("GEO Starter is a migration *destination model*, not a turnkey importer" — honest scope per LIMITATIONS) → card grid Source→Drupal mapping (4 cards from MIGRATION_MAP: Homepage→Canvas page; Service/offering→Service node; FAQ/reusable answer→Answer node; Evidence/source→Evidence Source node, each linking to a real example node) → evidence/source list → CTA → `/request-home-repair-permit-record`.
- **GEO demonstration:** Migration preserves structure + provenance, not just visual pages.
- **Acceptance checks:** php:eval load UUID `…003`; curl `/migrate` → 200; grep for "destination model", "Service node", a real Service title; screenshots.
- **Review checkpoint:** `drupal-critic` → `canvas-component-composability`; `copy-critic` for the non-goal/scope wording (must not over-claim).

### C-04 — Campaign page *(fourth — thinnest, most boilerplate)*

- **Purpose / audience:** Demonstrates time-sensitive public messaging (seasonal/urgent), per MIGRATION_MAP "Campaign page → Canvas page."
- **Alias:** `/campaign/winter-utility-help` (new `canvas_page`, UUID `45000000-…004`).
- **Composition:** Alert/callout (Phase 0 must confirm; else styled card; severity "info"; clearly fictional/demo deadline) → hero/media+text campaign headline → direct-answer block ("who this is for, what to do now") → CTA button-group (primary "Get help with a past-due water bill" → `/get-help-past-due-water-bill`; secondary "Read eligibility" → an Answer alias) → evidence/source list (Utility Assistance Review Policy `40…003`).
- **GEO demonstration:** Even urgent campaign messaging stays sourced and routes to governed, reviewed content.
- **Acceptance checks:** php:eval load UUID `…004`; curl `/campaign/winter-utility-help` → 200; grep for campaign heading + "Get help with a past-due water bill"; screenshots. If using a real callout component, confirm its ARIA role in rendered HTML.
- **Review checkpoint:** `drupal-critic` → `canvas-styling-conventions`; `a11y-planner` for the alert pattern (role="status"/"alert", not color-only severity).

---

## Composability Architecture

```
canvas_page (C-01 / C-02 / C-03 / C-04)
└── section / column container         ← Phase 0 must confirm exists
    ├── hero (or heading+text+image+button)
    ├── section[ heading + text ]        (direct-answer block)
    ├── card-grid / section
    │   ├── card (static props: title, summary, href → node alias)
    │   ├── card ...
    ├── section[ heading + (card|text per source → /sources/…) ]  (evidence list)
    ├── alert / callout (C-04 only)
    └── button / button-group (CTA)
```
- **Data flow:** props-down only. No live data, no parent-child fetching.

## Styling & Design Tokens

Use **Olivero's tokens / Canvas defaults only**. Do not hardcode hex/px in component props. Do not introduce a token system in this task — that is theme-lane work (`drupal-planner.theme`). Components must render acceptably under Olivero; the four pages are not the place to invent a design language.

## Upload / Deploy Pipeline

| Step | Action | Validation |
|---|---|---|
| 1 | Author page in Canvas UI on scratch DDEV (Phase 0 loop) | Renders in preview |
| 2 | Export `canvas_page` entity → content YAML | Tree present in YAML |
| 3 | Place at `content/canvas_page/<uuid>.yml`; declare node/term `depends` if the tree references entities (heed the VALIDATION.md `depends` lesson) | YAML lint passes |
| 4 | Pin `drupal/canvas: 1.4.1` in `composer.json` | composer validate |
| 5 | `drush recipe apply` on fresh install | All 4 routes → 200 |
| 6 | Per-page acceptance (above) | All checks pass |

**Risk:** a Canvas minor upgrade could invalidate committed trees — mitigated by the exact pin; flag in LIMITATIONS that Canvas pages are tied to 1.4.1 for the alpha.

## Accessibility Plan

| Page | Concern | Requirement |
|---|---|---|
| All | Heading order | Single H1 (hero), logical H2 per section block |
| All | Card links | Discernible text (not "read more" alone); href to real aliases |
| All | Images | Real `alt`; decorative images empty alt |
| C-04 | Alert/callout | `role="status"` (not `alert` unless truly urgent); severity not color-only |
| All | Contrast / focus | Inherit Olivero AA tokens; verify focus visible at keyboard check |

Full a11y review is a separate gate (`a11y-critic`) and a Marketplace blocker (WCAG 2.2 AA) — not closed by this task.

## Next Steps

- **Run Phase 0 first.** It is a gate. If containers are missing, downgrade to flat single-column or defer the Canvas demo and screenshot the working Service page instead — and say so in LIMITATIONS.
- **Implement in priority order:** C-01 → C-03 → C-02 → C-04.
- **Theme integration:** `drupal-planner.theme` owns the eventual GEO design tokens; this task stays Olivero-default.
- **Future-state (not alpha):** `geo_service_grid` JSON:API-backed code component to make card grids live — costed above; defer.

**Negative space (what this plan does NOT cover):** no Canvas↔Paragraphs conversion; no mixing on canonical pages; no AI/MCP/agent workflow; no JSON-LD/schema templates; no design-token system; no live node data; no Marketplace submission readiness; no full WCAG audit. It does NOT assert the four pages are buildable until Phase 0 confirms the container component inventory.
