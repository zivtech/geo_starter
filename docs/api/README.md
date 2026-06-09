# GEO Starter — machine-readable API references

Versioned, fetchable references so an AI coding agent (or any external system)
can inspect the GEO Starter content model **without relying on stale Drupal
training data**. This directly addresses recommendation #3 in Dries Buytaert's
*"Do AI coding agents recommend Drupal?"* (2026).

| File | What it is |
| --- | --- |
| `content-model.schema.json` | The canonical, versioned content model: the four node types, every field (machine name, type, cardinality, required), reference targets, the editorial workflow, schema.org mappings, and per-type payload validation schemas (`$defs`). |
| `openapi.yaml` | OpenAPI 3.1 description of the anonymous, read-only JSON:API surface for the four content types. |

## Versioning contract

These references carry an explicit `version` tied to the recipe's 1.x stability
contract (see `../LIMITATIONS.md`): within 1.x the content model is **frozen,
additive-only**. New optional fields/bundles bump the minor version; any
breaking change forces `2.0.0`. An agent can compare the `version` field here
against the installed recipe version to detect staleness.

## Freshness (no drift)

`content-model.schema.json` is **generated from `config/`**, not hand-maintained:

```bash
php tools/generate-content-model-schema.php          # regenerate
php tools/generate-content-model-schema.php --check  # CI drift guard (exit 1 if stale)
```

Regeneration is wired into `../RELEASE_CHECKLIST.md`. The human-readable twin of
`content-model.schema.json` is `../SCHEMA_MAP.md`.

## How agents use these

1. **Fetch** `content-model.schema.json` to learn the exact field machine names
   and required fields before reading or writing.
2. **Read** a running instance via the JSON:API surface in `openapi.yaml`
   (anonymous = published only).
3. **Write** (create/update Drafts) via the MCP server — see `../MCP.md`. The
   MCP `describe_content_model` tool returns this same schema from the live site.

See `../AGENT_GUIDE.md` for the end-to-end install → inspect → modify → verify loop.
