# Drupal.org Project Page Draft

**Status:** Canonical source for the live project page at
https://www.drupal.org/project/geo_starter. Synced for `1.0.0`
(2026-06-09). Do not use for Marketplace listing without a final
copy/proposal review.

## Summary

Drupal is the CMS for an age of agents. GEO Starter turns that position into a Drupal CMS site template for governed, source-backed content that people can read and retrieval systems can inspect.

## Description

Drupal is the CMS for an age of agents.

Agents need more than pages. They need reliable content with stable URLs, visible sources, review dates, structured fields, permissions, and editorial workflows.

Drupal already treats those needs as core publishing concerns. GEO Starter packages that strength as a Drupal CMS site template for teams moving from headless/composable or legacy page/post CMS stacks into a governed foundation for Generative Engine Optimization (GEO).

The stable 1.x line provides a destination model for source-backed services, answers, articles, evidence records, and controlled vocabularies — plus a ten-bundle structured section library with semantic rendering, four component-composed Canvas sample pages on the Mercury theme, an editorial dashboard, an XML sitemap, and a companion module that emits schema.org JSON-LD from the same governed fields the visible page renders.

From `1.0.0` the content model is under a stability contract: fresh install is the only supported path, changes within 1.x are additive-only, and breaking changes force `2.0.0`.

This is not a turnkey migration tool. It is not Marketplace-ready yet.

## Why Drupal For Agents

- Agents need content they can inspect, cite, and evaluate.
- Drupal brings structured entities, taxonomy, revisions, moderation, permissions, APIs, and rendered public pages into one open platform.
- GEO Starter makes that agent-era Drupal posture concrete with a governed content model, a structured section library, machine-readable structured data, and sample content.
- The recipe does not ship agent-write automation. It prepares the content foundation that agent and tool workflows need before they can be trusted.

## Features

