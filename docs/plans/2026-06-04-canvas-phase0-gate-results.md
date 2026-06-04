# Canvas Phase 0 — Gate Results (2026-06-04)

> Decision output required by `2026-05-30-canvas-sample-pages-plan.md` §0c and
> `2026-06-04-beta-readiness-plan.md` WS-A. Run on the reval DDEV project
> (`geostarter-reval-20260529-171053`, Drupal CMS 2.1.2 project template,
> core 11.3.x, PHP 8.4), recipe synced from `main` @ `8666f78`.

## Verdict

**0a (component inventory): FAIL under the recipe as shipped (Olivero) — PASS under Mercury.**
**0b (authoring-loop proof): PASS** (compose in Canvas UI → publish → `content:export`
→ `content/` re-import on a second fresh install → tree survives byte-identical;
see evidence below).
**0c decision: escalated to Alex** — the gate surfaced a default-theme fork
(see "The fork"), not a Canvas-capability failure.

## What the gate found

1. **As shipped (Olivero default), the component library the canvas plan assumed
   does not exist.** `drupal/canvas` 1.4.1 itself ships exactly one SDC
   (`canvas:image`). On the Olivero install, the builder exposes 36 components:
   ~34 block-source (menus, branding, breadcrumbs, views — chrome, not
   composition primitives) plus `sdc.navigation.title` (admin) and
   `sdc.olivero.teaser`. **No heading, no text, no button, no card, no hero,
   no section/container.** The plan's "yes (context7)" rows (`card`, `button`)
   referred to components that ship with **Drupal CMS 2.x's Mercury theme**,
   not with bare `drupal/canvas`.

2. **The components were never missing — the recipe opts out of them.**
   The reval project is the `drupal/cms` 2.1.2 template; **Mercury 1.0.4
   (stable) is already in `web/themes/contrib/`**. SDC discovery is
   theme-bound: `recipe.yml:37` (`- olivero` in `install:`) and `recipe.yml:49`
   (`system.theme` config action `default: olivero`) keep Mercury uninstalled,
   so its SDCs are never discovered or exposed.

3. **With Mercury installed as default (2-line change, tested on a fresh
   install of the modified throwaway recipe copy), the 0a table goes green:**

   | 0a need | Mercury provides | Status |
   |---|---|---|
   | Section/column container | `mercury:section` (header/main/footer slots), `mercury:group`, `mercury:grid` | PASS |
   | Card grid | `mercury:grid` + 5 card variants (image/icon/logo/pricing/testimonial) | PASS |
   | Hero / banner | `mercury:hero-billboard`, `mercury:hero-side-by-side`, `mercury:cta` | PASS |
   | Heading | `mercury:heading` | PASS |
   | Rich text | `mercury:text` | PASS |
   | Image | `mercury:image` (+ `canvas:image`) | PASS |
   | Button | `mercury:button` | PASS |
   | Card | `mercury:card` + variants | PASS |
   | Alert / callout | none dedicated — fallback per plan: styled card / `mercury:badge` / `mercury:blockquote` | PASS (named fallback) |
   | Accordion | `mercury:accordion` + `accordion-container` (bonus: FAQ-relevant) | PASS |

   24 `sdc.mercury.*` component entities, all **enabled by default** (60
   component entities total).

4. **Olivero→Mercury coupling audit:** zero `olivero` references in all 153
   `config/` files. The entire coupling is the two `recipe.yml` lines.

5. **Fresh-install-with-Mercury checks (modified throwaway copy):**
   - Recipe applies cleanly; Service node page 200.
   - **JSON-LD parity holds:** full graph `[FAQPage, HowTo, ItemList, Service,
     WebPage]` emitted, and each emitting paragraph is *visibly rendered*
     (FAQ section, "How to apply" step list → HowTo, "Related answers" card
     grid → ItemList). No machine names / raw target_ids in output.
   - **Chrome finding (pre-existing, NOT a Mercury regression):** the recipe
     composes only `drupal_cms_admin_ui/media/privacy_basic/seo_basic` — not
     `drupal_cms_starter` — and ships no front-end block config. On THIS
     install there are zero front-theme block placements (only claro/gin admin
     blocks) and zero Canvas `page_region` entities. Olivero masked this by
     shipping `config/optional/` blocks (menu, title, branding) that imported
     on theme install; Mercury ships none (its chrome is Canvas-native:
     `mercury:navbar` / `mercury:footer` components). **A Mercury move must add
     a chrome setup step** — the in-tree reference pattern is the `haven` /
     `byte` Drupal CMS 2.x site-template recipes (install `canvas` +
     `canvas_stark`, wildcard-import canvas config, ship footer menu + chrome).

## 0b evidence (authoring loop)

