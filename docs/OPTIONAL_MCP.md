# Optional: agent introspection via MCP (experimental opt-in)

> **Status: EXPERIMENTAL, unsupported, opt-in.** This is **not** part of the
> GEO Starter recipe and **not** a dependency of it. The recipe stays at the
> default `stable` floor with no MCP packages. This is an evaluation boundary,
> not a recommendation to add MCP to a production site.

The recipe keeps a typed agent inspect/modify surface out of the base package
because the upstream module has no stable supported release.

## Why it isn't shipped

[`drupal/mcp_server`](https://www.drupal.org/project/mcp_server) now has a
`2.0.0-alpha1` release (June 2026), alongside its `2.x-dev` branch. It still
has **no stable supported release**. An alpha/dev requirement would violate the
recipe's default-stable posture and needs its own security, permissions, and
operational review. MCP therefore remains out of the recipe.

## What it means today

The upstream project describes STDIO/HTTP transport, Tool API integration, and
optional OAuth support. GEO Starter does not validate, configure, or recommend
any of those paths. An operator who elects to evaluate the alpha must use the
upstream documentation and complete a separate threat model. At a minimum:

- require authentication for non-public capabilities;
- grant least privilege, with any write role unable to publish; and
- verify anonymous JSON:API still exposes published content only.

## What does NOT work yet (deferred)

- **Typed GEO content-model tools** (describe the model / read / draft-write /
  validate, and a moderation-gated agent role) are **not** available. A future
  companion package would require a stable upstream release and its own
  security review before it could be proposed.

## Safer alternative

For agent inspection without installing anything, use the in-repo
machine-readable model (`docs/api/content-model.schema.json`,
`docs/api/openapi.yaml`) plus the read-only JSON:API surface, and author content
through the editorial workflow. See `docs/AGENT_GUIDE.md`.
