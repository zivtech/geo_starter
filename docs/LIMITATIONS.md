# Limitations

GEO Starter is not Marketplace-ready yet.

## Current Limits

- Basic public rendering uses core Olivero; no final GEO-specific theme or design system has been implemented.
- One Canvas Page shell exists, but no component-composed Canvas sample pages are included yet.
- The ten-bundle section library is config-complete, but `section_cta`,
  `section_alert`, and `section_media_text` ship without sample content, and
  section rendering has not had a design/semantic-template pass.
- `screenshot.webp` is a representative alpha screenshot from the rendered sample Service page, not a final Marketplace marketing image.
- Rendered JSON-LD is emitted by the `drupal/geo_starter_jsonld` companion module
  for Service (with provider `ContactPoint`, `PostalAddress`, and
  `hoursAvailable`), Answer (`Question`), Article, Evidence Source
  (`CreativeWork`), and gated `FAQPage`, `HowTo`, and `ItemList` section
  emission — covered by the module's PHPUnit Unit/Kernel/Functional suites in
  Drupal.org CI, the full-surface acceptance probe (23/23 on a fresh
  `1.0.0-alpha4` install), and an offline schema.org domain-correctness check.
  **Not yet done:** external (hosted) schema.org validator and Google
  Rich-Results checks — required before any rich-result eligibility claim.
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
