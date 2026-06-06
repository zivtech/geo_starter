# Limitations

GEO Starter is not Marketplace-ready yet.

## Current Limits

- Public rendering uses the Drupal CMS **Mercury** theme's stock styling; no
  GEO-specific theme or design system has been implemented. Node pages render
  as a flat field list — the section rendering/design pass for the ten
  paragraph bundles has not happened yet.
- Four component-composed Canvas sample pages ship (homepage, migration
  landing, topic hub, campaign), built from stock Mercury SDCs and validated
  against the installed `drupal/canvas` 1.4 minor. A Canvas minor update
  could invalidate the committed component trees (the recipe deliberately
  does not pin an exact version).
- Nine of the ten section bundles ship sample content (`section_cta`,
  `section_alert`, and `section_media_text` were added 2026-06-05/06; the
  generic `geo_starter_section` fallback bundle has no sample). Section
  rendering has not had a design/semantic-template pass — that pass's
  every-bundle-visible requirement will need a `geo_starter_section`
  sample too.
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
  Drupal.org CI, the full-surface acceptance probe (23/23 on a fresh
  `1.0.0-alpha4` install), and an offline schema.org domain-correctness check.
  The hosted schema.org validator pass is **done** (zero errors/warnings on
  rendered JSON-LD for all four node types, 2026-06-05/06 — see
  `docs/VALIDATION.md`). **Not yet done:** Google Rich-Results findings
  (snippet-mode run blocked for headless tooling; pending a manual or
  URL-mode run) — required before any rich-result eligibility claim.
- Alpha-to-alpha releases may change the content model without an upgrade path.
  `1.0.0-alpha4` replaced the contact panel's free-text hours field with a
  structured `office_hours` field; existing alpha2/alpha3 installs must drop and
  recreate `field_section_hours`. Treat alphas as fresh-install previews.
- Sitemap and internal search behavior have not been proven.
- Accessibility, responsive, performance, and cache behavior have not completed release gates.
- Full install/apply proof passed on 2026-06-02 for the current `1.0.0-alpha4` package shape (all ten paragraph bundles import; acceptance probe passes).

## Explicit Non-Goals

- No turnkey source-CMS importer automation in Community alpha.
- No automatic conversion between Canvas pages and Paragraph sections.
- No free mixing of Canvas and Paragraphs on the same canonical page.
- No required AI provider, AI Agents, MCP, RDF, hypermedia API, or agent-write workflow.
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
