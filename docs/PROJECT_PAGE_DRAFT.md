# Drupal.org Project Page Draft

**Status:** Canonical source for the live project page at
https://www.drupal.org/project/geo_starter. Synced for `1.0.0-alpha4`
(2026-06-04). Do not use for Marketplace listing without a final
copy/proposal review.

## Summary

Drupal is the CMS for an age of agents. GEO Starter turns that position into a Drupal CMS recipe for governed, source-backed content that people can read and retrieval systems can inspect.

## Description

Drupal is the CMS for an age of agents.

Agents need more than pages. They need reliable content with stable URLs, visible sources, review dates, structured fields, permissions, and editorial workflows.

Drupal already treats those needs as core publishing concerns. GEO Starter packages that strength as a Drupal CMS site-template scaffold for teams moving from headless/composable or legacy page/post CMS stacks into a governed foundation for Generative Engine Optimization (GEO).

The Community alpha provides a destination model for source-backed services, answers, articles, evidence records, and controlled vocabularies — plus a structured section library and a companion module that emits schema.org JSON-LD from the same governed fields the visible page renders.

This alpha is not a turnkey migration tool. It is not Marketplace-ready yet.

## Why Drupal For Agents

- Agents need content they can inspect, cite, and evaluate.
- Drupal brings structured entities, taxonomy, revisions, moderation, permissions, APIs, and rendered public pages into one open platform.
- GEO Starter makes that agent-era Drupal posture concrete with a governed content model, a structured section library, machine-readable structured data, and sample content.
- The recipe does not ship agent-write automation. It prepares the content foundation that agent and tool workflows need before they can be trusted.

## Features

- Drupal CMS site-template recipe shape.
- Content model for Service, Answer, Article, and Evidence Source, with controlled Audience and Topic vocabularies.
- Schema.org JSON-LD emission through the required companion module [GEO Starter JSON-LD](https://www.drupal.org/project/geo_starter_jsonld): `Service` (with provider `ContactPoint`, `PostalAddress`, and `hoursAvailable` opening hours), `Question`/`Answer`, `Article`, `CreativeWork` evidence records with resolvable cross-page `citation` links, and gated `FAQPage`, `HowTo`, and `ItemList` emission from section content. Emitted only on full canonical published pages, and never beyond what the visible page renders.
- A ten-bundle structured section library: FAQ, step list (the `HowTo` source), card grid (the `ItemList` source), contact panel (the `ContactPoint` source, with structured office hours), call-to-action, alert, media-and-text, and a general section — attached to Service, Answer, and Article content.
- Editorial moderation workflow (draft → review → published → archived) with reviewed-date provenance on all four content types.
- Sample public-service content, including worked FAQ, step-list, card-grid, and contact-panel sections that exercise the JSON-LD path on install.
- Canvas Page shell for the visual-page authoring lane.
- JSON:API access smoke-test evidence for published and draft content.
- Documentation for migration mapping, source visibility, schema boundaries, and validation evidence.

## GEO Readiness

This section shows what the Community alpha supports now, what is partial, and what is still planned. It focuses on capabilities a CMS needs when search engines, retrieval systems, and agents decide what to cite.

### Content structure and operations

- Structured content model: Yes. Service, Answer, Article, and Evidence Source entities use shared fields plus controlled Topic and Audience vocabularies.
- Composable content operations: Partial. Typed content, taxonomy, entity references, moderation, JSON:API, and the ten-bundle section library ship now. Views and editor dashboards are future work.
- Editorial governance: Yes. All starter content types use a draft, review, published, and archived workflow with reviewed-date provenance.

### Retrieval and rendering output

- Rendered semantic HTML: Partial. Structured node pages and section paragraphs render through core Olivero. Final theme and semantic-template review remain open.
- Schema.org metadata from fields: Yes, with a stated validation boundary. The companion module emits one schema.org `@graph` per page from the same governed fields the page renders, covered by PHPUnit suites in Drupal.org CI, a full-surface acceptance probe, and an offline schema.org domain-correctness check. External validator and rich-results checks have not been run yet, so no rich-result eligibility is claimed.
- Structured API access: Yes. JSON:API is enabled and access-tested for published and draft content. This is a machine-readable integration surface; it is not the main channel answer engines use for citations.

### Agent and ownership readiness

- Agent and tool protocol story: Planned. Drupal AI and agent paths are documented as future integration surfaces. No write-capable agent workflow ships in this alpha.
- AI provider choice: Open. The recipe configures no AI provider. Provider choice remains a Drupal AI integration decision, with no proprietary runtime.
- Open ownership and no lock-in: Yes. GEO Starter is distributed as an open Drupal recipe with no proprietary runtime dependency.

### Migration readiness

- Migration destination for headless/composable and page/post stacks: Partial. A destination content model and migration map exist. Turnkey importer automation is not included in the alpha.

### Scope and honest limits

- AI citation, ranking, rich results, and answer-engine placement are never guaranteed. This recipe builds the structural foundation that helps; it does not promise outcomes.
- AI engines do not behave identically, so "AI visibility" is not a single switch. GEO Starter strengthens the rendered-HTML, structured-content, and cited-source path that documentation- and answer-citing engines favor. It does not, by itself, address community/forum or homepage-led citation patterns. Identify the engines your audience actually uses and measure per engine.

## Post-Installation

Applying the recipe imports the content model, the section library, and the sample content automatically. Review the included content types, vocabularies, sample pages, and the emitted JSON-LD on the sample Service page.

See `docs/INSTALL.md` and `docs/VALIDATION.md`.

## Requirements

- Drupal CMS compatible project (Drupal 11).
- Composer configured for Drupal packages.
- Required Drupal packages listed in `composer.json`, including Canvas, Paragraphs, Entity Reference Revisions, Office Hours, and the GEO Starter JSON-LD companion module.

## Current Limitations

- Basic public rendering uses core Olivero; no finished GEO-specific theme yet.
- No component-composed Canvas sample pages yet; one Canvas Page shell ships.
- The call-to-action, alert, and media-and-text sections ship as authoring bundles without sample content; section rendering has not had a design pass.
- No Views/editor dashboard implementation yet.
- No turnkey source-CMS importer automation.
- Structured-data emission is covered by tests, an acceptance probe, and an offline schema.org domain check; external validator and rich-results checks are still pending.
- Alpha releases may change the content model between versions without an upgrade path; alpha4 itself changed the contact-hours field in a way that requires a fresh install. Treat alphas as fresh-install previews.
- No guaranteed AI citations, rankings, rich results, or answer-engine placement.
- No required AI provider, MCP, RDF, or agent-write workflow.
- Alpha screenshot is representative, but not final Marketplace imagery.

## Documentation

- `README.md`
- `docs/INSTALL.md`
- `docs/LIMITATIONS.md`
- `docs/VALIDATION.md`
- `docs/PUBLISHING_AND_ACCEPTANCE_PLAN.md`
- `docs/TECHNICAL_ACCEPTANCE_PLAN.md`

## Support

Use the Drupal.org issue queue for Community alpha support. See `SUPPORT.md` and `SECURITY.md`.
