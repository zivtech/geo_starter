# GEO Starter — Beta Readiness Plan (1.0.0-beta1)

> Planning artifact, 2026-06-04 (rev. 4 — same-day rev. 2 after
> proposal-critic review; rev. 3 after WS-A Phase 0 ran and the **default
> theme moved Olivero → Mercury** per its findings, see
> `2026-06-04-canvas-phase0-gate-results.md`; rev. 4 on 2026-06-05:
> front-page decision resolved — config action route, see WS-A and §6;
> rev. 5 on 2026-06-07: WS-E decisions resolved + release-state truth
> pass — see §5 note, §6, §8 note).
> Gap analysis + sequencing from `1.0.0-alpha4` (shipped, released, project page
> synced) to a defined `1.0.0-beta1` bar.
> This plan does NOT restate the existing track documents — it sequences them:
> `docs/PUBLISHING_AND_ACCEPTANCE_PLAN.md` (four tracks, Marketplace gates),
> `docs/TECHNICAL_ACCEPTANCE_PLAN.md`, and the three 2026-05-30 feature plans
> in `docs/plans/` (canvas sample pages, paragraph component library, JSON-LD
> emission). Read those for the *how*; this document decides the *what* and
> *in which order* for beta.

**Beta bar decision (Alex, 2026-06-04):** all four debatable scope items are IN —
Canvas homepage + sample pages, section rendering/design pass, minimal editorial
dashboard, public demo URL — plus the non-negotiable gates listed under
"Beta bar" below.

---

## 1. What beta means here (the stability contract)

"Beta" must promise something, or it is just a bigger alpha. The tension to
resolve: alpha4 itself shipped a breaking storage change (free-text
`field_section_hours` → `office_hours`, fresh-install-only), and the
site-template ADR posture is **no update paths** for recipes. Beta cannot
promise in-place upgrades. It can and should promise discipline:

**Contract — recipe (`drupal/geo_starter`), from `1.0.0-beta1`:**

1. **Fresh install remains the only supported path.** Recipes are apply-once
   configuration artifacts; no `hook_update_N`-style migration ships. This is
   the documented Drupal CMS site-template posture, not a project deficiency.
2. **Content-model freeze, additive-only.** No field deletions, no field type
   or storage changes, no vocabulary restructuring between beta releases.
   New optional fields/bundles may be added. The freeze protects existing
   installs' data models; anything that would break one is deferred to 2.x.
3. **Breaking-change documentation duty.** If rule 2 must be broken
   (security or data-integrity grounds only), the release notes carry an
   explicit manual migration note, as the alpha4 `office_hours` note did.
4. **Sample content is exempt.** Demo content in `content/` may change freely
   between betas.

**Contract — module (`drupal/geo_starter_jsonld`), from its `1.0.0-beta1`:**

1. **Frozen within 1.x:** the **top-level entity type set** (`Service`,
   `Question`/`Answer`, `Article`, `CreativeWork`, `WebPage`, `FAQPage`,
   `HowTo`, `ItemList`), the **`@id` scheme** (`{url}` WebPage,
   `{url}#evidence-source` CreativeWork), and the **parity rule** (never emit
   beyond visible rendered HTML). New top-level types may be added.
2. **Not frozen:** nested helper sub-objects (`Person`, `Organization`,
   `Review`, `Audience`, `ContactPoint`, `PostalAddress`,
   `OpeningHoursSpecification`, `HowToStep`, `ListItem`, …) and intra-graph
   **property placement**. schema.org-correctness fixes may move or correct
   properties within the `@graph` (precedent: module alpha2 relocated
   `reviewedBy`/`about`/`citation`/`dateModified` to the `WebPage` for domain
   correctness). Every such change is documented in release notes and covered
   by a regression test.
3. **Settings keys are stable;** new keys default to current behavior.

*Why the escape hatch is load-bearing:* B5 (external validation) exists
precisely to surface schema.org violations the offline check missed. A freeze
with no correctness carve-out would be broken by the first Rich-Results
finding — an unkeepable promise is worse than a narrower honest one.

**Security-advisory posture (set expectations, not a blocker):** SA coverage on
drupal.org requires a stable release + opt-in. Both packages remain in the
prerelease advisory state through beta. `PUBLISHING_AND_ACCEPTANCE_PLAN.md`
already treats this as expected; the beta release notes should say it out loud.

