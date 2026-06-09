# Agent introspection: the MCP server

GEO Starter exposes a [Model Context Protocol](https://modelcontextprotocol.io)
endpoint so AI coding agents can inspect and safely modify a **running** site —
the tight `install → inspect → modify → verify` loop that recommendation #2 of
Dries Buytaert's *"Do AI coding agents recommend Drupal?"* (2026) calls for.

This is a deliberate change from the project's earlier posture, which listed
MCP/agent-write as a non-goal. The capability is now in scope, gated by a
threat model (see `SECURITY.md`).

## What ships where

| Layer | Package | Provides |
| --- | --- | --- |
| Transport + auth | `drupal/mcp_server` + `drupal/simple_oauth` | MCP over HTTP/STDIO, OAuth 2.1. Required by the recipe and enabled by `recipe.yml`. |
| Typed GEO tools | `drupal/geo_starter_mcp` (companion module, source in `geo_starter_mcp/`) | `geo.describe_content_model`, `geo.list_nodes`, `geo.get_node`, `geo.validate_node`, `geo.create_node`, `geo.update_node`, and the moderation-gated `geo_agent` role. |

The base recipe installs the transport + auth foundation. The **typed** tools
and the agent role ship in the companion module (a recipe cannot bundle a
module — same pattern as `geo_starter_jsonld`). See `geo_starter_mcp/README.md`.

## Tools

| Tool | Kind | Returns / does |
| --- | --- | --- |
| `geo.describe_content_model` | read | The live content model (four node types, fields, references, schema.org hints). Same shape as `docs/api/content-model.schema.json`, read from the running site. |
| `geo.list_nodes` | read | UUIDs/titles for a bundle (published only for the read scope). |
| `geo.get_node` | read | One node by UUID. |
| `geo.validate_node` | read | Validates a payload against the live model before you write. |
| `geo.create_node` | write | Creates a node **in Draft** (never published). |
| `geo.update_node` | write | Updates a node as a **new Draft revision**. |

## Auth and scopes

OAuth 2.1 via `simple_oauth`. Two scopes:

- `geo:read` → `access content` (published content for the read client).
- `geo:write` → the `geo_agent` role: author + send-to-review, **never publish**.

Run the HTTP transport with auth **required**. Do not enable the write tools
with auth disabled.

## Write safety (why agents can't publish)

Every write goes through `DraftWriter`, which forces `moderation_state: draft`
and `status: FALSE`. The `geo_agent` role is not granted the `publish`
transition of the `geo_starter_editorial` workflow. An agent can draft and
route content for review; a human (or an explicitly privileged role) publishes.
This keeps agent activity inside the existing editorial governance rather than
around it.

## Example MCP client config

```json
{
  "mcpServers": {
    "geo-starter": {
      "type": "http",
      "url": "https://geo-starter.ddev.site/mcp",
      "headers": { "Authorization": "Bearer ${GEO_STARTER_TOKEN}" }
    }
  }
}
```

(Confirm the endpoint path against your `mcp_server` release; obtain the token
via the OAuth client-credentials grant with only the scopes the agent needs.)

## Status / verification

- **Shipped & framework-stable:** the `geo_starter_mcp` services (live
  introspection + moderation-gated writes) and the `geo_agent` role config.
- **Integration step (verify on your stack):** registering the six tools with
  `mcp_server` and creating the two `simple_oauth` scopes — these depend on the
  installed module versions; see `geo_starter_mcp/README.md`.
- **Live verification:** has **not** been run in this repo (no live Drupal
  here). Use the checklist in `docs/DEMO_RUNBOOK.md` before relying on agent
  writes: confirm `geo.describe_content_model` returns the model, a Draft
  create lands as Draft, published content stays gated, and the JSON-LD probe
  still passes (23/23) with the MCP module enabled.

See `docs/AGENT_GUIDE.md` for the end-to-end agent loop and `docs/api/` for the
fetchable, versioned schema.
