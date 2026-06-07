# WS-B — Section Rendering Pass (geo_starter beta1) — Theme Plan

> **For Claude:** drupal-theme-planner protocol. This is an execution plan for a
> DECIDED architecture — do not re-litigate Tier-3 / submodule / Mercury. Invoke
> **drupal-theme-critic** at the review checkpoints in §11, and **drupal-critic**
> on the module submodule before its commit.
> **Drupal Version:** Drupal 11 core (Drupal CMS distribution).
> **Default theme (decided, beta plan rev. 3):** Mercury (`web/themes/contrib/mercury`).

**Scope:** Give the ten Paragraph bundles and the four node field-stacks
*semantic, accessible, lightly-styled* visible HTML — shipped as templates + a
scoped CSS library inside a **new submodule of `drupal/geo_starter_jsonld`**
(working name `geo_starter_jsonld_markup`, finalized in §1) — plus a recipe-side
display-config pass and the one missing `geo_starter_section` sample. The
visible-HTML ↔ JSON-LD parity contract (§ Hard constraints) is a guard, not a
preference: after all work, `tools/jsonld-probe.php` must pass **23/23**.

**Negative space (what this plan is NOT):** not a Mercury fork or subtheme; not a
GEO design system; not a Tailwind-utility dependency; not a Canvas-page change;
not a content-model change (no new fields, no field-type changes, no
field_sections allow-list changes); not a new drupal.org project; not a JSON-LD
emission change. Markup-only transformations of already-rendered field output.

---

## 0. Governing DoD (quoted verbatim from `2026-06-04-beta-readiness-plan.md` §WS-B)

> DoD (testable as written): every bundle visible on at least one sample page;
> rendered output contains **no raw field machine names and no unrendered
> `target_id` values**; each bundle's configured fields render through their
> view-display settings (labels/wrappers as configured); desktop (1280px) +
> mobile (375px) screenshots captured per bundle; JSON-LD probe re-run
> **23/23 after template work** (rendering changes must not trip the parity
> guards — a hidden field suppresses emission by design).

Every assertion in §10 is written so that a broken contract fails red, not green.

---

## 1. Verified grounding (cite, do not re-derive)

All field names, formatters, labels, and allow-lists below were read from
`config/` in `drupal/geo_starter` on 2026-06-06. They are the source of truth;
the 2026-05-30 content-model plan drifted in three places (noted inline).

### 1.1 Theme facts (from the reval test env, beta plan + brief)
- Mercury `base theme: false`. Ships **zero** field/node/paragraph templates →
  those bundles render through **core system module's classless minimal
  templates** (`field.html.twig`, `node.html.twig`, `paragraph.html.twig` from
  `core/modules/system/templates/` — bare `<div>` wrappers). Subtheming is
  **explicitly unsupported** (Mercury `CUSTOMIZING.md`).
- Mercury Tailwind v4 is compiled **from Mercury's own templates only**
  (`build/main.min.css`). Arbitrary utility classes injected via our markup are
  **NOT** in that build → **do not depend on Tailwind utilities**. Our CSS must
  be self-contained.
- Mercury **does** provide, and we **do** consume (with fallbacks):
  - `@layer base` typography for bare `h1`–`h6`, `p` (verified in `src/main.css`
    + compiled `build/`). Our semantic headings/paragraphs inherit it for free.
  - Design tokens as CSS custom properties on `:root` (shadcn-style, oklch),
    verified in `src/theme.css`:
    `--background --foreground --primary --secondary --accent --border --radius`
    plus `--shadow-*` and `--font-*`. Our component CSS consumes these with
    fallbacks → native look under Mercury, graceful elsewhere.
- 27 Mercury SDCs exist (card, accordion, section, grid, hero-*, …) but are
  **Canvas-oriented**. Decision (§5.4): **do not** embed Mercury SDCs from
  paragraph templates — they are undocumented as a public API, would couple our
  markup to a theme we cannot subtheme, and the "render OPEN, no collapse"
  FAQ rule (§ Hard constraints) conflicts with the accordion SDC's collapse
  behavior. Plain semantic markup + our own CSS is the lower-risk, parity-safe
  choice.

### 1.2 Live rendered baseline (curl on the DDEV reval site, flagship service)
Every field renders as `<div><div>Label</div><div>value</div></div>` bare divs;
labels visually identical to values. FAQ questions, step names, section titles
are bare divs — **no** headings/`<ol>`/semantic elements. Editorial labels leak
verbatim ("Reviewed by name", "Reviewed date"). Counts on that page: 0
`field__label`, 0 `field--name-*`, 8 `paragraph--type-*` wrappers (the
paragraphs module's own `paragraph.html.twig` suggestion works), 0 raw
`target_id`, 1 `ld+json` block. **This is the gap WS-B closes.**

### 1.3 Node field-stacks (read from `config/core.entity_view_display.node.*.default.yml`)

`field_sections` is `label: hidden` on all four node types and renders via the
paragraphs system (weight 90, just above `links` at 100). The *visible* leak is
the scalar/reference fields above it, all `label: above`:

| Node | Fields rendered (weight order), all `label: above` unless noted | reviewed-by leak |
|---|---|---|
| **service** (`41…`) | field_direct_answer(1), field_summary(2), field_problem_solved(3), field_audience(4), field_topic(6), field_eligibility(7), field_next_action(8 link), field_evidence_sources(9 ref-label), field_reviewed_by_name(10 string), field_reviewed_date(11 datetime), field_sections(90, hidden) | `field_reviewed_by_name` label = "Reviewed by name"; `field_reviewed_date` label = "Reviewed date" |
| **answer** (`42…`) | field_direct_answer(1), body(2), field_topic(3), field_audience(4), field_related_services(5), field_evidence_sources(6), field_reviewed_by_name(7), field_reviewed_date(8), field_sections(90, hidden) | same |
| **article** (`43…`) | field_summary(1), body(2), field_topic(3), field_audience(4), field_author_name(5), field_reviewed_by_name(6), field_evidence_sources(7), field_related_services(8), field_related_answers(9), field_reviewed_date(10), field_sections(90, hidden) | same + `field_author_name` "Author name" |
| **evidence_source** (`40…`) | field_publisher(1), field_source_url(2 link), field_publication_date(3), field_topic(4), field_source_type(5 list), field_credibility_note(6). **No field_sections.** | n/a |

The page `<h1>` comes from Mercury's page-title template (verified, beta plan
§WS-A). So nodes already have an h1; our sections slot **h2**, items **h3**.

### 1.4 Paragraph view-displays (read from `config/core.entity_view_display.paragraph.*.default.yml`)

All paragraph fields are `label: hidden` already (good — no label leak inside
sections; the leak is node-level only). Exact field stacks and formatters:

| Bundle | Field (weight) → formatter | Notes |
|---|---|---|
| **geo_starter_section** | field_section_kicker(0 string), field_section_heading(1 string), field_section_body(2 text_default) | generic; kicker = eyebrow |
| **section_faq** | field_section_heading(0 string), field_section_items(1 entity_reference_revisions_entity_view → section_faq_item) | nested |
| **section_faq_item** | field_section_question(0 string), field_section_answer(1 text_default) | child; never in field_sections directly |
| **section_step_list** | field_section_heading(0 string), field_section_steps(1 err_entity_view → section_step_item) | nested |
| **section_step_item** | field_section_step_name(0 string), field_section_step_text(1 text_default), field_section_step_image(2 entity_reference_entity_view → media) | child |
| **section_card_grid** | field_section_heading(0 string), field_section_cards(1 **entity_reference_label**, link: true) | cards = node refs rendered as linked labels |
| **section_cta** | field_section_heading(0 string), field_section_body(1 text_default), field_section_link(2 link) | |
| **section_alert** | **field_section_alert_level**(0 list_default), field_section_heading(1 string), field_section_body(2 text_default) | level values: `info\|success\|warning\|danger`, default `info` (drift: plan said `field_section_severity` / `critical`; as-built wins) |
| **section_contact_panel** | field_section_heading(0), field_section_contact_name(1 string), field_section_phone(2 string), field_section_email(3 **email_mailto**), field_section_address(4 text_default), field_section_hours(5 **office_hours_table**), field_section_link(6 link) | hours = office_hours module field, NOT string (drift: plan said string; as-built uses `office_hours` + `office_hours_table` formatter) |
| **section_media_text** | field_section_heading(0), field_section_media(1 entity_reference_entity_view → media image), field_section_body(2 text_default). **field_section_media_position is HIDDEN** (in `hidden:` block) | position field exists but is not rendered → left/right is **not** currently expressible from output; see §4.10 |

### 1.5 field_sections allow-lists — **AS-BUILT DRIFT vs the 2026-05-30 plan §7 matrix**

Read from `config/field.field.node.{service,answer,article}.field_sections.yml`.
**Config beats the plan.** This governs which bundle appears on which node, and
therefore the screenshot matrix:

| Bundle | service | answer | article |
|---|---|---|---|
| geo_starter_section | yes | yes | yes |
| section_faq | **yes** | **no** | **no** |
| section_step_list | yes | yes | yes |
| section_card_grid | yes | no | yes |
| section_cta | yes | no | yes |
| section_alert | yes | no | yes |
| section_contact_panel | **yes** | **no** | **no** |
| section_media_text | yes | no | yes |

**Consequence:** `section_faq`, `section_faq_item`, and `section_contact_panel`
are reachable **only on a Service page**. Their DoD screenshots come from the
flagship Service. (The brief's recollection that "FAQ is on answers" is the plan,
not the build; do not chase it.)

### 1.6 Sample-host map (read from `content/`, full reference graph)

