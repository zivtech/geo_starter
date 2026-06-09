# Agent Instructions

GEO Starter is a Drupal CMS site-template recipe, not a module bundle, theme-only project, distribution, or importer.

## Product Boundaries

- Keep `composer.json` type as `drupal-recipe`.
- Keep `recipe.yml` type as `Site`.
- Do not add dependency patches or pinned exact dependency versions.
- Do not require AI providers, AI Agents, MCP, RDF, hypermedia APIs, or agent-write workflows in the base template.
- Do not claim guaranteed AI citations, rankings, rich results, or answer-engine placement.
- Do not claim turnkey migration importer automation.
- Do not claim Canvas and Paragraphs can be freely mixed on the same canonical page.

## Current Scope (1.x stable)

- Drupal CMS site-template package shape, installable at the default `stable` Composer floor.
- Canvas and Paragraphs dual-lane authoring posture; Mercury public theme.
- Content model for Service, Answer, Article, Evidence Source, Audience and Topic — frozen additive-only within 1.x (see "Stability contract" in `README.md`). Breaking changes force `2.0.0`.
- Ten-bundle section library on `field_sections`; four component-composed Canvas sample pages.
- Schema.org JSON-LD via the required `drupal/geo_starter_jsonld` companion module.
- Sample public-service content exercising the JSON-LD path on install.
- Fresh install is the only supported path; never add `hook_update_N`-style upgrade promises.

## Before Public Release

- Rerun the released-artifact install proof after dependency, recipe config, or content changes (`docs/RELEASE_CHECKLIST.md`); run `tools/content-graph-lint.py` before any release.
- Keep `screenshot.webp` representative of the current rendered site.
- Update project page copy (`docs/PROJECT_PAGE_DRAFT.md`), support policy, release notes, and validation evidence together — they must tell the same story as `README.md`.
- Run accessibility, responsive, security/access, and copy/proposal critic gates.
