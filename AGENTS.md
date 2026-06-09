# Agent Instructions

GEO Starter is a Drupal CMS site-template recipe, not a module bundle, theme-only project, distribution, or importer.

## Common Agent Tasks

If you are an AI coding agent working *with* a GEO Starter site (scaffolding,
inspecting, or authoring content), start with **`docs/AGENT_GUIDE.md`** — it
walks the full install → inspect → modify → verify loop with copy-pasteable
commands. Quick map:

- **Scaffold** a running site in one command: `ddev start && ddev geo-install`
  (or `./scripts/quickstart.sh`).
- **Inspect** the model: fetch `docs/api/content-model.schema.json`, query
  `/jsonapi`, or call the `geo.describe_content_model` MCP tool (`docs/MCP.md`).
- **Modify**: create Drafts via the MCP write tools (Draft-only; see below).
- **Verify**: JSON-LD probe, schema.org validator, `moderation_state` checks.

The instructions below are for agents *developing the recipe itself*.

## Product Boundaries

- Keep `composer.json` type as `drupal-recipe`.
- Keep `recipe.yml` type as `Site`.
- Do not add dependency patches or pinned exact dependency versions.
- Do not require an AI provider, RDF, RDFa, or a hypermedia API in the base
  template. (MCP **is** now in scope via `drupal/mcp_server`; the typed tools
  ship in the companion `geo_starter_mcp` module, and agent writes are
  Draft-only — see `docs/MCP.md`.)
- Keep the recipe a config artifact: ship modules (e.g. `geo_starter_jsonld`,
  `geo_starter_mcp`) as separate Composer packages, never bundled inside it.
- Do not grant an agent role the publish transition; agent writes stay in Draft.
- Do not claim guaranteed AI citations, rankings, rich results, or answer-engine placement.
- Do not claim turnkey migration importer automation.
- Do not claim Canvas and Paragraphs can be freely mixed on the same canonical page.

## Current Alpha Scope

- Drupal CMS site-template package shape.
- Canvas and Paragraphs dependency posture.
- MVP content model for Service, Answer, Article, Evidence Source, Audience and Topic.
- Sample public-service content as the first proof wrapper.
- JSON:API access smoke-test evidence for current MVP content.

## Before Public Release

- Rerun a clean Drupal CMS install/apply proof after dependency changes.
- Replace `screenshot.webp` with a representative rendered site screenshot.
- Add or update project page copy, support policy, release notes, and validation evidence.
- Run accessibility, responsive, security/access, and copy/proposal critic gates.