**Version cadence (explicit choice):** the two packages tag `1.0.0-beta1` in
lockstep for the simpler release narrative. The alternative — letting the
module race ahead to stable on its own cadence (Marketplace ultimately
requires a *stable* module dependency) — is deliberately deferred: lockstep
holds while the emission surface is still absorbing WS-D findings; revisit at
beta2 if the module proves quieter than the recipe.

---

## 2. Current state (verified, 2026-06-04)

Two-package train, both released on drupal.org:

| Package | Version | Verified state |
|---|---|---|
| `drupal/geo_starter` | 1.0.0-alpha4 | 10 paragraph bundles import with fields on fresh install; sample content for faq/step_list/card_grid/contact_panel; acceptance probe 23/23; project page synced 2026-06-04 |
| `drupal/geo_starter_jsonld` | 1.0.0-alpha3 | 5 normalizers + 3 contributors + hours mapper; PHPUnit Unit/Kernel/Functional (+1 FunctionalJavascript) in Drupal.org GitLab CI; offline schema.org domain check 0 errors (all-types sweep); requires recipe alpha4+ |

Known dependencies between them: module alpha3 requires the recipe's
`office_hours` field shape (recipe alpha4+) — coupling is defensive at runtime
(module emits nothing when the field is absent); the module does not require
the recipe. Release ordering rule (from `docs/SESSION_HANDOFF.md`): **module
tags/releases first, recipe second** — the recipe's `composer.json` must never
reference a module version that is not yet installable. (Enforcement is tag
existence, not the `^1.0` constraint.)

---

## 3. Beta bar (scope, decided)

**In (decided 2026-06-04):**

- B1. Canvas sample pages: Phase-0 gate + C-01 homepage, C-02 migration
  landing, C-03 hub, C-04 campaign (per the canvas plan).
  **✅ COMPLETE 2026-06-05** — all four pages shipped with critic rounds
  (C-01 `db32083`/`fcd7093`, C-03 `022edb9`/`c1a5d1b`, C-02 `8799560`/
  `4c0a748`, C-04 `ba7a0bd`/`1148eaa`); every page fresh-install gated
  with owner-only round-trip deltas.