| Bundle | Sample paragraph UUID | Host node UUID | Host bundle | Host alias |
|---|---|---|---|---|
| section_faq (+items 46…0011/0012) | 46000000-…-000000000001 | 41000000-…-000000000001 | service | `/apply-emergency-food-and-utility-assistance` |
| section_step_list (+items 46…0022/0023) | 46000000-…-000000000021 | 41000000-…-000000000001 | service | `/apply-emergency-food-and-utility-assistance` |
| section_card_grid (cards → answers 42…0001/0002) | 46000000-…-000000000031 | 41000000-…-000000000001 | service | `/apply-emergency-food-and-utility-assistance` |
| section_contact_panel | 46000000-…-000000000041 | 41000000-…-000000000001 | service | `/apply-emergency-food-and-utility-assistance` |
| section_alert | 46000000-…-000000000051 | 41000000-…-000000000004 | service | `/get-help-past-due-water-bill` |
| section_cta | 46000000-…-000000000061 | 43000000-…-000000000002 | article | `/preparing-complete-assistance-application` |
| section_media_text (media 49…0001) | 46000000-…-000000000071 | 43000000-…-000000000001 | article | `/how-answer-hub-keeps-public-service-pages-reviewable` |
| **geo_starter_section** | **MISSING — this is the gap** | — | — | — |

UUID scheme: nodes `40…`=evidence_source, `41…`=service, `42…`=answer,
`43…`=article; paragraphs `46…`; media `49…`; file `48…`. The two answers
`42…0001` / `42…0002` carry **no** field_sections today (they are card *targets*,
not section hosts). Text format on all sample bodies: `content_format`.

The probe's marquee node is the flagship Service `41000000-…-000000000001`,
which carries the 2-pair FAQ, 2-step list, card grid, and contact panel — i.e.
4 of the 8 bundles render on that one page, and it is the JSON-LD probe's anchor.

---

## 2. Submodule shape (decided architecture — execution detail)

### 2.1 Name & placement
- **Machine name:** `geo_starter_jsonld_markup`.
- **Location:** `geo_starter_jsonld/modules/geo_starter_jsonld_markup/`
  (a submodule directory inside the existing module repo
  `/Users/AlexUA_1/claude/ai-initiative-modules/geo_starter_jsonld`).
- **Why a submodule, not the main module:** emission-only installs must remain
  possible (a consumer who themes elsewhere installs `geo_starter_jsonld` alone).
  The markup submodule is **independently installable/uninstallable**; uninstalling
  it reverts to core's classless rendering (rollback, §12). It depends on the
  parent module so the parity contract stays co-located, but the parent does not
  depend on it.

> **Recipe ↔ module package boundary — READ THIS BEFORE IMPLEMENTING (do not
> revert to a bundled-module assumption).** A Drupal 11 recipe is a configuration
> artifact: it **cannot** discover or install a module placed inside the *recipe*
> package's own directory. That is **not** what we are doing. The submodule lives
> inside the **`drupal/geo_starter_jsonld` Composer package**, which the recipe
> *already* declares as a `require` (verified: `geo_starter/composer.json` line 13,
> `"drupal/geo_starter_jsonld": "^1.0"`). Composer therefore places the parent
> module **and its `modules/geo_starter_jsonld_markup` submodule** into the site
> codebase when the recipe is installed; Drupal's extension discovery finds
> submodules inside an installed module's tree; and the recipe's
> `install: [geo_starter_jsonld_markup]` line (§5.1) is consequently valid. The
> submodule is shipped, versioned, and released **as part of `drupal/geo_starter_jsonld`**
> — there is **no new Composer package** and **no module bundled in the recipe
> package**. The only recipe change is the one `install:` array entry; no new
> `require` is added because `^1.0` on the parent already covers the submodule.

### 2.2 `geo_starter_jsonld_markup.info.yml`
```yaml
name: 'GEO Starter JSON-LD Markup'
type: module
description: 'Semantic, accessible visible-HTML templates + scoped CSS for GEO Starter section paragraphs and node field-stacks. Keeps visible HTML in parity with the JSON-LD emitted by geo_starter_jsonld.'
package: 'GEO Starter'
core_version_requirement: ^11
dependencies:
  - geo_starter_jsonld:geo_starter_jsonld
  - paragraphs:paragraphs
  - drupal:node
```
No `office_hours` dependency is needed (the office_hours formatter renders the
hours value; our template only wraps the already-rendered `{{ content.field_section_hours }}`).

### 2.3 Directory layout
```
geo_starter_jsonld/modules/geo_starter_jsonld_markup/
  geo_starter_jsonld_markup.info.yml
  geo_starter_jsonld_markup.module          # MANDATORY — hook_theme() (§2.4)
  geo_starter_jsonld_markup.libraries.yml   # one library: sections (§6)
  templates/
    paragraph--geo-starter-section.html.twig
    paragraph--section-faq.html.twig
    paragraph--section-faq-item.html.twig
    paragraph--section-step-list.html.twig
    paragraph--section-step-item.html.twig
    paragraph--section-card-grid.html.twig
    paragraph--section-cta.html.twig
    paragraph--section-alert.html.twig
    paragraph--section-contact-panel.html.twig
    paragraph--section-media-text.html.twig
  css/
    sections.css
```
Templates are **flat in `templates/`** (no subdirs) so hook names map to
filenames via `strtr($hook, '_', '-')` without explicit `'template'` path
overrides. This is the simplest correct pattern.

> **Removed from the previous draft:** `node/node--service--provenance.html.twig`
> and `field/field--node--field-reviewed-by-name.html.twig` /
> `field--node--field-reviewed-date.html.twig`. The §4.11 provenance treatment
> is now a **config-relabel** (MAJOR-1 revision, see §4.11); field templates are
> documented as future work requiring a node template, explicitly out of scope.

### 2.4 Template registration mechanism (CORRECTED — `hook_theme()` is MANDATORY for modules)

> **Correction from proposal-critic review (CRITICAL-1):** The original plan
> assumed module templates are auto-discovered via theme suggestion chains
> without a `hook_theme()` entry. This is **FALSE for modules**. Core's theme
> registry (`Registry.php`) calls `drupal_find_theme_templates()` only for
> theme/base-theme branches (the directory-scan fallback is gated on
> `$type == 'theme' || 'base_theme'`, `Registry.php:716`). Modules merge
> **only** their `hook_theme()` output into the registry (`Registry.php:564`).
> A module template placed in `templates/` without a corresponding
> `hook_theme()` entry is **invisible** — silent fallthrough to the core
> template, which is precisely the failure mode this whole pass exists to kill.
>
> `core/tests/Drupal/FunctionalTests/Theme/TwigDebugMarkupTest.php:45-46`
> confirms this: it manually appends the module-path scan precisely because the
> registry never does it for modules.

**The `.module` file is MANDATORY.** `geo_starter_jsonld_markup.module` must
implement `hook_theme()` registering every paragraph suggestion with
`'base hook' => 'paragraph'` (and field suggestions if §4.11 evolves to use
field templates in a future pass). The `'base hook'` key tells the registry to
treat these as suggestions of the existing `paragraph` theme hook — so the
suggestion chain (`paragraph__section_faq`, `paragraph__geo_starter_section`,
etc.) activates properly and the module's templates are selected.

**`hook_theme()` entries — 10 paragraph templates (8 parent + 2 child bundles):**
```php
function geo_starter_jsonld_markup_theme(): array {
  return [
    'paragraph__geo_starter_section' => ['base hook' => 'paragraph'],
    'paragraph__section_faq' => ['base hook' => 'paragraph'],
    'paragraph__section_faq_item' => ['base hook' => 'paragraph'],
    'paragraph__section_step_list' => ['base hook' => 'paragraph'],
    'paragraph__section_step_item' => ['base hook' => 'paragraph'],
    'paragraph__section_card_grid' => ['base hook' => 'paragraph'],
    'paragraph__section_cta' => ['base hook' => 'paragraph'],
    'paragraph__section_alert' => ['base hook' => 'paragraph'],
    'paragraph__section_contact_panel' => ['base hook' => 'paragraph'],
    'paragraph__section_media_text' => ['base hook' => 'paragraph'],
  ];
}
```
Template filenames derive from hook names via `strtr($hook, '_', '-')`:
`paragraph__section_faq` → `paragraph--section-faq.html.twig`. The templates
live flat in the module's `templates/` directory (§2.3); no explicit
`'template'` or `'path'` key is needed when the directory name and filename
follow this convention.

No `hook_theme_suggestions_paragraph_alter()` is required — the alert variant
uses a BEM modifier class from the Twig value read (§4.6/§4.8), not a template
suggestion. The `.module` file's only hook is `hook_theme()`.

> **Task 0 STOP-GATE (elevated from verification step — CRITICAL-2; child path
> added per delta re-check MINOR-2):** before writing any template beyond the
> proving pair, write **TWO** templates — the parent
> (`paragraph--section-faq.html.twig`) **and its child**
> (`paragraph--section-faq-item.html.twig`) — + their `hook_theme()` entries,
> install the submodule, render the flagship Service page with
> `twig_debug: true`, and confirm `THEME DEBUG` marks **both** module templates
> as selected (the TwigThemeEngine `TwigThemeEngine.php:95` emits the
> selected/candidate markers): the parent's `<dl class="geo-faq">` AND the
> child's `<dt class="geo-faq__q">` must appear. The child exercises the nested
> `entity_reference_revisions` render path — a distinct code path from the
> parent; proving only the parent would let a child-only registration typo
> survive to step 2. **If either module template is NOT selected, STOP —
> diagnose the hook_theme() registration before authoring any further
> templates.** Cross-reference: F1 (sections.css present on the section page) is
> the positive counter-signal that the template loaded and attached the library.
> Only after this gate passes: author the remaining 8 paragraph templates. This
> sequence prevents a batch of 10 templates that are all invisible due to a
> registration error.

