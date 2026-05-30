# Limitations

GEO Starter is not Marketplace-ready yet.

## Current Limits

- Basic public rendering uses core Olivero; no final GEO-specific theme or design system has been implemented.
- One Canvas Page shell exists, but no component-composed Canvas sample pages are included yet.
- One broad proof Paragraph type, `geo_starter_section`, exists with `field_sections` config on Service, Answer, and Article. Specialized Paragraph bundles are not included yet.
- `screenshot.webp` is a representative alpha screenshot from the rendered sample Service page, not a final Marketplace marketing image.
- Rendered JSON-LD has not been implemented or validated.
- Sitemap and internal search behavior have not been proven.
- Accessibility, responsive, performance, and cache behavior have not completed release gates.
- Full install/apply proof passed on 2026-05-29 for the current `1.0.0-alpha2` corrected-taxonomy package shape.

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