- B2. Section rendering/design pass for all ten bundles in **Mercury**
  (rev. 3; was Olivero) + sample content for `section_cta`, `section_alert`,
  `section_media_text`.
  **Samples sub-item ✅ COMPLETE 2026-06-05** (`44a117c` alert+cta,
  `4c6c711` media_text w/ project-original diagram via the recipe's first
  `content/file`+`content/media` shipment; drupal-critic SHIP, two minors
  applied). Placement note: the shipped `field_sections` allow-list
  permits these bundles on service+article only — NOT answer (config
  beats the plan's allow-matrix). **The rendering/design pass remains
  open** — that is the bulk of B2.
- B3. Minimal editorial dashboard: one content-overview View (filter by type,
  moderation state; shows reviewed date).
  **✅ COMPLETE 2026-06-06** — `views.view.geo_content` at
  `/admin/content/geo`, gated on the `access content overview` permission
  (Drupal CMS `content_editor` carries it; no invented role). Grouped
  moderation filter (core's dynamic ModerationStateFilter exposed select is
  broken on this multi-workflow install — documented in LIMITATIONS).
  drupal-critic FIX-THEN-SHIP: 2 MAJOR (taxonomy_index topic filter hid
  unpublished nodes → refiled on node__field_topic; unbounded type filter
  leaked the page bundle → bundle lock + reduced dropdown) + 2 minor
  (workflow config dep, orphaned uid relationship) — all four fixed and
  re-gated, including a discriminating unpublished-node probe test.
- B4. Public demo/preview URL (hardened — see WS-E DoD).
- B5. External structured-data validation: hosted schema.org validator +
  Google Rich Results Test across all four node types (code-snippet mode
  before the demo URL exists; URL mode against the demo once B4 lands).
- B6. Sitemap + internal search proof (publishing plan Track 3, gate 7) +
  **JSON:API access re-proof** on the new beta surfaces (Track 3, gate 6:
  published-200/draft-403 after canvas pages and new paragraph samples exist).
- B7. Accessibility spot-check: keyboard navigation + color contrast on the
  homepage, one Service page, and the dashboard (Track 3, gate 5 — NOT full
  WCAG 2.2 AA evidence; that stays Marketplace-track).
- B8. Stability contract (§1) adopted and documented in README + release notes.
- B9. Docs truth-alignment at release (README, LIMITATIONS, project page,
  VALIDATION) — enumerated, not gestured (see WS-G step 4).

**Out (explicit negative space — beta does NOT include):**

- Finished GEO-specific theme / design system (Mercury + its stock component
  styling is the beta ceiling; rev. 3 — was Olivero + presentation pass).
- Turnkey source-CMS importer automation.
- Full WCAG 2.2 AA / performance / responsive evidence packs (Marketplace).
- Marketplace submission itself (needs stable deps incl. a stable
  `geo_starter_jsonld`, named support owner, final imagery — see
  `MARKETPLACE_SUBMISSION_PACKET.md`).
- Drupal AI / agent-write workflows, MCP, RDF.
- Live-data Canvas code components (named future-state in the canvas plan).
- Answer `FAQPage` default flip, `emit_howto` expansion beyond current gates,
  or any new JSON-LD types beyond §1's frozen set.

---

## 4. Gap analysis → workstreams

Each workstream cites its governing doc; details live there.

### WS-A — Canvas pages (B1) — governing doc: `2026-05-30-canvas-sample-pages-plan.md`
- **Gap:** one empty Canvas shell (`45000000-…0001`, alias `/geo-starter`);
  zero composed pages.
- **Phase 0 ran 2026-06-04 — gate PASSED** (see
  `2026-06-04-canvas-phase0-gate-results.md`): authoring loop proven
  (UI compose → `content:export` → recipe re-import → byte-identical tree);
  component inventory green **under Mercury** (decision: default theme moved
  Olivero → Mercury; `recipe.yml` + `composer.json` updated). **New WS-A work
  item:** site chrome — Mercury ships no `config/optional` blocks (its
  navbar/footer are Canvas components); follow the `haven`/`byte` Drupal CMS
  2.x site-template pattern. Residual 0b caveat: sibling-order and
  slot-nesting round-trip not yet exercised (single-component proof) — diff
  the first multi-component C-01 export before committing it.
- **Phase 0 is a beta-blocking gate** (component inventory + authoring-loop
  proof on a throwaway DDEV install). Its documented fallbacks change shape
  under the beta bar: the *flat single-column* fallback is acceptable for
  beta **with a named quality floor** — hero block, at least one card/list
  grouping of canonical links, a visible sources section, and passing the
  WS-B "no unstyled output" assertions; the *"defer C-01..C-04 entirely"*
  fallback is NOT acceptable (B1 is in the bar) — if Phase 0 fails hard,
  this plan must be re-scoped (escalate; options: code component route with
  its build-pipeline cost, or renegotiate B1).
- Phase-0 sub-risk from session history: `drush content:export` silently
  mangles some entity exports (proven for paragraphs). Phase 0b explicitly
  proves the canvas_page export/import loop before any page is built; if the
  loop fails, STOP per the canvas plan.
- **Constraint policy (RESOLVED here, supersedes the canvas plan's exact-pin
  mandate):** keep `drupal/canvas: ^1.4`. Exact pins are forbidden by the
  recipe's own committed gates (`TECHNICAL_ACCEPTANCE_PLAN.md` "No exact
  dependency pins"; `PUBLISHING_AND_ACCEPTANCE_PLAN.md` "no pinned dependency
  versions" for site templates). Phase 0 re-validates the committed component
  tree against the **current** 1.4 minor; an exact pin may be used only as a
  throwaway local-validation aid, never committed. The residual risk the
  canvas plan correctly flags — a Canvas minor invalidating committed trees —
  is documented in LIMITATIONS instead of being papered over by a
  non-compliant pin.
- **Front-page decision (RESOLVED rev. 4 — Alex, 2026-06-05): C-01 becomes
  the front page via a `system.site` config action (`page.front:
  /geo-starter`), gated on empirical apply-proof before commit.** Ordering
  reality the gate must confirm: within a single recipe, config actions run
  *before* content import, so the action necessarily references an alias
  that does not yet exist at action time. The gate verifies core accepts
  that (`simpleConfigUpdate` does not run the SiteInformationForm's
  route-existence validation — confirm) and that `/` renders the
  canvas_page after import; haven sets its front page the same way, so the
  pattern check against haven's `recipe.yml` is part of the gate. The
  alias is known at author time: `/geo-starter`, verified in
  `content/canvas_page/45000000-…001.yml`. Fallback if apply breaks: keep
  the `/geo-starter` alias as the demo URL and re-surface the decision.
  **Gate result (2026-06-05): PASSED** on fresh
  `site:install recipes/geo_starter` — clean apply; `/` renders the canvas
  shell with chrome (200, `path-frontpage`, canonical `/geo-starter`);
  `/geo-starter` 301-canonicalizes to `/`; JSON-LD spot-check intact; the
  dotted-key action form (`page.front: /geo-starter`) leaves `page.403/404`
  untouched (a nested `page:` map would replace the whole map — why haven
  sets 404+front together). Observed: install end-state stores `page.front`
  as the entity's internal path `/page/1` (rewriter unattributed; canvas and
  recipe_installer_kit code searched clean) — harmless, both forms resolve
  to the shell per-install; the committed action keeps the stable alias
  form. Action committed to `recipe.yml`.