---

## 3. CSS architecture

### 3.1 Methodology
**BEM-ish, `geo-` namespaced, single component library, token-consuming.** One
file `css/sections.css`. No SCSS build (recipe/module ships compiled CSS; no
asset pipeline introduced). No utilities, no `@media`-query sprawl — one shared
breakpoint (§3.3). Rationale: the surface is ~10 small components; a build step
and a methodology framework would be over-engineering for a beta presentation
floor, and the brief forbids Tailwind-utility dependence.

### 3.2 Token consumption (consume Mercury tokens, always with fallback)
Every color/spacing/radius reference uses `var(--token, fallback)` so the CSS is
native under Mercury and graceful under any other theme:
```css
.geo-section {
  --geo-gap: 1rem;
  margin-block: var(--geo-gap);
  padding-block: var(--geo-gap);
  border-top: 1px solid var(--border, #e2e2e2);
}
.geo-section__kicker {
  font-size: .8125rem; text-transform: uppercase; letter-spacing: .05em;
  color: var(--primary, #1a56db);
}
.geo-alert { border-radius: var(--radius, .5rem); border: 1px solid var(--border, #e2e2e2); }
.geo-alert--warning { /* level-driven accent, fallback colors */ }
```
We introduce a small set of **local** `--geo-*` custom properties (spacing scale,
alert accent map) scoped to `.geo-section` / `.geo-alert` so the component is
self-describing and not silently dependent on Mercury internals.

### 3.3 Breakpoint
One shared breakpoint, mobile-first: base styles are the 375px layout; a single
`@media (min-width: 768px)` block upgrades the two genuinely multi-column
components (`card_grid` → grid; `media_text` → text/media side-by-side). We do
**not** add a `breakpoints.yml` (no responsive-image styles are introduced here;
that is Marketplace-track). Everything else is single-column at all widths.

### 3.4 No inline styles
Per brief + house rules. The only "dynamic" styling is the alert accent, driven
by a BEM modifier class chosen from `field_section_alert_level` **in Twig** (a
class string, not an inline style) — see §4.8.

---

## 4. Per-bundle template specs (exact field names, semantic elements, classes)

Conventions across all paragraph templates:
- Print fields through `{{ content.field_* }}` (NOT `{{ paragraph.field_*.value|raw }}`)
  so the configured formatter, sanitization, and cacheability are preserved.
  **Markup-only:** never add a field not in the view-display, never hide one that
  is shown (parity). Wrap, label semantically, order — nothing else.
- Preserve `{{ attributes }}` on the root element (do not strip — Mercury/contextual
  links/quickedit attach here). Add our class via
  `{{ attributes.addClass('geo-section', 'geo-section--<bundle>') }}`.
- Headings: section title = **h2**; nested item title = **h3**. (Page h1 from
  Mercury page-title.) This fixes the global heading hierarchy a11y requirement.
- Empty-guard with `{% if content.field_x|render|trim %}` before emitting a
  wrapper that would otherwise render an empty labelled box — but **never**
  suppress a field that has content (that would break parity). The guard only
  avoids empty chrome for optional fields.

### 4.1 `paragraph--geo-starter-section.html.twig` (generic)
- Fields: `field_section_kicker` (string), `field_section_heading` (string),
  `field_section_body` (text_default).
- Markup:
  ```twig
  <section{{ attributes.addClass('geo-section', 'geo-section--generic') }}>
    {% if content.field_section_kicker|render|trim %}
      <p class="geo-section__kicker">{{ content.field_section_kicker }}</p>
    {% endif %}
    {% if content.field_section_heading|render|trim %}
      <h2 class="geo-section__title">{{ content.field_section_heading }}</h2>
    {% endif %}
    <div class="geo-section__body">{{ content.field_section_body }}</div>
  </section>
  ```
  Note: `field_section_heading`/`kicker` use the `string` formatter, which wraps
  in its own element; printing `{{ content.field_section_kicker }}` yields the
  formatter's markup. We wrap that in our semantic element. (Acceptable: the
  string formatter emits inline text; the outer `<h2>`/`<p>` carries semantics.)
- CSS: kicker eyebrow, h2 inherits Mercury base typography, body spacing.

### 4.2 `paragraph--section-faq.html.twig` (parent) + `--section-faq-item` (child)
**FAQ MUST render OPEN — no `<details>`/`<summary>` collapsed-by-default.**
Decision + rationale (state in plan and in a template comment): the project's
"no hidden claims" parity rule is stricter than Google's collapsed-FAQ
allowance. A collapsed `<details>` hides the answer text from the initial DOM
paint and creates a visible↔emitted ambiguity (the JSON-LD `acceptedAnswer.text`
is present while the on-screen answer is hidden behind a disclosure). We refuse
that ambiguity. The FAQ renders as a definition list, fully visible.

- Parent fields: `field_section_heading` (string), `field_section_items`
  (entity_reference_revisions_entity_view → renders each `section_faq_item`).
- Parent markup:
  ```twig
  <section{{ attributes.addClass('geo-section', 'geo-section--faq') }}>
    {% if content.field_section_heading|render|trim %}
      <h2 class="geo-section__title">{{ content.field_section_heading }}</h2>
    {% endif %}
    <dl class="geo-faq">{{ content.field_section_items }}</dl>
  </section>
  ```
  The `<dl>` wraps the rendered item list; each item template emits a `<dt>`/`<dd>`
  pair so the `<dl>` is valid.
- Child (`section_faq_item`) fields: `field_section_question` (string),
  `field_section_answer` (text_default). Child markup:
  ```twig
  {# Root paragraph wrapper still applies attributes; render Q/A as dt/dd. #}
  <div{{ attributes.addClass('geo-faq__item') }}>
    <dt class="geo-faq__q">{{ content.field_section_question }}</dt>
    <dd class="geo-faq__a">{{ content.field_section_answer }}</dd>
  </div>
  ```
  **Validity note:** a `<div>` between `<dl>` and its `<dt>`/`<dd>` is permitted
  in the HTML5 living standard (a `<dl>` may contain `<div>` grouping wrappers,
  each containing one `<dt>`+`<dd>`). The paragraph root must carry `attributes`,
  hence the wrapping `<div class="geo-faq__item">`. This is the standards-correct
  way to keep both the paragraph attributes and the `<dl>` semantics.
- a11y: `<dl>`/`<dt>`/`<dd>` give the Q/A pairing programmatic structure; nothing
  is hidden; no ARIA needed (no disclosure widget).
- CSS: question weight/spacing; answer indent; item separators.

### 4.3 `paragraph--section-step-list.html.twig` (parent) + `--section-step-item` (child)
- Parent fields: `field_section_heading` (string), `field_section_steps`
  (renders each `section_step_item`). Use an **ordered list** — steps are
  sequential (this is the HowTo source).
- Parent markup:
  ```twig
  <section{{ attributes.addClass('geo-section', 'geo-section--steps') }}>
    {% if content.field_section_heading|render|trim %}
      <h2 class="geo-section__title">{{ content.field_section_heading }}</h2>
    {% endif %}
    <ol class="geo-steps">{{ content.field_section_steps }}</ol>
  </section>
  ```
- Child (`section_step_item`) fields: `field_section_step_name` (string),
  `field_section_step_text` (text_default), `field_section_step_image`
  (entity_reference_entity_view → media, optional). Markup:
  ```twig
  <li{{ attributes.addClass('geo-steps__item') }}>
    <h3 class="geo-steps__name">{{ content.field_section_step_name }}</h3>
    <div class="geo-steps__text">{{ content.field_section_step_text }}</div>
    {% if content.field_section_step_image|render|trim %}
      <div class="geo-steps__image">{{ content.field_section_step_image }}</div>
    {% endif %}
  </li>
  ```
  **Validity note:** the paragraph root must be the `<li>` so `attributes` land
  on the list item and the `<ol>` has only `<li>` children. Step order = delta
  order = `<ol>` order (matches the HowTo `position` derivation — parity-safe).
- a11y: `<ol>` conveys sequence; `<h3>` per step keeps hierarchy; image alt comes
  from the media entity (already authored: "Diagram: …").

### 4.4 `paragraph--section-card-grid.html.twig`
- Fields: `field_section_heading` (string), `field_section_cards`
  (**entity_reference_label**, `link: true` → renders linked node titles).
- The formatter emits a list of linked labels. Wrap as a `<ul>` of cards; the
  field's own item wrappers become the list — but the `entity_reference_label`
  formatter renders each item in a `<div>`, not `<li>`. To get a real list
  **without** changing the formatter (no config change), wrap the rendered field
  in a styled container and let CSS grid lay the divs out; do **not** fake `<li>`s
  around formatter output we don't control.
- Markup:
  ```twig
  <section{{ attributes.addClass('geo-section', 'geo-section--cards') }}>
    {% if content.field_section_heading|render|trim %}
      <h2 class="geo-section__title">{{ content.field_section_heading }}</h2>
    {% endif %}
    <div class="geo-cards">{{ content.field_section_cards }}</div>
  </section>
  ```
- CSS: `.geo-cards` = `display: grid; gap` ; the formatter's per-item `<div>`s
  become grid cells styled as cards (border, padding, hover). At `min-width:768px`
  → `grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr))`. Mobile = 1 col.
- **Parity note:** `entity_reference_label` already renders the visible linked
  title; JSON-LD `ItemList.itemListElement` carries `url` + `name` of the same
  nodes → visible and emitted match. No truncation/hiding.

