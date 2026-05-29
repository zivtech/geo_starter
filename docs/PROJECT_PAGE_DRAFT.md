# Drupal.org Project Page Draft

**Status:** Draft for Community alpha. Do not use for Marketplace listing without a final copy/proposal review.

## Summary

Migration-ready Drupal CMS starter for governed, sourceable content people can read and retrieval systems can inspect.

## Description

GEO Starter is a Drupal CMS site-template scaffold for teams moving from headless/composable or legacy page/post CMS stacks into a governed Drupal foundation for Generative Engine Optimization (GEO).

The Community alpha provides a destination model for sourceable services, answers, articles, evidence records, and controlled vocabularies. It is designed to help teams preserve familiar publishing and page-building patterns while adding stronger governance, visible sources, review dates, structured fields, and retrieval-friendly public pages.

This alpha is not a turnkey migration tool and is not Marketplace-ready yet.

## Features

- Drupal CMS site-template recipe shape.
- MVP content model for Service, Answer, Article, Evidence Source, Audience, Topic, and Service area.
- Sample public-service content for the first proof wrapper.
- Canvas Page shell for visual-page proof.
- `geo_starter_section` Paragraph type attached to Service, Answer, and Article content.
- Editorial moderation workflow for governed publishing.
- JSON:API access smoke-test evidence for published and draft content.
- Documentation for migration mapping, source visibility, schema boundaries, and future context-surface research.

## Demo Feature Breakdown

| Drupal AI-era demo claim | Included now | Notes |
| --- | --- | --- |
| Structured content model | Yes | Typed Drupal content for services, reusable answers, articles, evidence, and vocabularies. |
| Composable content operations | Partial | Structured content, taxonomy, references, moderation, JSON:API, and Paragraph sections are present. Views/editor dashboards and bulk operations are future work. |
| Editorial governance | Yes | Workflow states and moderation are included for the starter content types. |
| AI visibility output | Partial | Public pages render and JSON:API access is proven. Final theme, semantic template review, and JSON-LD are still open. |
| API access for AI consumption | Yes | JSON:API is enabled and access-tested. |
| Agent/tool protocol story | Planned | No agent workflow or AI provider configuration ships in this alpha. |
| Provider choice and ownership | Partial | The package is open Drupal configuration with no proprietary AI runtime, but it does not configure an AI provider. |
| Contentful-to-Drupal migration wedge | Partial | Destination model and migration map exist; importer automation is not included. |

## Post-Installation

After applying the starter, review the included content types, vocabularies, and sample content. The current alpha includes helper scripts for sample content and JSON:API access probes.

See `docs/INSTALL.md` and `docs/VALIDATION.md`.

## Requirements

- Drupal CMS compatible project.
- Composer configured for Drupal packages.
- Required Drupal packages listed in `composer.json`, including Canvas, Paragraphs, and Entity Reference Revisions.

## Current Limitations

- No finished theme yet.
- No component-composed Canvas pages yet.
- Only one broad proof Paragraph type; no specialized section library yet.
- No Views/editor dashboard implementation yet.
- No turnkey source-CMS importer automation.
- No guaranteed AI citations, rankings, rich results, or answer-engine placement.
- No required AI provider, MCP, RDF, or agent-write workflow.
- Placeholder screenshot must be replaced before public release.

## Documentation

- `README.md`
- `docs/INSTALL.md`
- `docs/LIMITATIONS.md`
- `docs/VALIDATION.md`
- `docs/PUBLISHING_AND_ACCEPTANCE_PLAN.md`
- `docs/TECHNICAL_ACCEPTANCE_PLAN.md`

## Support

Use the repository issue queue for Community alpha support. See `SUPPORT.md` and `SECURITY.md`.