- DoD: all four routes return 200 on a fresh install; component trees import
  from `content/canvas_page/`; front-page decision implemented; screenshot
  refreshed from the real homepage (`screenshot.webp` replacement is a beta
  deliverable — it currently shows the Service page — and re-triggers
  Track-3 gate 9).

### WS-B — Section rendering pass + missing samples (B2) — governing doc: `2026-05-30-paragraph-component-library-content-model-plan.md`
- **Gap:** bundles validated for *import*, not for *presentation*; three
  bundles (`section_cta`, `section_alert`, `section_media_text`) have no
  sample content.
- Author the three samples as hand-written `content/` YAML with `depends`
  declarations on their host nodes — the `content/` directory is the single
  source of truth. **Do NOT use `drush content:export` for paragraph content
  (exporter silently drops paragraphs / writes raw target_ids), and do NOT
  edit or run `tools/create-alpha-sample-content.php`** (dev-only
  delete-then-recreate script; running it wipes and recreates all sample
  nodes).
- Rendering pass: Mercury-compatible Twig/templates or view-display tuning
  (rev. 3; was Olivero). Smoke status under Mercury, eyeballed via screenshot:
  all content present + full JSON-LD graph intact, but the node page is a
  flat field dump — labels visually indistinguishable from values, no spacing
  hierarchy. Same gap as under Olivero; the pass is real styling work, not a
  tune. Text formats stay `content_format` (no `basic_html` in this install).
- DoD (testable as written): every bundle visible on at least one sample
  page; rendered output contains **no raw field machine names and no
  unrendered `target_id` values**; each bundle's configured fields render
  through their view-display settings (labels/wrappers as configured);
  desktop (1280px) + mobile (375px) screenshots captured per bundle; JSON-LD
  probe re-run **23/23 after template work** (rendering changes must not trip
  the parity guards — a hidden field suppresses emission by design).

### WS-C — Minimal editorial dashboard (B3)
- **Gap:** "Views and editor dashboards are future work" (project page).
- Scope discipline: ONE View (`/admin/content/geo`) — columns: title, type,
  moderation state, reviewed date, topic; exposed filters: type, moderation
  state, topic. Config-only, exported to `config/`. No custom module, no
  dashboards beyond this.
- **Open prerequisite to verify first:** which editorial role gates it. The
  recipe currently ships no custom editorial role config; identify whether a
  Drupal CMS-provided role (e.g., from `drupal_cms_admin_ui`) carries the
  needed permissions or whether the View's access must key on a permission
  (`view any unpublished content` / moderation permissions) instead of a
  role. Resolve before building; do not invent a new role without need.
- DoD (testable as written): View imports on fresh install at
  `/admin/content/geo`; exposes a working `moderation_state` filter that can
  isolate `needs_review`; shows title/type/moderation-state/reviewed-date
  columns; returns 403 for anonymous and for a non-editorial authenticated
  account; returns 200 for the chosen editorial role/permission.

### WS-D — External validation (B5) — governing doc: module README "Release validation"
- **Gap:** offline domain check done; hosted validator + Rich Results never run.
- Phase 1 (no demo needed): paste rendered HTML of all four node types into
  validator.schema.org and the Rich Results Test (code-snippet mode). Sweep
  ALL types — the 2026-06-01 single-node lesson (an Answer-only violation
  survived a single-node check) is the reason this is a hard rule.
  **Phase 1 green is a hard precondition of the module `1.0.0-beta1` tag**
  (WS-G step 2) — do not freeze the contract before the snippet-mode pass.