### 4.5 `paragraph--section-cta.html.twig`
- Fields: `field_section_heading` (string), `field_section_body` (text_default),
  `field_section_link` (link formatter, renders an `<a>`).
- Markup:
  ```twig
  <aside{{ attributes.addClass('geo-section', 'geo-section--cta') }}>
    {% if content.field_section_heading|render|trim %}
      <h2 class="geo-section__title">{{ content.field_section_heading }}</h2>
    {% endif %}
    <div class="geo-cta__body">{{ content.field_section_body }}</div>
    <div class="geo-cta__action">{{ content.field_section_link }}</div>
  </aside>
  ```
  `<aside>` is appropriate: a contextual, tangentially-related call-to-action.
- CSS: button-style the rendered link via `.geo-cta__action a { … }` consuming
  `--primary`/`--radius` with fallbacks. No schema.org type emitted (parity-safe;
  the link is visible body content).
- a11y: the link is a real `<a href>` from the link formatter (keyboard/focus for
  free); button styling must keep a visible focus outline (do not remove
  `:focus-visible`).

### 4.6 `paragraph--section-alert.html.twig`
- Fields: `field_section_alert_level` (list_default, values
  `info|success|warning|danger`), `field_section_heading` (string),
  `field_section_body` (text_default).
- **Level → modifier class in Twig** (a class string, not inline style). Read the
  raw value to build the BEM modifier; still print the field through its formatter
  so parity holds if the value is ever shown — but here the level is presentational,
  so we render it as a class only and do **not** print `content.field_section_alert_level`
  as visible text (it is a severity, not content). **Parity check:** the JSON-LD
  for `section_alert` emits **no distinct type** (plan §6) — the alert is visible
  callout body only — so not printing the level as text creates no
  visible↔emitted mismatch. Confirmed safe.
  ```twig
  {% set level = paragraph.field_section_alert_level.value|default('info') %}
  <aside role="note"
         {{ attributes.addClass('geo-section', 'geo-alert', 'geo-alert--' ~ level) }}>
    {% if content.field_section_heading|render|trim %}
      <h2 class="geo-alert__title">{{ content.field_section_heading }}</h2>
    {% endif %}
    <div class="geo-alert__body">{{ content.field_section_body }}</div>
  </aside>
  ```
  Reading `paragraph.field_section_alert_level.value` for a class is display logic
  (allowed in a template); it transforms presentation, not content.
- CSS: `.geo-alert` base + four accent modifiers (`--info/--success/--warning/--danger`)
  mapping to a local `--geo-alert-accent` custom property, fallback colors
  hard-coded; consume `--radius`/`--border`. Contrast: pick accent + text colors
  meeting AA 4.5:1 on the alert background (verify in §9).
- a11y: `role="note"` (a callout is a complementary note, not a live region —
  these are static page callouts, not dynamically-injected alerts, so `role="alert"`
  would be wrong/over-announcing). Severity conveyed by heading text + accent;
  color is not the sole signal (the heading text carries the meaning, e.g.
  "Winter utility help closes March 31").

### 4.7 `paragraph--section-contact-panel.html.twig`
- Fields (all optional except heading present in sample): `field_section_heading`
  (string), `field_section_contact_name` (string), `field_section_phone`
  (string), `field_section_email` (**email_mailto** → renders mailto `<a>`),
  `field_section_address` (text_default), `field_section_hours`
  (**office_hours_table** → renders the office_hours module's hours table),
  `field_section_link` (link).
- Use `<address>` for the contact block; structure the name/phone/email as a
  description list for label-free but programmatically-grouped output. Print each
  field through its formatter (email stays a real mailto link; hours stay the
  office_hours table; address keeps formatting).
  ```twig
  <section{{ attributes.addClass('geo-section', 'geo-section--contact') }}>
    {% if content.field_section_heading|render|trim %}
      <h2 class="geo-section__title">{{ content.field_section_heading }}</h2>
    {% endif %}
    <address class="geo-contact">
      {% if content.field_section_contact_name|render|trim %}
        <p class="geo-contact__name">{{ content.field_section_contact_name }}</p>
      {% endif %}
      {% if content.field_section_phone|render|trim %}
        <p class="geo-contact__phone">{{ content.field_section_phone }}</p>
      {% endif %}
      {% if content.field_section_email|render|trim %}
        <p class="geo-contact__email">{{ content.field_section_email }}</p>
      {% endif %}
      {% if content.field_section_address|render|trim %}
        <div class="geo-contact__address">{{ content.field_section_address }}</div>
      {% endif %}
      {% if content.field_section_hours|render|trim %}
        <div class="geo-contact__hours">{{ content.field_section_hours }}</div>
      {% endif %}
      {% if content.field_section_link|render|trim %}
        <div class="geo-contact__link">{{ content.field_section_link }}</div>
      {% endif %}
    </address>
  </section>
  ```
  The phone is a `string` field (no `tel:` link from the formatter). Markup-only
  rule: do **not** invent a `tel:` link the formatter doesn't produce (that would
  add markup beyond the rendered field; a `tel:` wrapper is arguably a11y-positive,
  but it changes rendered output — flag as an optional config follow-up to switch
  the phone formatter or field type, NOT a template hack). Keep it as text for beta.
- **Parity note:** the contact panel maps to `ContactPoint`/`PostalAddress`/
  `OpeningHoursSpecification` nested under the provider Organization. Every
  emitted field (telephone, email, address, hours) is visibly rendered here →
  parity holds. The office_hours table renders the same Mon–Fri 09:00–17:00 the
  JSON-LD emits (5 `OpeningHoursSpecification`, asserted by the probe).
- a11y: `<address>` for contact info; email is a real mailto link; office_hours
  table is the module's accessible table output (do not restyle into divs).

