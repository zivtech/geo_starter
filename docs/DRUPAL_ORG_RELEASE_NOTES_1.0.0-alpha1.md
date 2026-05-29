# GEO Starter 1.0.0-alpha1 Release Notes

> **Reflects the corrected taxonomy.** `topic` is the single subject axis (`field_topic`
> required on Service and Answer); the `service_area` vocabulary and `field_service_area`
> are removed. Validated on a fresh Drupal CMS install on 2026-05-29 — see
> `docs/VALIDATION.md`, "Corrected-Taxonomy Acceptance Proof."

GEO Starter is an early Drupal CMS site-template recipe for an age of agents. It shows how Drupal can provide governed, source-backed content that people can read and retrieval systems can inspect.

This first alpha is for evaluation and feedback. It is not Marketplace-ready, and it is not a turnkey migration tool.

## Included In This Alpha

- Drupal CMS site-template recipe shape with `type: Site`.
- MVP content model for Service, Answer, Article, Evidence Source, Audience and Topic.
- Sample public-service content for install and JSON:API access proof.
- Canvas as the visual-page authoring lane, with a Canvas Page shell.
- Paragraphs and Entity Reference Revisions, with `geo_starter_section` attached to Service, Answer, and Article nodes.
- Olivero as the basic public frontend theme for alpha rendering.
- Clean install, rendered-page, YAML parsing, and JSON:API published/draft access validation evidence.
- Alpha documentation for installation, limitations, dependencies, content licenses, migration mapping, schema boundaries, support, and security reporting.

## What This Proves

- The package can be required into a fresh Drupal CMS project.
- The recipe can be selected as the installation recipe for a fresh Drupal CMS site.
- The current dependency set resolves without patches or exact pins.
- Starter content imports with the expected counts.
- Anonymous JSON:API access protects draft node content for the current content model.
- The recipe installs a Paragraph authoring lane for structured content.
- A Canvas Page shell can be imported and served.

## Current Limits

- No finished GEO-specific theme or design system yet.
- No component-composed Canvas sample pages yet.
- No specialized Paragraph bundles beyond the broad `geo_starter_section` proof type.
- No turnkey source-CMS importer automation.
- No rendered JSON-LD/schema output yet.
- No guaranteed AI citations, rankings, rich results, or answer-engine placement.
- No required AI provider, MCP, RDF, or agent-write workflow.

See `docs/VALIDATION.md`, `docs/LIMITATIONS.md`, and `docs/TECHNICAL_ACCEPTANCE_PLAN.md` for the current proof state and remaining gates.
