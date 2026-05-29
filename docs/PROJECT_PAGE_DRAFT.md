# Drupal.org Project Page Draft

**Status:** Draft for Community alpha. Do not use for Marketplace listing without a final copy/proposal review.

## Summary

Drupal is the CMS for an age of agents. GEO Starter turns that position into a Drupal CMS recipe for governed, source-backed content that people can read and retrieval systems can inspect.

## Description

Drupal is the CMS for an age of agents.

Agents need more than pages. They need reliable content with stable URLs, visible sources, review dates, structured fields, permissions, and editorial workflows.

Drupal already treats those needs as core publishing concerns. GEO Starter packages that strength as a Drupal CMS site-template scaffold for teams moving from headless/composable or legacy page/post CMS stacks into a governed foundation for Generative Engine Optimization (GEO).

The Community alpha provides a destination model for source-backed services, answers, articles, evidence records, and controlled vocabularies. It helps teams preserve familiar publishing and page-building patterns while adding stronger governance, source visibility, structured fields, and retrieval-friendly public pages.

This alpha is not a turnkey migration tool. It is not Marketplace-ready yet.

## Why Drupal For Agents

- Agents need content they can inspect, cite, and evaluate.
- Drupal brings structured entities, taxonomy, revisions, moderation, permissions, APIs, and rendered public pages into one open platform.
- GEO Starter makes that agent-era Drupal posture concrete with a governed content model and sample content.
- The recipe does not ship agent-write automation. It prepares the content foundation that agent and tool workflows need before they can be trusted.

## Features

- Drupal CMS site-template recipe shape.
- MVP content model for Service, Answer, Article, Evidence Source, Audience, Topic, and Service area.
- Agent-era Drupal positioning grounded in structured content, governance, source visibility, and open ownership.
- Sample public-service content for the first proof wrapper.
- Canvas Page shell for visual-page proof.
- `geo_starter_section` Paragraph type attached to Service, Answer, and Article content.
- Editorial moderation workflow for governed publishing.
- JSON:API access smoke-test evidence for published and draft content.
- Documentation for migration mapping, source visibility, schema boundaries, and future context-surface research.

## GEO Readiness

This section shows what the Community alpha supports now, what is partial, and what is still planned. It focuses on capabilities a CMS needs when search engines, retrieval systems, and agents decide what to cite.

**Content structure and operations**

- Structured content model: Yes. Service, Answer, Article, and Evidence Source entities use shared fields plus controlled Topic, Audience, and Service area vocabularies.
- Composable content operations: Partial. Typed content, taxonomy, entity references, moderation, JSON:API, and one reusable `geo_starter_section` Paragraph type ship now. Views and editor dashboards are future work.
- Editorial governance: Yes. All starter content types use a draft, review, published, and archived workflow with reviewed-date provenance.

**Retrieval and rendering output**

- Rendered semantic HTML: Partial. Structured node pages and a Paragraph section render through core Olivero. Final theme and semantic-template review remain open.
- Schema.org metadata from fields: Planned. A schema map exists. JSON-LD is deferred until rendered-content parity is proven, so the recipe does not emit structured-data claims the visible page cannot support.
- Structured API access: Yes. JSON:API is enabled and access-tested for published and draft content. This is a machine-readable integration surface; it is not the main channel answer engines use for citations.

**Agent and ownership readiness**

- Agent and tool protocol story: Planned. Drupal AI and agent paths are documented as future integration surfaces. No write-capable agent workflow ships in this alpha.
- AI provider choice: Open. The recipe configures no AI provider. Provider choice remains a Drupal AI integration decision, with no proprietary runtime.
- Open ownership and no lock-in: Yes. GEO Starter is distributed as an open Drupal recipe with no proprietary runtime dependency.

**Migration readiness**

- Migration destination for headless/composable and page/post stacks: Partial. A destination content model and migration map exist. Turnkey importer automation is not included in the alpha.

**Scope and honest limits**

- AI citation, ranking, rich results, and answer-engine placement are never guaranteed. This recipe builds the structural foundation that helps; it does not promise outcomes.
- AI engines do not behave identically, so "AI visibility" is not a single switch. GEO Starter strengthens the rendered-HTML, structured-content, and cited-source path that documentation- and answer-citing engines favor. It does not, by itself, address community/forum or homepage-led citation patterns. Identify the engines your audience actually uses and measure per engine.

## Post-Installation

After applying the starter, review the included content types, vocabularies, and sample content. The current alpha includes helper scripts for sample content and JSON:API access probes.

See `docs/INSTALL.md` and `docs/VALIDATION.md`.

## Requirements

- Drupal CMS compatible project.
- Composer configured for Drupal packages.
- Required Drupal packages listed in `composer.json`, including Canvas, Paragraphs, and Entity Reference Revisions.

## Current Limitations

- Basic public rendering uses core Olivero; no finished GEO-specific theme yet.
- No component-composed Canvas pages yet.
- Only one broad proof Paragraph type; no specialized section library yet.
- No Views/editor dashboard implementation yet.
- No turnkey source-CMS importer automation.
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
