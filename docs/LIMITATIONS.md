# Limitations

GEO Starter is not Marketplace-ready yet.

## Current Limits

- Public rendering uses the Drupal CMS **Mercury** theme's stock styling plus
  the `geo_starter_jsonld_markup` submodule's semantic templates for the ten
  section bundles (WS-B rendering pass, 2026-06-07): h2/h3 heading hierarchy,
  open `<dl>` FAQ, ordered `<ol>` steps, `<address>` contact, accented
  alert/CTA callouts, card grid, and media/text two-column layout — styled by
  one ~6 KB token-consuming CSS library (Mercury design tokens with hard
  fallbacks; no Tailwind-utility dependence). This is **lightly-styled
  semantic rendering, not a design system**: the node field-stack above the
  sections renders through core's classless field template (labels relabeled
  — "Reviewed by" / "Last reviewed" / "Author" — but visually plain), and a
  grouped provenance footer band plus field-level visual de-emphasis are
  documented future work requiring node templates.
- Four component-composed Canvas sample pages ship (homepage, migration
  landing, topic hub, campaign), built from stock Mercury SDCs and validated
  2026-06-07 against `drupal/canvas` 1.5.0 + `drupal/mercury` 1.0.5 on a
  fresh composer-built Drupal CMS install. The recipe ships
  `canvas.component.*` config for every component the trees use (the
  byte/haven site-template pattern) because Canvas's component-config
  auto-generation is install-stack-dependent and, on current stacks, runs
  after recipe content import. Bounded risk: the recipe caps the Canvas/Mercury minors it accepts
  (`drupal/canvas >=1.4 <1.6`, `drupal/mercury >=1.0.5 <1.1`) to the
  validated range, so a newer minor that changes component prop schemas
  cannot silently invalidate the committed trees and shipped configs —
  Mercury 1.0.5's new required `overlay_opacity` CTA prop is the precedent
  for why an unbounded minor is unsafe here. Maintainer rule: raising a cap
  is a deliberate step — re-export the shipped component configs and
  re-validate the trees
  (`docs/VALIDATION.md`, "Released-Artifact Install Proof").
- All ten section bundles ship sample content (`section_cta`,
  `section_alert`, and `section_media_text` added 2026-06-05/06; the generic
  `geo_starter_section` sample added 2026-06-07 on the
  `/who-can-apply-emergency-assistance` answer, which also gives the answer
  node type its first rendered section). The WS-B rendering pass verified
  every bundle visible on a fresh install: 21/21 markup/label/scoping
  assertions, desktop (1280px) + mobile (375px) screenshots per bundle, and
  the JSON-LD parity probe re-run at 23/23 after all template work. One
  caveat the assertions cannot see: template-selection on the fresh install
  was proven via the `geo-` markup itself (only the submodule's templates
  emit those elements), not a THEME DEBUG trace.
- `screenshot.webp` is a representative screenshot of the composed homepage
  on a fresh install, not a final Marketplace marketing image.
- The editorial dashboard View (`/admin/content/geo`, gated on the
  `access content overview` permission) uses a **grouped** moderation-state
  filter with the `geo_starter_editorial` states hard-coded as options
  (Draft / Needs review / Published / Archived). If you add or rename
  workflow states, update the View's filter options to match. The grouping
  is deliberate: core's dynamic `ModerationStateFilter` exposed select
  renders zero state options on this multi-workflow install (it also
  affects core's own `/admin/content/moderated` view here) — an upstream
  issue worth searching/filing in the core queue.
- Rendered JSON-LD is emitted by the `drupal/geo_starter_jsonld` companion module
  for Service (with provider `ContactPoint`, `PostalAddress`, and
  `hoursAvailable`), Answer (`Question`), Article, Evidence Source
  (`CreativeWork`), and gated `FAQPage`, `HowTo`, and `ItemList` section
  emission — covered by the module's PHPUnit Unit/Kernel/Functional suites in
  Drupal.org CI, the full-surface acceptance probe (23/23 on the 1.0.0 released-artifact install — see `docs/VALIDATION.md`,
  'Stable 1.0.0 Released-Artifact Proof'), and an offline schema.org domain-correctness check.
  The hosted schema.org validator pass is **done** (zero errors/warnings on
  rendered JSON-LD for all four node types, 2026-06-05/06 — see
  `docs/VALIDATION.md`). **Not yet done:** Google Rich-Results findings
  (snippet-mode run blocked for headless tooling; pending a manual or
  URL-mode run) — required before any rich-result eligibility claim.
- Fresh install is the only supported path within 1.x. Recipes are apply-once
  configuration artifacts; no `hook_update_N` migration ships. Within 1.x the
  content model, `@id` scheme, and entity-type set are frozen (additive-only);
  breaking changes force `2.0.0`. Pre-1.0 releases changed the content model
  without an upgrade path (e.g., `1.0.0-alpha4` replaced the contact panel's
  free-text hours field with a structured `office_hours` field); those installs
  are not upgrade-compatible with 1.0.0.
- **XML sitemap** ships via `simple_sitemap` (added 2026-06-07, WS-F): the
  four canonical node types + Canvas pages are configured for indexing; a fresh
  install's `/sitemap.xml` 404s until the first `automated_cron` run (or a
  manual `drush simple-sitemap:generate`) — recipes are config-only and cannot
  generate. Verified: config correct + generate → 26 URLs, all types, drafts
  excluded (`docs/VALIDATION.md` WS-F).