### 4.8 (reserved) — no per-level template suggestion alter needed
The alert variant is handled by the modifier class in §4.6, so **no
`hook_theme_suggestions_paragraph_alter()`** is required — the `.module` file's
only hook is `hook_theme()` (§2.4). (Kept as an explicit "considered and
rejected" note so the executor does not add a suggestion alter "to be safe.")

> **Authoring note (MINOR-2):** the `string` formatter wraps its output in its
> own element. When authoring the FAQ item template (§4.2), verify the actual
> DOM nesting of the `string` formatter's element inside the `<dt>` during
> template work — e.g. confirm `<dt class="geo-faq__q"><div class="field...">Q
> text</div></dt>` is acceptable or whether the inner wrapper must be stripped
> (which would require switching to `{{ items[0].content }}` instead of
> `{{ content.field_section_question }}`). Resolve empirically at authoring time;
> do not guess the formatter's exact wrapping element.

### 4.9 `paragraph--section-media-text.html.twig`
- Fields: `field_section_heading` (string), `field_section_media`
  (entity_reference_entity_view → media image), `field_section_body` (text_default).
  `field_section_media_position` is **HIDDEN in the view-display** → it does not
  render and is not available via `content.*`.
- **Position decision:** the position value (`right` in the sample) is *not* in
  the rendered output. Two parity-safe options:
  (a) read `paragraph.field_section_media_position.value` in Twig to choose a
      BEM modifier (`geo-mediatext--media-right`) — this is display logic (layout
      choice), changes no content, emits nothing → parity-safe; **recommended**.
  (b) ignore position; always media-right on desktop. Simpler, loses author intent.
  **Recommend (a).** Reading a hidden field for *layout* is allowed (it is not
  content; the JSON-LD emits only the image `ImageObject` from the media, which
  is visibly rendered regardless of side).
  ```twig
  {% set pos = paragraph.field_section_media_position.value|default('right') %}
  <section{{ attributes.addClass('geo-section', 'geo-section--mediatext', 'geo-mediatext--media-' ~ pos) }}>
    {% if content.field_section_heading|render|trim %}
      <h2 class="geo-section__title">{{ content.field_section_heading }}</h2>
    {% endif %}
    <div class="geo-mediatext__grid">
      <div class="geo-mediatext__media">{{ content.field_section_media }}</div>
      <div class="geo-mediatext__body">{{ content.field_section_body }}</div>
    </div>
  </section>
  ```
- CSS: mobile = stacked (media then text). `min-width:768px` = two-column grid;
  `--media-right`/`--media-left` modifier swaps `order`. Image is responsive
  within its column (`max-width:100%; height:auto`).
- a11y: media alt already authored; heading h2; reading order in source is
  media→body (acceptable; the body is supplementary to the diagram).

### 4.10 `geo_starter_section` SAMPLE (the missing-bundle gap)
Authored as new `content/` YAML in `drupal/geo_starter` (recipe side, §5.5). No
template difference — uses §4.1. Listed here so the bundle's template + sample
are accounted for together.

### 4.11 Node field-stack treatment + the editorial-label leak ("Reviewed by name" / "Reviewed date")

**The problem (verified):** `field_reviewed_by_name` and `field_reviewed_date`
render with `label: above` and their human label is the literal field label
"Reviewed by name" / "Reviewed date" — these leak as visible UI chrome that reads
like form-field names, undermining the provenance positioning. Same for
`field_author_name` ("Author name") on articles.

**Decision (MAJOR-1 revision — config-relabel is the default, not the alternative):**
relabel the three fields in `core.entity_view_display.node.{service,answer,article}.default.yml`:
- `field_reviewed_by_name` label → **"Reviewed by"**
- `field_reviewed_date` label → **"Last reviewed"**
- `field_author_name` (article only) label → **"Author"**

This is a **recipe-side config change** (label-string-only, parity-safe per Hard
constraint 1 — **visibility stays `above`, no field set to `hidden`**). The
provenance values still render identically; only the label wording changes.
Parity: JSON-LD `WebPage.reviewedBy` (Person) + `dateModified` are present; the
values are still visibly rendered → parity holds.

Per-field CSS de-emphasis (smaller font, muted color meeting AA contrast) is
achievable via `.field--name-field-reviewed-by-name, .field--name-field-reviewed-date`
selectors in `sections.css`, scoped to `.node` — no wrapping container needed.

> **Future work (explicitly out of scope for beta): a grouped provenance footer
> band.** The ideal presentation ("Reviewed by Demo Services Editorial Review ·
> Last reviewed May 10, 2026") requires a shared container wrapping both fields
> into one semantic unit. Under the plan's current constraints (no node template
> override, no preprocess), two independent field templates **cannot enclose
> themselves in one shared container** — each field renders in its own
> `field.html.twig` pass at its own weight in the field-stack. Achieving the
> grouped footer requires a `node--service.html.twig` (and per-type variants)
> that wraps the two fields in a `<footer class="geo-provenance">`. This is
> deferred because it introduces the monolithic-node-template failure mode the
> plan avoids (§ Negative space). Revisit post-beta when the node templates'
> full treatment is scoped (Marketplace-track design system).

**The rest of the node field-stack:** no node-level template is needed. The bare
`field--name-*` wrappers are acceptable once the section paragraphs and the
provenance label leak are fixed — the remaining `label: above` fields
(direct answer, summary, audience, topic, evidence sources, next action) have
*correct, non-leaky* labels and render fine through core's field template with
our CSS giving them spacing. **Decision: no full `node--*.html.twig` override**
(avoids the monolithic-template failure mode; the field-stack is already
view-display-driven). If the bare field labels need visual hierarchy, that is a
CSS-only concern on the existing `field` markup, added to `sections.css` scoped
to `.node .field` — but keep it minimal; the beta floor is "no unstyled dump +
no leaky labels + semantic sections," not a full node redesign.

---

## 5. Recipe-side change list (config-only — `drupal/geo_starter`)

The recipe cannot ship templates/CSS/PHP. Its WS-B changes are limited to:

### 5.1 `recipe.yml` — add the submodule to `install:` (NO new `composer.json` require)
Add `geo_starter_jsonld_markup` to the `install:` list (alphabetically near
`geo_starter_jsonld`). This enables the already-present submodule on fresh
install:
```yaml
install:
  - ...
  - geo_starter_jsonld
  - geo_starter_jsonld_markup   # ADD — enables WS-B templates + CSS submodule
  - ...
```
**Do NOT add a `require` to `geo_starter/composer.json`.** The submodule ships
*inside* `drupal/geo_starter_jsonld`, which is already required (`^1.0`,
composer.json line 13). Composer places the parent package + submodule in the
codebase; the `install:` entry just turns the submodule on. Adding a separate
`require` for `geo_starter_jsonld_markup` would be wrong — it is not its own
package (see the boundary callout in §2.1). The recipe's WS-B footprint is
exactly: this one `install:` line + the §5.5 sample + the §5.2 label relabel.

### 5.2 Display-label pass (decided — config-relabel, per §4.11)
Edit three view-display files:
- `core.entity_view_display.node.service.default.yml`
- `core.entity_view_display.node.answer.default.yml`
- `core.entity_view_display.node.article.default.yml`

Change **only** the `label` strings on:
- `field_reviewed_by_name` → **"Reviewed by"** (was "Reviewed by name")
- `field_reviewed_date` → **"Last reviewed"** (was "Reviewed date")
- `field_author_name` (article only) → **"Author"** (was "Author name")

**Visibility stays `above`; no field set to `hidden`** (Hard constraint 1: a
hidden JSON-LD-mapped field suppresses emission by design). These are
label-string-only edits — the field values, formatters, and weights are
untouched. Parity: JSON-LD reads field values, not display labels → safe.

### 5.3 Optional label-wording polish (safe, recommended regardless)
The scalar fields use machine-derived labels that read acceptably but could be
warmer. **Any change here is label-string-only**, never visibility:
- `field_direct_answer` label "Direct answer" → fine, keep.
- `field_next_action` label "Next action" → consider "What to do next".
- These are nice-to-haves; defer unless trivial. **Hard rule: never change a
  JSON-LD-mapped field's *visibility*; label text and label position
  (above/inline/visually_hidden) are the only safe knobs.**

> Which fields are JSON-LD-mapped (do NOT hide): field_direct_answer,
> field_summary, field_evidence_sources, field_next_action, field_reviewed_date,
> field_reviewed_by_name, field_topic, field_audience, body, and the section
> paragraphs. In practice **every** currently-`above` field on these nodes feeds
> some normalizer/contributor — treat the whole stack as parity-locked for
> visibility. Label *position* changes (above→inline→visually_hidden) are safe;
> they don't suppress emission (emission reads field values, not display labels).

### 5.4 No SDC, no breakpoints.yml, no theme config
Confirmed non-changes: do not register Mercury SDCs, do not add
`mercury.breakpoints.yml` or any theme override, do not touch `system.theme`.

### 5.5 The `geo_starter_section` sample (host + YAML sketch)

**Host decision:** attach to the existing **answer** node `42000000-…-000000000001`
(`/who-can-apply-emergency-assistance`). Rationale:
- answer's `field_sections` allow-list permits `geo_starter_section` (verified).
- The two answer nodes currently render **no** sections at all → this also gives
  the **answer node type** its first rendered section (improves the screenshot
  matrix's node coverage, not just bundle coverage).
- No new node required; minimal additive change (one new paragraph YAML + edit
  the answer's `field_sections` + `depends`). Honors "content/ is the single
  source of truth; hand-written YAML with `depends`."

**New file:** `content/paragraph/46000000-0000-4000-8000-000000000081.yml`
```yaml
_meta:
  version: '1.0'
  entity_type: paragraph
  uuid: 46000000-0000-4000-8000-000000000081
  bundle: geo_starter_section
  default_langcode: en
default:
  status:
    - value: true
  field_section_kicker:
    - value: 'Good to know'
  field_section_heading:
    - value: 'How this answer is kept current'
  field_section_body:
    - value: '<p>This fictional demo answer is reviewed on a fixed cadence and cites the same evidence sources as the services it supports, so the eligibility summary above stays traceable to one governed place.</p>'
      format: content_format
```

**Edit** `content/node/42000000-0000-4000-8000-000000000001.yml`:
- Add to `_meta.depends`: `46000000-0000-4000-8000-000000000081: paragraph`.
- Add a `field_sections` block under `default:`:
  ```yaml
  field_sections:
    -
      entity: 46000000-0000-4000-8000-000000000081
  ```
- Keep all existing fields untouched (additive only). The content does NOT
  re-state `field_direct_answer` (parity discipline / design principle 1 — the
  generic section is "how it's kept current," not a second answer).

This adds `geo_starter_section` to a sample page **and** gives the answer node
type a rendered section. DoD "every bundle visible on a sample page" is then met:
all 8 paragraph bundles (faq/step_list/card_grid/contact_panel on the flagship
service; alert on the water-bill service; cta + media_text on the two articles;
geo_starter_section on the who-can-apply answer) plus the four node types.

---

## 6. Asset library + attachment strategy

### 6.1 `geo_starter_jsonld_markup.libraries.yml`
```yaml
sections:
  version: 1.x
  css:
    component:
      css/sections.css: {}
```
- `component` weight bucket (loads after base/theme, before nothing custom needed).
- No JS (no behaviors required — FAQ is open, no disclosure widgets; nothing
  interactive). **jQuery-free by construction.**
- No external/CDN libraries.

### 6.2 Attachment strategy — load ONLY where sections render (not site-wide)
The brief requires "attached only where needed (paragraph/node render, not
site-wide if avoidable)." Attach the library **from the paragraph templates**, so
it loads only on pages that actually render a section paragraph:
- In each `paragraph--section-*.html.twig` add at the top:
  `{{ attach_library('geo_starter_jsonld_markup/sections') }}`.
- The library loads on **any page rendering at least one section paragraph**.
  Pages with no sections (including admin routes) do not load it.

> **Attachment placement (MINOR-4):** place `attach_library` in each bundle's
> own template (all 10 paragraph templates). Child templates
> (`section-faq-item`, `section-step-item`) may also attach the library — Drupal
> deduplicates library attachments, so a child attaching the same library as its
> parent is harmless and makes each template self-sufficient.

> Attachment-in-template is the correct lazy-load mechanism (the library bubbles
> via `#attached` through render caching). Do **not** put the CSS in the
> submodule `.info.yml` (that would load it on every page including admin —
> exactly the global-CSS failure mode the brief warns against). Verify the CSS is
> absent from a page with no sections (e.g. an admin route) as a §10 assertion.

> **Node-level provenance de-emphasis CSS** (§4.11 config-relabel path): the
> `.field--name-field-reviewed-by-name` / `.field--name-field-reviewed-date`
> selectors in `sections.css` will style fields on node pages **whether or not**
> the page has sections. This CSS loads via the paragraph template attachment only
> on section pages; on sectionless node pages, the provenance fields render with
> their relabeled text but without de-emphasis styling. This is acceptable for
> beta — the label leak is fixed everywhere; the visual de-emphasis is a bonus
> on section pages only. A site-wide provenance style would require a different
> attachment point (future work per §4.11).

### 6.3 Cache implications
`attach_library` from a template participates in the paragraph/node render cache;
no preprocess, no cache-tag manipulation. The JSON-LD module's own cacheability
(merged in `hook_node_view_alter`) is untouched. The submodule's `.module` file
ships only `hook_theme()` (registration infrastructure); **no preprocess
functions are introduced** (the §4.6/§4.9 value reads are inline Twig display
logic, not preprocess) — this keeps us clear of the "business logic in
preprocess" failure mode entirely.

---

## 7. Preprocess & hook plan

**The `.module` file ships exactly one hook: `hook_theme()`** (§2.4). This is
the mandatory template-registration hook — it is infrastructure, not preprocess
logic.

**Zero preprocess functions.** This pass adds no `hook_preprocess_*` hooks. All
display logic lives in Twig (`{% if %}` guards, a `.value` read for a modifier
class, `attach_library`). The only "logic" is presentational class selection
(alert level, media position) which is display-only and belongs in the template;
introducing a preprocess function would be over-engineering and invites the
business-logic-leak failure mode. If drupal-theme-critic flags a Twig value read
as too heavy, the fallback is a `hook_preprocess_paragraph__<bundle>()` that
sets a single `#variables['geo_alert_level']` — but default is no preprocess.

**No `hook_theme_suggestions_*_alter()`** — the alert variant uses a BEM modifier
class (§4.6/§4.8), not a template suggestion.

---

## 8. Accessibility plan

| Concern | Treatment |
|---|---|
| Heading hierarchy | Page h1 (Mercury page-title) → section titles **h2** → nested item titles (faq item, step name) **h3**. Verified per node type as a DoD step (§10). No skipped levels. |
| FAQ | Open `<dl>`/`<dt>`/`<dd>`, never collapsed — no disclosure ARIA, nothing hidden (parity + a11y aligned). |
| Steps | `<ol>` conveys sequence; `<h3>` per step. |
| Contact | `<address>` element; email is a real `mailto:` link; office_hours module's accessible table preserved. |
| CTA / links | Real `<a href>` from formatters → native keyboard + focus. Button styling MUST keep `:focus-visible` outline (no `outline:none` without replacement). |
| Alert | `role="note"` (static callout, not a live region); meaning carried by heading text, not color alone; accent colors meet AA contrast (§9). |
| Card grid | Grid of real links; each card is keyboard-reachable; focus-visible preserved. |
| Media/text | `max-width:100%` images; alt text authored; logical source order. |
| Color | All token-consumed colors have fallbacks meeting AA 4.5:1 (text) / 3:1 (large) — verified §9. |
| Motion | No animations introduced (no `prefers-reduced-motion` work needed; if a hover transition is added, gate it). |
| Provenance labels | Config-relabeled (§4.11); de-emphasis CSS where loaded (section pages); AA contrast on muted text. Grouped footer band is future work (requires node template). |

This is a **spot-level** a11y pass aligned with beta gate B7 (keyboard + contrast
on homepage/Service/dashboard), not full WCAG 2.2 AA evidence (Marketplace-track,
explicitly out of scope per beta plan).

---

## 9. Performance budget

- **CSS:** one `sections.css`, target **< 8 KB** uncompressed (≈ 10 small
  components, no framework). It is the only asset this pass adds.
- **JS:** **0 bytes** (no behaviors).
- **Loading:** lazy via `attach_library` in templates → not on no-section/admin
  pages (§6.2). No render-blocking additions site-wide.
- **Images:** none added by templates; the media_text/step images are
  author-supplied media rendered through existing media display (no new image
  styles introduced — Marketplace-track).
- **LCP:** unaffected/neutral — we add a small same-origin stylesheet on content
  pages; no web fonts, no large images, no blocking JS. No LCP regression expected.
- **No critical-CSS extraction** (over-engineering for this surface; Mercury
  already inlines its base layer).

---

## 10. DoD assertion list (written so a broken contract fails RED)

Run on a **fresh** `drush site:install recipes/geo_starter` on the DDEV reval
project (re-sync protocol §11). Per assertion, the failure is explicit:

**A. Template-selection proof (catches `hook_theme()` registration errors)**
- A1. With `twig_debug: true`, the `THEME DEBUG` comment on each of the 10
  paragraph bundles' rendered output (8 parent bundles + 2 child bundles:
  `section_faq_item`, `section_step_item`) names the module-provided
  `paragraph--section-*.html.twig` (or `paragraph--geo-starter-section.html.twig`)
  as **the selected template** — not the core fallback. One assertion per bundle
  (10). *Fail = `hook_theme()` entry missing/mistyped, or template filename does
  not match the hook name via `strtr($hook, '_', '-')`.*

**B. Semantic-element presence (per bundle, the core of "rendered correctly")**
On the named host page (§1.6), assert the bundle's defining semantic element is
present in the response HTML:
- B-faq: `/apply-emergency-food-and-utility-assistance` contains `<dl class="geo-faq"` AND ≥2 `<dt class="geo-faq__q"`. *Fail = FAQ not a list.*
- B-steps: same page contains `<ol class="geo-steps"` AND ≥2 `<li class="geo-steps__item"` AND `<h3 class="geo-steps__name"`. *Fail = steps not ordered/headed.*
- B-cards: same page contains `<div class="geo-cards"` AND ≥1 link inside. *Fail = card grid unstyled.*
- B-contact: same page contains `<address class="geo-contact"` AND a `mailto:` link AND the office_hours table markup. *Fail = contact not semantic.*
- B-alert: `/get-help-past-due-water-bill` contains `<aside ... class="...geo-alert geo-alert--warning"` AND `role="note"`. *Fail = alert level/role missing.*
- B-cta: `/preparing-complete-assistance-application` contains `<aside ... class="...geo-section--cta"` AND an `<a` inside `.geo-cta__action`. *Fail = CTA link missing.*
- B-mediatext: `/how-answer-hub-keeps-public-service-pages-reviewable` contains `<section ... class="...geo-section--mediatext geo-mediatext--media-right"` AND an `<img`/media inside `.geo-mediatext__media`. *Fail = media/text layout missing.*
- B-generic: `/who-can-apply-emergency-assistance` contains `<section ... class="...geo-section--generic"` AND `<h2 class="geo-section__title"` AND `.geo-section__kicker`. *Fail = generic section/sample missing.*

**C. No raw machine names / no unrendered target_ids (DoD core)**
- C1. None of the 8 host pages' HTML contains the substrings `field_section_`,
  `target_id`, or a bare paragraph/node UUID. *Fail = leak.*
- C2. The reviewed-by/date label leak is gone: the flagship Service page HTML
  does **not** contain the visible text "Reviewed by name" or "Reviewed date"
  (it shows "Reviewed by" / "Last reviewed" per §4.11). *Fail = editorial leak
  persists.*

**D. Heading hierarchy (a11y DoD)**
- D1. On each of the four node types' representative pages, the heading order has
  no skipped levels (exactly one `<h1>`; section titles are `<h2>`; faq/step
  items `<h3>`). Verify with an outline check (axe/`headingsMap` or manual DOM
  walk). *Fail = hierarchy broken.*

**E. JSON-LD parity (the hard gate)**
- E1. `php tools/jsonld-probe.php` (via `drush scr`) prints **`23 passed, 0
  failed`** and exits 0 **after** all template + sample + config work. *Fail =
  parity tripped (a field was hidden, or the new sample changed the graph).*
- E2. Specifically re-confirm the marquee assertions still hold: FAQPage
  mainEntity length == 2, HowTo step count == 2, ItemList ≥1, ContactPoint with
  5 OpeningHoursSpecification — i.e. the contact/faq/steps/cards templates did
  not alter what the contributors traverse. (These are inside the 23; E2 just
  names the ones most coupled to the templates touched.)

**F. Library scoping**
- F1. The flagship Service page response includes `sections.css`. *Fail = not
  attached.*
- F2. An admin route (e.g. `/admin/content`) response does **not** include
  `sections.css`. *Fail = global-CSS leak.*

**G-release. Release-tarball safety (submodule must survive drupal.org packaging)**
- G-release-1. The `drupal/geo_starter_jsonld` package's `.gitattributes` (if it
  exists) does **not** contain an `export-ignore` rule for `modules/` or
  `modules/geo_starter_jsonld_markup/`. *Fail = the submodule is stripped from
  the release tarball and `install: [geo_starter_jsonld_markup]` triggers
  `UnknownExtensionException` on every fresh install from a packaged release.*
