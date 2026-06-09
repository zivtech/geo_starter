# GEO Starter MCP tools

Typed [Model Context Protocol](https://modelcontextprotocol.io) tools for the
GEO Starter content model. Companion to the `drupal/geo_starter` recipe; built
on the [`mcp_server`](https://www.drupal.org/project/mcp_server) contrib module.

This module is the concrete answer to recommendation #2 of Dries Buytaert's
*"Do AI coding agents recommend Drupal?"* (2026): give agents a typed way to
**inspect and modify a running instance** so the install → inspect → modify →
verify loop is tight.

## Packaging

The `geo_starter` recipe is a `drupal-recipe` (config only) and cannot bundle a
module — exactly as `geo_starter_jsonld` ships as its own package. The source
lives in the recipe repo under `geo_starter_mcp/` for review and is
export-ignored from the recipe's Composer artifact. To use it, publish it as
`drupal/geo_starter_mcp` (or require it via a path repo), then:

```bash
composer require drupal/geo_starter_mcp
drush en geo_starter_mcp -y
```

## What it exposes

| Tool | Reads/Writes | Backed by |
| --- | --- | --- |
| `geo.describe_content_model` | read | `ContentModel::describe()` — live field definitions for the four node types. |
| `geo.list_nodes` | read | JSON:API / entity query (published for anonymous scope). |
| `geo.get_node` | read | entity load by UUID. |
| `geo.validate_node` | read | `ContentModel::validate()` — required-field + unknown-field checks. |
| `geo.create_node` | write (Draft) | `DraftWriter::create()` — forced `moderation_state: draft`. |
| `geo.update_node` | write (Draft) | `DraftWriter::update()` — new Draft revision. |

The substantive logic lives in transport-independent services
(`src/ContentModel.php`, `src/DraftWriter.php`) so it is kernel-testable without
the MCP transport, and so the write-to-Draft guarantee is enforced in one place.

## Security contract

- All writes land in `draft` (status FALSE). The agent role (`geo_agent`,
  shipped in `config/install/`) is **not** granted the `publish` transition.
- `geo:read` → `access content` (published only for the anonymous/read client).
- `geo:write` → the `geo_agent` role (author + send-to-review, never publish).
- Auth is OAuth 2.1 via `simple_oauth`; do not run the HTTP transport with auth
  disabled on a site that has the write tools enabled.

See the recipe's `docs/MCP.md` and `SECURITY.md` for the full threat model.

## Integration steps (verify against your installed versions)

These two steps depend on the exact APIs of the installed `mcp_server` and
`simple_oauth` releases, so they are documented rather than shipped as
potentially-version-mismatched config:

1. **Register the six tools** at `/admin/config/services/mcp-server/tools`
   (or via `mcp_server` config), pointing read tools at the `ContentModel`
   service and write tools at the `DraftWriter` service. The method signatures
   above are stable; only the Tool API plugin/annotation wrapper varies by
   `mcp_server` version.
2. **Create the `geo:read` and `geo:write` OAuth2 scopes** (simple_oauth 6
   `oauth2_scope` entities), mapping `geo:write` to the `geo_agent` role and
   `geo:read` to `access content`. Issue a client credentials token to the
   agent with only the scopes it needs.

> This module ships verified, framework-stable services + the moderation-gated
> role. The Tool API and OAuth scope wiring are the remaining integration step;
> complete them against your stack and confirm with the live-verification
> checklist in `docs/DEMO_RUNBOOK.md` before relying on agent writes.