- Phase 2 (after WS-E): re-run Rich Results in URL mode against the demo.
  Accept openly: a Phase-2 finding after the module tag triggers
  `geo_starter_jsonld 1.0.0-beta2` under the §1 correctness carve-out — a
  known branch, not a failure of the plan.
- Honest-claims rule: eligibility *results* (e.g., FAQ rich result eligible)
  may be documented as observed outcomes, never promised as guarantees.
- DoD: zero errors on validator.schema.org for all four types; Rich Results
  findings recorded in `docs/VALIDATION.md`; any violations fixed in the
  module and covered by a regression test before its beta tag.

### WS-E — Public demo URL (B4)
- **Decisions (RESOLVED rev. 5 — Alex, 2026-06-07):** indexability =
  **crawlable-by-design** (the GEO-dogfood default); hosting = **existing
  Zivtech infrastructure** (specific host/hostname chosen after the
  released-artifact install proof). Build source amended: the beta1 tags
  now exist, so the demo builds from **released artifacts** — module beta1
  resolved from packages.drupal.org (its release node is a hard
  prerequisite) + the recipe at tag `1.0.0-beta1` copied into `recipes/`.
  Site templates are NOT served by the p2 composer facade (verified
  2026-06-07; `drupal/haven` 404s identically), so the recipe-from-checkout
  step is the documented site-template reality, not a shortcut. This
  supersedes the pre-tag-branch device below. The sample-content
  disclosure required by the indexability decision lives **on the demo
  instance only** — never in the recipe's `content/` (that would ship demo
  copy into every user's install).
- **Gap:** none exists; Marketplace eventually requires one; URL-mode Rich
  Results and shareable proof both want it.
- Decision needed (§6): hosting. Candidate paths, cheapest first:
  (a) a small VPS/cloud instance with the recipe applied, (b) Tugboat/preview-
  style hosting, (c) other managed preview. Choose for *longevity through
  beta*; the Marketplace preview URL can be revisited at submission time.
  The demo is built from the **pre-tag recipe branch build** (see §5) — that
  is what breaks the apparent demo↔release circularity.
- **Indexability decision (§6):** this is a GEO starter — a crawlable demo
  *dogfoods the thesis* (answer engines can only cite what they can fetch).
  Default recommendation: indexable, with the demo's sample-content nature
  stated on its homepage. If indexable, treat content staleness (below) as a
  visible-quality concern, not a nicety.
- DoD (security items are hard requirements): public URL serves homepage +
  sample content anonymously over HTTPS; JSON-LD visible in page source;
  **admin password is rotated to a non-default secret (never
  `--account-pass=admin`)**; no seed/test credentials exist; anonymous
  requests to draft content and admin routes are denied (verified, not
  assumed); indexability decision implemented (robots/noindex or
  deliberately crawlable); demo has a named owner + refresh/teardown note;
  URL recorded on the project page under a "Try it" heading.

### WS-F — Sitemap/search + JSON:API re-proof + a11y spot-check (B6, B7)
- After WS-A/WS-B land (routes and rendering final). Sitemap: verify
  `drupal_cms_seo_basic`'s sitemap covers the canonical types + canvas pages;
  search: prove internal search returns sample content.
- **JSON:API access re-proof (Track-3 gate 6, mandatory):** the last recorded
  published-200/draft-403 proof predates every surface beta adds. Re-run it
  after canvas_page entities and the three new paragraph samples exist:
  published canvas_page/node 200 anonymously; draft canvas_page 403; paragraphs
  hosted by unpublished parents not anonymously reachable through any
  JSON:API collection. The JSON-LD probe does NOT cover this (emission is
  published-only by design; it never exercises the draft-403 path).
- A11y: keyboard-only walk + contrast check on homepage, one Service page,
  dashboard.
- DoD: sitemap URL returns the expected routes; search finds a known sample
  node; JSON:API matrix above recorded in `docs/VALIDATION.md`; keyboard walk
  completes with no traps; contrast failures fixed or ticketed with severity
  noted in LIMITATIONS.

### WS-G — Release train + docs alignment (B8, B9)
1. Freeze: confirm stability-contract text (§1) in both repos' READMEs.
2. Module first: WS-D **Phase 1 green** + final probe + PHPUnit + domain
   check green → tag/release `geo_starter_jsonld 1.0.0-beta1`. (A WS-D
   Phase-2 finding later triggers beta2 — accepted branch.)