- Drupal CMS site-template recipe shape, installable at the default `stable` Composer minimum-stability floor.
- Content model for Service, Answer, Article, and Evidence Source, with controlled Audience and Topic vocabularies.
- Schema.org JSON-LD emission through the required companion module [GEO Starter JSON-LD](https://www.drupal.org/project/geo_starter_jsonld): `Service` (with provider `ContactPoint`, `PostalAddress`, and `hoursAvailable` opening hours), `Question`/`Answer`, `Article`, `CreativeWork` evidence records with resolvable cross-page `citation` links, and gated `FAQPage`, `HowTo`, and `ItemList` emission from section content. Emitted only on full canonical published pages, and never beyond what the visible page renders. Passes the hosted schema.org validator with zero errors and warnings on all four node types.
- A ten-bundle structured section library: FAQ, step list (the `HowTo` source), card grid (the `ItemList` source), contact panel (the `ContactPoint` source, with structured office hours), call-to-action, alert, media-and-text, and a general section — attached to Service, Answer, and Article content, with semantic HTML templates (definition-list FAQ, ordered steps, `<address>` contact, accented callouts).
- Four component-composed Canvas sample pages — homepage, migration landing, topic hub, and campaign — built from stock Mercury components, plus the Canvas front-page shell.
- Mercury as the public frontend theme.
- Editorial moderation workflow (draft → review → published → archived) with reviewed-date provenance on all four content types, and an editorial dashboard at `/admin/content/geo`.
- XML sitemap (`simple_sitemap`) indexing the canonical content types and Canvas pages for crawler and agent discoverability.
- Sample public-service content, including worked FAQ, step-list, card-grid, and contact-panel sections that exercise the JSON-LD path on install.
- JSON:API access proof for published and draft content on a fresh install.
- Documentation for installation, migration mapping, source visibility, schema boundaries, and validation evidence.

## GEO Readiness

This section shows what the 1.x stable line supports now, what is partial, and what remains open. It focuses on capabilities a CMS needs when search engines, retrieval systems, and agents decide what to cite.

### Content structure and operations

- Structured content model: Yes. Service, Answer, Article, and Evidence Source entities use shared fields plus controlled Topic and Audience vocabularies.
- Composable content operations: Yes. Typed content, taxonomy, entity references, moderation, JSON:API, the ten-bundle section library, four component-composed Canvas pages, and an editorial dashboard at `/admin/content/geo`.
- Editorial governance: Yes. All starter content types use a draft, review, published, and archived workflow with reviewed-date provenance.

### Retrieval and rendering output

- Rendered semantic HTML: Partial. Section bundles render through semantic templates (heading hierarchy, definition-list FAQ, ordered steps, `<address>` contact) on top of Mercury's stock styling. The node field stack above the sections still renders through core's classless field template; a GEO-specific design system is not included.
- Schema.org metadata from fields: Yes, with a stated validation boundary. The companion module emits one schema.org `@graph` per page from the same governed fields the page renders, covered by PHPUnit suites in Drupal.org CI, a full-surface acceptance probe, an offline schema.org domain-correctness check, and a hosted schema.org validator pass (zero errors/warnings, all four node types). Google Rich-Results findings are still pending, so no rich-result eligibility is claimed.
- Structured API access: Yes. JSON:API is enabled and access-tested for published and draft content. This is a machine-readable integration surface; it is not the main channel answer engines use for citations.

### Agent and ownership readiness

- Agent and tool protocol story: Planned. Drupal AI and agent paths are documented as future integration surfaces. No write-capable agent workflow ships.
- AI provider choice: Open. The recipe configures no AI provider. Provider choice remains a Drupal AI integration decision, with no proprietary runtime.
- Open ownership and no lock-in: Yes. GEO Starter is distributed as an open Drupal recipe with no proprietary runtime dependency.

### Migration readiness

- Migration destination for headless/composable and page/post stacks: Partial. A destination content model and migration map exist. Turnkey importer automation is not included.

### Scope and honest limits

- AI citation, ranking, rich results, and answer-engine placement are never guaranteed. This recipe builds the structural foundation that helps; it does not promise outcomes.
- AI engines do not behave identically, so "AI visibility" is not a single switch. GEO Starter strengthens the rendered-HTML, structured-content, and cited-source path that documentation- and answer-citing engines favor. It does not, by itself, address community/forum or homepage-led citation patterns. Identify the engines your audience actually uses and measure per engine.

## Installation

Fresh install only. Site templates are not served by the packages.drupal.org Composer facade, so the recipe tree is placed from its release tag into a `composer create-project drupal/cms` project — see `docs/INSTALL.md` in the repository for the verified steps. Applying the recipe imports the content model, the section library, and the sample content automatically; run cron once to populate `/sitemap.xml`.

Review the included content types, vocabularies, sample pages, the editorial dashboard at `/admin/content/geo`, and the emitted JSON-LD on the sample Service page.

## Requirements

- Drupal CMS 2.1+ project (Drupal core 11.3+, PHP 8.3+).
- Composer at the default `stable` minimum-stability floor — no override needed.
- Required Drupal packages listed in `composer.json`: Canvas (`>=1.4 <1.6`), Mercury (`>=1.0.5 <1.1`), Paragraphs, Entity Reference Revisions, Office Hours, Simple XML Sitemap, the Drupal CMS admin/media/privacy/SEO recipes, and the GEO Starter JSON-LD companion module (`^1.0`). The Canvas and Mercury minors are capped to the validated range.

## Current Limitations

- Lightly-styled semantic rendering, not a design system: the node field stack above the sections renders through core's classless field template, and no GEO-specific theme ships.
- Google Rich-Results findings are pending; the hosted schema.org validator passes with zero errors/warnings. No rich-result eligibility is claimed.
- Fresh install only — no in-place upgrades, including from pre-1.0 releases.
- No turnkey source-CMS importer automation.
- No required AI provider, MCP, RDF, or agent-write workflow.
- Not covered by the Drupal Security Team yet (opt-in application is a post-1.0 follow-up).
- No guaranteed AI citations, rankings, rich results, or answer-engine placement.
- Not Marketplace-ready; the screenshot is representative, not final Marketplace imagery.

## Documentation

- `README.md`
- `docs/INSTALL.md`
- `docs/LIMITATIONS.md`
- `docs/VALIDATION.md`
- `docs/AUTHORING_MODEL.md`
- `docs/MIGRATION_MAP.md`
- `docs/SCHEMA_MAP.md`

## Support

Use the Drupal.org issue queue for support: bug reports, install failures, documentation gaps, and release-readiness findings. See `SUPPORT.md` and `SECURITY.md`.