- **Internal search is NOT shipped** (deferred post-beta, WS-F decision
  2026-06-07): `drupal_cms_search` is not in the recipe. Site search is a
  site-UX feature, not the GEO discoverability primitive the sitemap covers;
  add `drupal_cms_search` to the `recipes:` list if you need it.
- Accessibility, responsive, performance, and cache behavior have not completed
  full release gates. A WS-F spot-check (2026-06-07) passed on the two public
  surfaces carrying WS-B markup (homepage + Service page): keyboard walks with a
  skip-link, visible focus, no traps; all WS-B CSS color pairs at WCAG AA. Not
  yet covered: the admin dashboard's keyboard pass (core Views + admin theme,
  no custom markup), responsive/perf/cache gates, and Mercury's own full WCAG
  conformance (the base theme's responsibility).
- Drupal Security Team advisory coverage now applies to the stable releases
  (verified 2026-07-15: project-page shield + `security covered="1"` in the
  release-history feed for 1.0.0/1.0.1/1.1.0). That is advisory handling for
  reported vulnerabilities, **not a security audit**; alphas/betas remain
  uncovered, and the Marketplace privacy/security attestations below are
  still outstanding. Report suspected vulnerabilities per `SECURITY.md`, not
  the public issue queue.
- Full released-artifact install proof passed on 2026-06-08 for the `1.0.0` package shape: default `stable` Composer floor, real d.o `geo_starter_jsonld 1.0.0` package, JSON-LD probe 23/23, content-graph-lint OK, all pages render — see `docs/VALIDATION.md`.

The `content_format` text format used by governed body fields is provided
by the composed Drupal CMS sub-recipes (see `recipe.yml` `recipes:`), not
by this recipe. Its allowed-HTML posture is therefore inherited; sites that
tighten or loosen it do so outside this recipe's contract.

## Canvas Component-Version Pins (known fragility)

The shipped `canvas.page_region.*` configs pin exact component-version
hashes, and canvas recomputes those hashes from current core schema data at
install time. The `1.1.0` pins match the current core era (11.4); a future
core change — or an install context that alters the hash inputs, such as a
non-English install language (drupal_cms #3573892) — can re-trigger a
canvas #3563959-class version mismatch (`OutOfRangeException` from
`assertVersionExists()` on every HTML page) until canvas supports
version-less recipe placements (#3571366). If a fresh install hits this,
check the project issue queue.

`ddev geo-install` is **experimental and currently known-broken**;
`tools/quickstart.sh` is the verified one-command path.

## Explicit Non-Goals

- No turnkey source-CMS importer automation.
- No automatic conversion between Canvas pages and Paragraph sections.
- No free mixing of Canvas and Paragraphs on the same canonical page.
- No required AI provider, AI Agents, RDF, hypermedia API, or built-in agent-write workflow. (A programmatic MCP introspection/write surface is an optional, experimental opt-in — see `docs/OPTIONAL_MCP.md` — never a recipe dependency; the recipe installs at the default `stable` floor with no MCP packages.)
- No guaranteed AI citations, rankings, rich results, or answer-engine placement.

## Marketplace Blockers

Do not submit to the Official Marketplace until the repository has:

- named creator/applicant and support owner;
- support and maintenance commitments;
- stable, supported required dependencies with no patches;
- representative screenshot and preview/demo URL;
- complete theme and authoring proof;
- WCAG 2.2 AA accessibility evidence;
- performance and responsive evidence;
- privacy/security attestations;
- final listing copy and marketing materials.
