# Optional: agent introspection via MCP (experimental opt-in)

> **Status: EXPERIMENTAL, unsupported, opt-in.** This is **not** part of the
> GEO Starter recipe and **not** a dependency of it. `composer require
> drupal/geo_starter` stays at the default `stable` floor with no MCP packages.
> Everything below is a manual add-on a site operator chooses to install.

The recipe answers recommendation #2 of Dries Buytaert's *"Do AI coding agents
recommend Drupal?"* (2026) — a typed way for agents to inspect/modify a running
site — only as this opt-in, because the upstream module is not yet released.

## Why it isn't shipped

[`drupal/mcp_server`](https://www.drupal.org/project/mcp_server) provides the
Model Context Protocol transport for Drupal, but **has no tagged release** — only
a `2.x` development branch (actively developed; last commit 2026-06). A recipe
that `require`d `drupal/mcp_server:^1.0` would be **uninstallable** (the
constraint matches nothing), which would break the recipe's clean stable-floor
install. So MCP stays out of the recipe and lives here as a dev-pinned opt-in.

## What works today (transport + auth foundation only)

You can stand up the MCP **transport and OAuth 2.1 auth** against the `2.x-dev`
branch. Pin the dev constraint **per package** and keep `prefer-stable` on — do
**not** set a global `minimum-stability: dev` (that cascades dev across your
whole project):

```jsonc
// composer.json (your site project, NOT the recipe)
{
  "require": {
    "drupal/mcp_server": "2.x-dev",
    "drupal/simple_oauth": "^6"
  },
  "minimum-stability": "stable",
  "prefer-stable": true
}
```

```bash
composer require 'drupal/mcp_server:2.x-dev' 'drupal/simple_oauth:^6'
drush en mcp_server simple_oauth -y
```

Then, in the MCP server + Simple OAuth admin config:

- Run the **HTTP transport with authentication required** — never expose it
  unauthenticated.
- Issue OAuth 2.1 clients **least privilege**: a read client should map to
  `access content` (published only); any write client should map to a role that
  can author and send to review but **cannot** publish (publication stays a
  human/editorial decision via the `geo_starter_editorial` workflow).
- Confirm anonymous JSON:API still exposes published content only (unchanged).

## What does NOT work yet (deferred)

- **Typed GEO content-model tools** (describe the model / read / draft-write /
  validate, and a moderation-gated agent role) are **not** available yet. They
  are intended to ship later as a separate companion package once
  `drupal/mcp_server` cuts a stable release — at which point that package gets
  its own security review of the agent-write surface. Until then, only the
  generic transport + auth foundation above is installable.

## Safer alternative

For agent inspection without installing anything, use the in-repo
machine-readable model (`docs/api/content-model.schema.json`,
`docs/api/openapi.yaml`) plus the read-only JSON:API surface, and author content
through the editorial workflow. See `docs/AGENT_GUIDE.md`.
