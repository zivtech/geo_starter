# Agent Instructions

GEO Starter is a Drupal CMS site-template recipe, not a module bundle, theme-only project, distribution, or importer.

## Quick Reference

The 80% path for working with this repository:

- **Install a site from it:** `tools/quickstart.sh my-site 1.0.1` (or the
  manual steps in `docs/INSTALL.md`). `composer require drupal/geo_starter`
  does NOT work — site templates are not served by the packages.drupal.org
  Composer facade.
- **Layout:** `recipe.yml` (recipes/modules/config actions) · `config/`
  (176 exported config files; no `uuid:`/`_core:` keys — keep it that way) ·
  `content/` (default content with `_meta.depends` graphs) · `tools/`
  (dev scripts) · `docs/` (the user-facing doc set; `docs/plans/` is
  internal history, export-ignored).
- **Content model:** Service, Answer, Article, Evidence Source node types;
  Topic + Audience vocabularies; ten section bundles on `field_sections`
  (all three content node types). Map: `docs/AUTHORING_MODEL.md`,
  `docs/SCHEMA_MAP.md`. Machine-readable twin (for AI agents):
  `docs/api/content-model.schema.json` (generated from `config/`, drift-guarded
  via `tools/generate-content-model-schema.php --check`) + `docs/api/openapi.yaml`.
- **Working as an AI agent with a site built from this recipe:**
  `docs/AGENT_GUIDE.md` (install → inspect → modify → verify). Content authoring
  goes through the editorial workflow; a programmatic MCP surface is an optional,
  experimental opt-in only (`docs/OPTIONAL_MCP.md`), never a recipe dependency.
- **JSON-LD** comes from the companion module `drupal/geo_starter_jsonld`
  (separate package/repo — a recipe cannot bundle a module). Extend emission
  there via tagged services: `geo_starter_jsonld.node_normalizer` /
  `geo_starter_jsonld.paragraph_contributor`. Never emit beyond what the
  rendered page shows (parity rule).
- **Add a section bundle (example-first):** copy an existing
  `paragraphs.paragraphs_type.*` + its `field.*` + form/view display configs;
  add the bundle to `target_bundles` in
  `field.field.node.{service,answer,article}.field_sections`; ship sample
  content in `content/paragraph/` with complete `_meta.depends`; if it should
  emit JSON-LD, add a paragraph contributor in the companion module.
  Additive-only within 1.x — never delete/retype fields (contract in
  `README.md`).
- **Validate before any commit that touches `config/` or `content/`:**
  `python3 tools/content-graph-lint.py` (depends completeness + acyclicity)
  and `composer validate --strict`. Config/content changes additionally
  require re-running the released-artifact install proof before a release
  (`docs/RELEASE_CHECKLIST.md`) — do not skip it because the lint is green.

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