3. Recipe second: fresh-install acceptance (DDEV, re-sync into `packages/` +
   `recipes/` per the established re-validation workflow), probe 23/23, all
   B-gates green, **recipe constraint verified installable via a clean
   `composer create-project` + require before tagging** → roll CHANGELOG →
   tag/release `geo_starter 1.0.0-beta1`.
4. Docs truth-alignment, enumerated:
   - LIMITATIONS lines that **close**: canvas shell-only; cta/alert/media_text
     missing samples + no design pass; sitemap/search unproven; external
     validation pending (if WS-D green).
   - LIMITATIONS lines that **open**: committed Canvas trees validated against
     a specific `canvas` 1.4 minor (un-pinned by policy); static-prop Canvas
     cards go stale if canonical nodes are renamed; demo-content staleness
     posture for the public demo.
   - README/project page/VALIDATION synced (the PROJECT_PAGE_DRAFT.md →
     live-page discipline used 2026-06-04); release notes carry the stability
     contract, the SA-posture note, and the **tested Drupal CMS compatibility
     window** (state the `drupal/cms` + core minor actually validated, e.g.
     "validated on Drupal CMS 2.1.x / core 11.3.x").
5. Stability note (scoped correctly): beta tags only ease installs for
   consumers whose `minimum-stability` floor is `beta` or looser;
   `minimum-stability: stable` projects still cannot install until `1.0.0`.
   The real win is signaling + the §1 contract, not composer reach.

---

## 5. Sequencing

```
WS-B (sections + samples)  ──────────────┐
WS-A Phase 0 (gate) ─→ WS-A C-01..C-04 ──┼─→ WS-F (sitemap/search/JSON:API/a11y) ─→ WS-G (release train)
WS-C (dashboard, independent) ───────────┤
WS-D Phase 1 (snippet validation) ───────┤        WS-D Phase 1 green is a precondition of WS-G step 2
         WS-E (demo URL) ─→ WS-D Phase 2 ┘
```

- Start in parallel: WS-A Phase 0, WS-B, WS-C, WS-D Phase 1.
- WS-A pages need Phase 0 green. WS-E wants C-01 + WS-B done (the demo
  should show the real thing) and is built from the **pre-tag recipe branch**
  (`drush recipe apply` from a checkout) — the release train then cuts tags
  after WS-D Phase 2 runs against it. No circularity.
- WS-F needs final routes/rendering. WS-G is strictly last and strictly
  ordered (module → recipe).
- Suggested release slicing if betas should ship value earlier: WS-D Phase 1
  fixes (if any) can justify a module `alpha4` before the beta train; avoid
  recipe alphas past alpha5 — converge.
- **Rev. 5 reality note (2026-06-07):** the release train ran ahead of WS-E —
  both packages' `1.0.0-beta1` tags were cut and pushed on 2026-06-07 with B4
  (WS-E) and WS-D Phase 2 **deliberately trailing the tags** (Alex, recorded
  decision 2026-06-07). However, drupal.org **release nodes do not exist yet**
  for either beta1 (verified via updates.drupal.org: recipe latest release =
  alpha4, module latest = alpha3) — "released" in WS-G steps 2–3 is therefore
  tagged-not-published. Remaining order: module beta1 release node FIRST
  (nothing can resolve `^1.0@beta` until it exists), then WS-E demo, then
  WS-D Phase 2, then the recipe's release node — holding the recipe release
  until Phase 2 is green restores this section's original
  demo→validate→release intent.

Effort (t-shirt, per session-sized units): WS-A M–L (Phase 0 is the variance);
WS-B M; WS-C S; WS-D S (Phase 1) + S (Phase 2); WS-E S–M (hosting choice
dependent); WS-F S; WS-G S.

---

## 6. Open decisions (decide before the affected workstream starts)

1. **Demo URL hosting** (WS-E): VPS vs Tugboat-style vs other. Owner: Alex.
2. **Demo indexability** (WS-E): crawlable-by-design (dogfoods the GEO
   thesis; recommended) vs noindex. Interacts with the WS-E hardening DoD.
3. **Editorial access mechanism for WS-C**: existing Drupal CMS role vs
   permission-based access. Verify what the recipe's current config actually
   provides before choosing.