- Composed in Canvas UI on fresh Mercury install: inserted `mercury:heading`
  into the existing `/geo-starter` shell page (Library → component contextual
  menu → Insert), published via Review changes → "Publish 1 selected".
- Published page renders the heading anonymously (curl, no auth).
- `drush content:export canvas_page 1` (numeric id — UUID arg not accepted)
  produces clean, hand-readable YAML: component instance `uuid`,
  `component_id: sdc.mercury.heading`, and ALL input props
  (`heading_text`, `level`, `text_size`, `text_color`, `align`). Nothing like
  the paragraph-exporter mangling.
- Exported YAML placed at `content/canvas_page/<uuid>.yml` in the throwaway
  recipe copy → second fresh `site:install` → **/geo-starter returns 200
  anonymously with the heading rendered, and a re-export of the imported
  entity diffs byte-identical against the original export.** The serialized
  tree survives the full recipe content lifecycle with zero drift.

```yaml
# Exported components field (verbatim):
components:
  -
    uuid: e02cf5dd-74ee-4456-931f-b50cb9f3e715
    component_id: sdc.mercury.heading
    inputs:
      heading_text: 'Your heading goes here'
      level: '2'
      text_size: heading-responsive-4xl
      text_color: default
      align: left
```

- Authoring-pipeline hygiene note: raw export carries `revision_user`/`owner`
  `target_id: 1`; normalize to the conventions the existing `content/` files
  use (owner 0, no revision_user) before committing real pages.

## Eyeball check (screenshots, fresh Mercury install, 1280px)

- **Service node page:** all fields/sections present with Mercury's modern
  typography, breadcrumb works — but it is a **flat field dump** (labels
  render as plain text indistinguishable from values, no spacing system).
  Same as under Olivero; this IS the WS-B rendering pass, unchanged in kind.
- **`/geo-starter` shell:** renders as a **blank white page** anonymously —
  empty component tree + no chrome = nothing visible. This is what fresh
  installs serve today. Chrome (below) + C-01 are what fix it; do NOT refresh
  `screenshot.webp` or cut any demo/tag from this state.

## What this gate did NOT prove (negative space)

- **Sibling order preservation** in the tree (single-component proof; the
  2-component UI insert was time-boxed out). Verify during C-01 authoring —
  first multi-component export must be diffed for order.
- **Slot nesting round-trip** (section-with-children). Same: verify at C-01.
- Full acceptance probe (23/23) under Mercury — only one Service node
  smoke-checked. WS-B re-run required regardless of theme decision.
- Rendering quality of the other 9 paragraph bundles under Mercury (WS-B scope).
- A11y/contrast under Mercury (WS-F scope).
- Mercury chrome composition (navbar/footer) — needed work under the Mercury
  option, sized by the haven/byte reference pattern.

## The fork (DECIDED 2026-06-04: Option 1 — Mercury, Alex)

Implemented same day: `recipe.yml` 37/49 olivero→mercury;
`composer.json` gains `drupal/mercury: ^1.0` (Mercury is contrib — Olivero
shipped with core and needed no require; without this line the recipe breaks
outside the `drupal/cms` project template). Chrome setup (haven/byte pattern)
is the immediate follow-up work item under WS-A.

This is a **product-identity decision**, not just an unblock: does geo_starter
ride the Drupal CMS 2.x default theme (Mercury) or assert its own (Olivero)?

**Option 1 — Mercury default (recommended).** 2-line `recipe.yml` change +
chrome setup (haven/byte pattern) + WS-B re-targeted to Mercury.
Pros: full component library for C-01..C-04 out of the box; aligns the
site-template with the Drupal CMS 2.1.x default it claims as its compat
window; modern agency-style demo + screenshot; components upstream-maintained;
Mercury is stable (1.0.4). Costs: WS-B rendering pass + probe re-run + a11y +
screenshots all target Mercury; chrome work; beta-plan WS-A/WS-B text amended.

**Option 2 — Stay Olivero, author code components.** Author ~6–8 `js_component`
config entities (heading/text/hero/card/section-with-slot) in-browser, ship as
recipe config. Pros: keeps current rendering validation. Costs: builds and
maintains a parallel component library that duplicates Mercury; dated look;
fights the base platform's direction.

**Option 3 — Re-scope B1.** Named escalation path in the beta plan; shrinks
the beta's headline deliverable. Not recommended while Option 1 is this cheap.

## Superseded / confirmed along the way

- Canvas plan §0d exact-pin (`1.4.1`) stays superseded by the beta plan:
  `^1.4` committed; installed-and-validated minor is 1.4.1.
- WS-C prerequisite (§6.4) verified as a side-finding: the recipe ships **no**
  `user.role.*` config — the editorial View must key on a Drupal CMS-provided
  role or permission, not a geo_starter role.