- G-release-2. The `composer.json` of `drupal/geo_starter_jsonld` does **not**
  contain an `exclude-from-classmap` or `.gitattributes`-equivalent that would
  drop the `modules/` directory. *Fail = same as above.*

> If no `.gitattributes` exists in the module repo, both assertions pass
> trivially (no exclusion rule = no exclusion). The check is defensive: a future
> contributor adding `.gitattributes` for test-directory exclusion could
> accidentally glob-exclude `modules/`. This assertion catches that.

**G. Screenshots (DoD deliverable, not pass/fail logic but required)**
- G1. Desktop **1280px** + mobile **375px** screenshot per bundle × host page,
  captured via Playwright/agent-browser against the reval install. Matrix in §11.
  8 bundles → 16 screenshots minimum (faq/steps/cards/contact share the flagship
  page but are screenshotted per-section region).

### 10.H Existing PHPUnit tests — break analysis + new coverage

**Existing module tests and whether WS-B can break them** (the module's
Unit/Kernel/Functional suite runs in drupal.org GitLab CI; these gate the module
beta1 tag alongside the probe):
- `FaqPageEmissionTest` (Functional): asserts the **emitted JSON-LD graph**
  (FAQPage/Question/acceptedAnswer), not visible markup. WS-B is markup-only and
  changes no field visibility → **should not break**. Confirm green post-change
  anyway (the markup submodule is installed in the functional test site if it's
  in the recipe — verify the test's module-enable list; if the test enables only
  `geo_starter_jsonld`, the markup submodule is absent there and the test is
  unaffected by definition).
- `ReviewedByPlacementTest` (Kernel): asserts **JSON-LD graph placement only** —
  specifically that `reviewedBy` lives on the `WebPage` object (lines 134–147),
  the 2026-06-02 relocation. It **never renders HTML**. WS-B markup changes
  cannot break it. **Do NOT touch this test.**