(Resolved rev. 2: Canvas constraint policy — `^1.4` stands, exact pins
forbidden by the recipe's committed gates; see WS-A. Resolved rev. 3:
**default theme = Mercury** — decided by Alex 2026-06-04 on Phase 0 evidence;
see `2026-06-04-canvas-phase0-gate-results.md`. Resolved rev. 4: **front
page = C-01 via `system.site` config action** — decided by Alex 2026-06-05,
gated on the WS-A empirical apply-proof; fallback `/geo-starter` alias if
the apply breaks. Resolved rev. 5 — all by Alex, 2026-06-07: decision 1
**demo hosting = existing Zivtech infrastructure**, specific host/hostname
after the released-artifact install proof; decision 2 **demo indexability =
crawlable-by-design**; decision 3 was resolved at WS-C build time —
permission-based access via `access content overview`, no invented role.)

---

## 7. Pre-mortem (what kills this beta)

| Risk | Likelihood | Mitigation |
|---|---|---|
| Canvas Phase 0 finds no usable containers AND flat fallback misses the named quality floor | Medium | Phase 0 runs FIRST; floor defined in WS-A; escalation path named (re-scope B1, do not silently defer) |
| canvas_page content export/import loop not reproducible | Low–Med | Phase 0b STOP-gate before any page authoring; known paragraph-exporter precedent documented |
| Rich Results / hosted validator surfaces schema violations late | Medium | WS-D Phase 1 early + precondition of module tag; Phase-2 finding → module beta2 under the §1 correctness carve-out; regression test per fix |
| Rendering pass breaks JSON-LD parity guards (hidden fields → suppressed emission) | Medium | WS-B DoD requires probe re-run 23/23 after template work |
| JSON:API access regression on new surfaces (draft canvas_page / nested paragraphs leak) | Low–Med | WS-F mandatory re-proof: published-200/draft-403 matrix incl. paragraph-host path |
| Public demo exposed with default credentials or reachable drafts | Medium | WS-E hard DoD: rotated admin secret, draft/admin denial verified, indexability decided |
| Canvas static-prop cards go stale on a public, possibly indexed demo | Medium | Documented LIMITATION at WS-G; demo owner + refresh note in WS-E DoD; live-data code component stays named future-state |
| Another office_hours-style breaking change sneaks into beta | Medium | Stability contract §1 adopted at WS-G freeze; any model change after freeze restarts the freeze |
| Two-package version skew (recipe beta requires unreleased module state) | Low | WS-G strict ordering; clean `composer create-project` install check before recipe tag |

---

## 8. Beta-1 definition of done (roll-up)

> **Rev. 5 status note (2026-06-07):** items 1–4 and 6 are complete in-repo
> and the beta1 tags are cut and pushed; item 5 (demo URL) and the
> "released" half of item 7 (drupal.org release nodes) **deliberately trail
> the tags** — Alex's recorded re-scope decision, 2026-06-07. The roll-up
> below remains the bar for calling beta1 *done*; the tag is not the finish
> line. See the §5 rev. 5 note for the remaining order.

`drupal/geo_starter 1.0.0-beta1` ships when, on a fresh Drupal CMS install
(tested compat window stated in release notes):

1. Recipe applies cleanly; probe 23/23 (re-run post-template-work); all ten
   bundles render with no raw machine names / unrendered target_ids, with
   per-bundle desktop+mobile screenshots (WS-B).
2. Four Canvas pages (or the flat-composition fallback meeting the WS-A
   quality floor) import and return 200; front-page decision implemented;
   `screenshot.webp` replaced from the real homepage (WS-A).
3. Editorial View imports at `/admin/content/geo`, isolates `needs_review`,
   and enforces the 403/200 access matrix (WS-C).
4. All four node types pass hosted schema.org validation with zero errors;
   Rich Results findings documented; WS-D Phase 1 was green before the module
   tag (WS-D).
5. Public demo URL is live, hardened per the WS-E security DoD, indexability
   decided, and linked from the project page (WS-E).
6. Sitemap/search proofs, the JSON:API published-200/draft-403 re-proof on
   all new surfaces, and the a11y spot-check are recorded in VALIDATION.md
   (WS-F).
7. `geo_starter_jsonld 1.0.0-beta1` released first; stability contract and
   SA-posture note in both release notes; LIMITATIONS open/close list applied;
   README/project page synced (WS-G).
