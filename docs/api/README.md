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

The schema's `version` tracks the **content model**, not the recipe release.
Within the 1.x line the content model is **frozen, additive-only** (see
`../LIMITATIONS.md`), so this stays `1.0.0` across 1.x recipe releases (e.g.
the recipe may be at `1.1.0` while the content-model schema is still `1.0.0`).
A new optional field/bundle is an additive change; any breaking change forces a
`2.0.0` content model. Do not bump this `version` just because the recipe
version changed.

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
3. **Write** through the editorial workflow (Draft → review → publish). A
   programmatic agent write/introspection surface (MCP) is an optional,
   experimental opt-in — see `../OPTIONAL_MCP.md`.

See `../AGENT_GUIDE.md` for the end-to-end install → inspect → modify → verify loop.