- Other Kernel/Unit normalizer/contributor tests: emission-only, markup-agnostic
  → unaffected.

> **Mandatory Task-0 grep (CRITICAL-3):** before authoring any template, grep
> across **both** repos' `tests/` trees for rendered-HTML assertions on the
> literal strings `"Reviewed by name"`, `"Reviewed date"`, and `"Author name"`.
> Only a test asserting on those visible label strings (not JSON-LD graph
> content) could break from the §4.11 config-relabel. If such a test exists,
> update its expected string to the new label and note it as an intended change.
> If no test matches (expected — the module tests are emission-only), proceed.
> ```
> grep -rn '"Reviewed by name"\|"Reviewed date"\|"Author name"' \
>   /Users/AlexUA_1/claude/ai-initiative-modules/geo_starter_jsonld/tests/ \
>   /Users/AlexUA_1/claude/ai-initiative-modules/geo_starter/tests/ || true
> ```

> Because the markup submodule may not be enabled in the parent module's existing
> functional tests, the **primary markup regression guard is the recipe-side
> fresh-install acceptance (§10 A–F)** + the probe, not the module PHPUnit suite.
> That is by design (emission-only installs must keep passing without the markup).

**New coverage warranted (cheap, markup smoke — keep it kernel where possible):**
- **N1. KernelTest in the markup submodule** (`tests/src/Kernel/SectionMarkupTest.php`):
  install `geo_starter_jsonld_markup` + paragraphs + node; for each of the 8
  bundles, build a paragraph (with children for nested), render it **through the
  entity view builder** (`\Drupal::entityTypeManager()->getViewBuilder('paragraph')->view($paragraph)`,
  then `\Drupal::service('renderer')->renderRoot($build)`) so the test exercises
  the real suggestion → template selection path (the `hook_theme()` registration
  from CRITICAL-1 is the thing being guarded). Assert the **defining semantic
  element is present** in the rendered HTML — `<dl class="geo-faq"` (faq),
  `<ol class="geo-steps"` + `<h3 class="geo-steps__name"` (steps),
  `<aside ... role="note"` + `geo-alert--<level>` (alert),
  `<address class="geo-contact"` (contact), `<section ... geo-section--cards"`
  (cards), `geo-section--cta"` + an `<a` (cta),
  `geo-section--mediatext geo-mediatext--media-right"` (media_text),
  `<h2 class="geo-section__title"` + `geo-section__kicker` (generic). One cheap
  assertion per bundle. **N1's primary job is guarding the `hook_theme()`
  registration** (CRITICAL-1's CI guard) — if a `hook_theme()` entry is missing
  or its key is mistyped, the corresponding bundle's semantic element will be
  absent from the rendered output, and the assertion fails red. The
  semantic-element checks are the *mechanism*; the registration guard is the
  *purpose*.
- **N2. A single assertion that the markup submodule is uninstallable cleanly**
  (revert-to-classless rollback path, §12) — optional, only if cheap.
- **Do NOT** add browser/FunctionalJavascript tests (no JS in this pass; the
  fresh-install §10 assertions + screenshots cover the integrated view). Keep the
  new test fast and bundle-focused; the heavy integration proof is the DDEV
  acceptance run, deliberately.

---

## 11. Sequencing, screenshot matrix, and review checkpoints

### 11.1 Order of operations (module → recipe → acceptance → screenshots → probe → docs)
1. **Task 0 — STOP-GATE** (§2.4, CRITICAL-2 + delta MINOR-2): write the
   submodule scaffold (`.info.yml`, `.module` with `hook_theme()`,
   `.libraries.yml`, `css/sections.css` stub) + **TWO** templates — parent
   `paragraph--section-faq.html.twig` AND child
   `paragraph--section-faq-item.html.twig` — + their `hook_theme()` entries.
   Install the submodule on the DDEV reval project, render the flagship Service
   page with `twig_debug: true`, and confirm `THEME DEBUG` marks **both** module
   templates as selected (not the core fallback): parent `<dl class="geo-faq">`
   AND child `<dt class="geo-faq__q">` present — the child proves the nested
   `entity_reference_revisions` render path. Cross-reference: F1 (sections.css
   present on section pages) is the positive counter-signal.
   **If either module template is NOT selected, STOP and diagnose** — do not
   author the remaining 8 templates until registration is proven. Also run the
   §10.H mandatory grep for rendered-HTML assertions on "Reviewed by name" /
   "Reviewed date" / "Author name" across both repos' `tests/` trees.
2. **Module work (`geo_starter_jsonld` repo):** after Task 0 passes, author the
   remaining 8 paragraph templates + their `hook_theme()` entries (§4.1–§4.9),
   finalize `css/sections.css`, write the N1 KernelTest (§10.H).
   **drupal-theme-critic checkpoint #1** on the submodule (templates + CSS +
   library + a11y + `hook_theme()` registration correctness).
   **drupal-critic** pass on the submodule (Drupal coding standards, `.module`
   hook_theme-only, no preprocess, dependency correctness).
3. **G-release check** (§10 G-release, MAJOR-2): verify `.gitattributes` /
   `composer.json` do not `export-ignore` the `modules/` directory.
4. **Recipe work (`geo_starter` repo):** `recipe.yml` install addition (§5.1);
   the `geo_starter_section` sample paragraph + answer edit (§5.5); the §5.2
   view-display label relabel (config-relabel is the decided path per §4.11).
5. **Fresh-install acceptance (DDEV reval project):** re-sync into the DDEV
   project (established re-validation protocol). **Exact filesystem paths the
   submodule must occupy for `install: [geo_starter_jsonld_markup]` to resolve
   (MAJOR-2 — an `UnknownExtensionException` on the submodule machine name
   aborts the entire `site:install`, not just the submodule):**
   - Module package sync target: `packages/geo_starter_jsonld/` (the parent).
     The submodule must land at
     `packages/geo_starter_jsonld/modules/geo_starter_jsonld_markup/` with its
     `.info.yml` present — Drupal's extension discovery scans `modules/`
     subdirectories of installed modules.
   - Recipe sync target: `recipes/geo_starter/` (config parsed only at install —
     established gotcha).
   - **Verification before `site:install`:** confirm the submodule's `.info.yml`
     is present at the expected path:
     ```
     ls packages/geo_starter_jsonld/modules/geo_starter_jsonld_markup/geo_starter_jsonld_markup.info.yml
     ```
     If missing, the sync was incomplete — fix before proceeding (do not let
     `site:install` discover the error as a cryptic `UnknownExtensionException`).
   - Then: `drush site:install recipes/geo_starter` from scratch with a rotated
     admin pass. Run §10 A–F + G-release assertions.
6. **Screenshots (§11.2):** 1280 + 375 per bundle.
7. **Probe:** `tools/jsonld-probe.php` → 23/23 (§10 E1). **Hard gate.**
8. **Docs:** LIMITATIONS truth-pass (close "no design pass" + "missing
   geo_starter_section sample"; the cta/alert/media_text-missing line already
   closed in B2); beta-readiness plan WS-B as-built note; module README mention of
   the markup submodule. **drupal-theme-critic checkpoint #2** (post-implementation
   review of the rendered result + screenshots, per house routing rule).
9. **Commits in both repos** (conventional commits; §11.4).

### 11.2 Screenshot matrix (bundle × URL × viewport)
| Bundle | URL | 1280px | 375px |
|---|---|---|---|
| section_faq | /apply-emergency-food-and-utility-assistance | ✓ | ✓ |
| section_step_list | /apply-emergency-food-and-utility-assistance | ✓ | ✓ |
| section_card_grid | /apply-emergency-food-and-utility-assistance | ✓ | ✓ |
| section_contact_panel | /apply-emergency-food-and-utility-assistance | ✓ | ✓ |
| section_alert | /get-help-past-due-water-bill | ✓ | ✓ |
| section_cta | /preparing-complete-assistance-application | ✓ | ✓ |
| section_media_text | /how-answer-hub-keeps-public-service-pages-reviewable | ✓ | ✓ |
| geo_starter_section | /who-can-apply-emergency-assistance | ✓ | ✓ |

(4 bundles co-located on the flagship Service → capture each section region; a
single full-page 1280/375 of that page plus per-section crops satisfies "per
bundle".) Tool: agent-browser `--profile Default` against the DDEV cert (per the
established Canvas/DDEV recipe), or Playwright; screenshots stored alongside the
beta evidence (not committed into module/recipe code dirs unless the repo's
convention says so — confirm at checkpoint #2).

### 11.3 Review checkpoints (drupal-theme-critic focus)
- **Checkpoint #1 (pre-recipe, on the submodule):** `hook_theme()` registration
  correctness (all 10 entries present with `'base hook' => 'paragraph'`),
  template semantics (h2/h3/ol/dl/address correctness), `{{ attributes }}`
  preservation, the FAQ "open, no collapse" decision, the alert `role`/contrast,
  library lazy-attach, zero-preprocess, string-formatter wrapping inside
  semantic elements (§4.8 authoring note). Also drupal-critic for Drupal coding
  standards + dependency/info-yml correctness + `.gitattributes` safety
  (G-release).
- **Checkpoint #2 (post-implementation):** rendered result vs §10 assertions,
  screenshots, probe 23/23, no global-CSS leak, heading order, config-relabel
  labels correct (§5.2). This is the house-rule "route through
  drupal-theme-critic after implementation" gate.

### 11.4 Commits
- Module repo: `feat(markup): geo_starter_jsonld_markup submodule — semantic
  section templates + scoped CSS (WS-B)`.
- Recipe repo: `feat(recipe): install markup submodule + geo_starter_section
  sample; reviewed-by label pass (WS-B)`.
- Docs commit(s) per house convention (`docs:`).

---

## 12. Risks + rollback

| Risk | Likelihood | Mitigation |
|---|---|---|
| **`hook_theme()` registration error** → missing or mistyped entry causes silent fallthrough to classless template (the exact gap we're fixing, re-introduced by the module's own infrastructure) | Med | Task 0 STOP-GATE (CRITICAL-2): prove ONE template + its entry before authoring the rest. §10 A1 THEME DEBUG assertion per bundle. N1 KernelTest guards all 10 entries in CI. A one-char hook-name typo fails A1 + N1 red. |
| **`.gitattributes` export-ignore** strips `modules/` from the drupal.org release tarball → `install: [geo_starter_jsonld_markup]` triggers `UnknownExtensionException` on every packaged install | Low | §10 G-release assertions; defensive — no `.gitattributes` exists today, but a future contributor adding test-directory exclusion could accidentally glob the submodule. |
| **Parity guard trips** — a template change makes a field render empty/hidden, suppressing emission, or the new sample alters the graph | Med | §10 E1 probe 23/23 is a hard gate; §10 E2 names the coupled assertions. Templates are markup-only (never hide a populated field). The geo_starter_section sample deliberately does NOT re-state the direct answer. |
| **Mercury token rename** (oklch shadcn tokens are theme-internal, could change on a Mercury update) | Low–Med | Every `var(--token, fallback)` has a hard fallback → graceful degrade, never breakage. Documented as a LIMITATION (styling consumes Mercury tokens best-effort). |
| **Render-cache staleness** after template install | Low | `drush cr` post-install (standard); fresh-install acceptance has no stale cache. attach_library bubbles correctly through render cache. |
| **office_hours_table markup** differs from assumption (we wrap, don't rebuild) | Low | We only wrap `{{ content.field_section_hours }}`; B-contact asserts the table renders. No restyle of the module's table internals. |
| **`<dl>` + `<div>` wrapper validity** doubt | Low | HTML5 living standard permits `<div>` grouping in `<dl>`; if a validator objects, fall back to flat `<dt>`/`<dd>` and move paragraph attributes to the `<dd>` (documented alt). |
| **`field_section_media_position` read** considered a config dependency | Low | It's a Twig `.value` read of an existing field; if the field were removed it's `|default('right')`-guarded. No hard coupling. |
| **Screenshot tooling flake** (DDEV cert / headless) | Low | Established agent-browser Canvas/DDEV recipe (cert flag, viewport set, refs stale per snapshot) is on record in memory. |

**Rollback:** uninstall `geo_starter_jsonld_markup` (it is an independent
submodule) → all bundles revert to core's classless rendering; `geo_starter_jsonld`
emission is untouched; the recipe's `geo_starter_section` sample + any label
config change are harmless on their own. No data migration, no content-model
change to unwind. The recipe is fresh-install-only (beta contract §1), so the
rollback story for a deployed site is "don't add it to `install:`" — there is no
in-place downgrade obligation.

---

## 13. Release sequencing implication (beta tag — maintainer confirms)

This work lands **inside `drupal/geo_starter_jsonld`** as a submodule. The module
has a PENDING `1.0.0-beta1` gated on WS-D Phase 1 external validation (separate
thread). Two options, presented with coupling costs:

- **(A) Grow beta1 scope to include the markup submodule.** beta1 ships
  normalizers/contributors **and** `geo_starter_jsonld_markup`. Single release
  narrative; matches the §1 lockstep cadence the beta plan already chose.
  **Coupling cost:** the module beta1 tag is now gated on **the later of**
  {WS-D Phase 1 validation, WS-B completion}. If WS-D Phase 1 is green and
  ready to tag but WS-B is still in progress, the tag waits for WS-B. In
  practice this coupling may be harmless (both are beta blockers running in
  parallel per beta plan §5), but it is a real schedule dependency — if WS-B
  discovers an unexpected issue (e.g., the Task 0 stop-gate fails and requires
  investigation), it delays the module tag and therefore the recipe tag.
- **(B) Ship markup as `1.0.0-beta2` immediately after beta1.** Decouples the
  module tag from WS-B: WS-D Phase 1 green → tag beta1 (emission-only) → WS-B
  completes → tag beta2 (adds submodule). **Coupling cost:** two module releases
  in quick succession; the recipe's beta1 must reference the module version that
  *contains the submodule* (beta2, not beta1) — so the recipe tag still waits
  for WS-B regardless. The decoupling benefit is only to the *module* tag, not
  the recipe tag.

**Maintainer decides at tag time.** Both options are viable. The choice depends
on whether WS-D Phase 1 and WS-B complete close enough in time for (A) to avoid
real delay. If they do, (A) is simpler (one tag, one release narrative). If
WS-D Phase 1 is ready significantly earlier, (B) avoids holding it.

**Recipe tag dependency (both options):** the recipe's `1.0.0-beta1` requires
the module release that *contains the submodule* to **exist** (release-ordering
rule: module tags first, recipe second; the recipe's `composer.json` must never
reference an uninstallable module version). Under (A): module beta1 → recipe
beta1. Under (B): module beta2 → recipe beta1. Call out the required module
version in `composer.json` at recipe-tag time.

---

## 14. Non-goals (explicit, held)

- No Mercury fork or subtheme (unsupported by Mercury; brief constraint).
- No Tailwind-utility dependence (utilities aren't in Mercury's build).
- No Canvas page changes (Canvas is a separate lane; WS-A owns it).
- No content-model changes: no new fields, no field-type/storage changes, no
  `field_sections` allow-list edits, no new bundles. (The geo_starter_section
  sample is content, not model.)
- No new drupal.org project (submodule of the existing module).
- No JSON-LD emission change (templates are visible-HTML only; the contract is a
  guard).
- No preprocess functions (the `.module` ships `hook_theme()` only — registration
  infrastructure, not preprocess logic); no JS/jQuery; no SDC embedding; no
  breakpoints.yml; no image styles; no critical-CSS pipeline.
- No field templates, no node templates (the provenance grouped-footer band is
  documented as future work in §4.11; config-relabel handles the leak for beta).
- Not full WCAG 2.2 AA evidence (Marketplace-track); this is the B7 spot-level
  floor.

---

## 15. As-built notes (2026-06-07 — what shipped vs. this plan)

All DoD gates green: 21/21 fresh-install assertions, probe **23/23** re-run
after every template change, N1 kernel test **36 assertions** green, phpcs
(Drupal + DrupalPractice) **0 violations**, screenshots 5 pages × 2 viewports
visually verified. Deviations and findings, each evidence-driven:

1. **List parents iterate field children** (`section_faq`, `section_step_list`
   templates): core's classless field template inserts wrapper `<div>`s that
   violate the `<ol>` content model (only `<li>` permitted; browsers hoist the
   div and break AT list semantics) and `<dl>` grouping rules. The parents
   iterate `content.field_x`'s numeric children directly — each child still
   renders through its formatter/template with its own cacheability.
   Endorsed at checkpoint #1 as "the established Drupal idiom for this exact
   situation."
2. **`<p>` wrappers → `<div>`** (generic kicker; contact name/phone/email):
   field formatters emit block elements, and a div inside `<p>` is invalid —
   browsers auto-close the `<p>` and eject the content from the styled
   wrapper. Found ONLY at the screenshot gate (source greps cannot see DOM
   repair; the class-presence assertion passed while the visual was broken).
   Lesson recorded: markup-present ≠ renders-correctly.
3. **Provenance label edits live in `field.field.node.*` config**, not the
   view displays (§5.2 as written): core view displays control label
   *position* only; label text has no per-display override. 7 field-config
   files relabeled; visibility untouched (parity-locked).
4. **Provenance de-emphasis CSS dropped as dead code**: the planned
   `.field--name-*` selectors can never match classless core markup. The
   relabel fixes the leak; visual de-emphasis joins the footer band as
   future work (requires node templates).
5. **Local sync path differs from §11.1.5**: this reval project has no
   `packages/geo_starter_jsonld` path repo — the module came from drupal.org
   packagist, so the submodule syncs to
   `web/modules/contrib/geo_starter_jsonld/modules/geo_starter_jsonld_markup/`.
   The recipe-boundary architecture is unaffected; G-release verified no
   `.gitattributes`/composer exclusion rules (submodule survives packaging).
   **The fresh install enabling the submodule from the recipe's one
   `install:` line empirically closes the §12 packaging/discovery risk.**
6. **A1 on the fresh install** was satisfied via the B-suite (`geo-` semantic
   elements present ⇒ module templates selected; nothing else emits them),
   not a THEME DEBUG trace — debug settings are wiped by install. THEME DEBUG
   selection was proven directly at the Task-0 stop-gate (parent + child).
   Environment note: D11.3 twig debug lives in the `development_settings`
   keyvalue collection, not State.
7. **Checkpoint #1 verdicts**: drupal-theme-critic APPROVE-WITH-FIXES
   (color-mix fallback added; N1 child assertions widened), drupal-critic
   APPROVE-WITH-FIXES (`drupal:node` dep dropped, `CoversFunction` added,
   `version: VERSION`). The `.value`-read pattern kept deliberately
   (zero-preprocess design; expect — and answer — the drupal.org reviewer
   question). Open post-beta item: `<address>` element semantics for a
   third-party org's contact panel (conformance nit, no user impact).
8. **§13 release sequencing remains the maintainer's call at tag time**
   (option A: fold into the RRT-gated `1.0.0-beta1`; option B: `beta2`).
   Module beta1 is still gated on the WS-D Phase 1 manual RRT run.
