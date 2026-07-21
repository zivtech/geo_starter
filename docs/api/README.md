# GEO Starter — machine-readable API references

Versioned, fetchable references so an AI coding agent (or any external system)
can inspect the GEO Starter content model **without relying on stale Drupal
training data**. This directly addresses recommendation #3 in Dries Buytaert's
*"Do AI coding agents recommend Drupal?"* (2026).

| File | What it is |
| --- | --- |
| `content-model.schema.json` | The canonical, versioned content model: the four node types, every field (machine name, type, cardinality, required), reference targets, the editorial workflow, schema.org mappings, and per-type payload validation schemas (`$defs`). |
| `openapi.yaml` | OpenAPI 3.1 description of the anonymous, read-only JSON:API surface for the four content types. |
| `draft-article.schema.json` + `DRAFT_ARTICLE_CONTRACT.md` | A local-only, draft-only Article handoff and its shared PHP validator/importer instructions. It cannot publish, update, or delete content. |

## Versioning contract

The schema's `version` tracks the **content model**, not the recipe release.
Within the 1.x line the model is **additive-only** (see `../LIMITATIONS.md`).
An additive field, bundle, or contract change increments the schema's minor
version (for example `1.0.0` to `1.1.0`); a breaking change requires `2.0.0`.
The recipe and schema versions can differ, but do not bump the schema version
for a recipe release that does not change the content model.

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
   and required fields before reading JSON:API or preparing a local draft
   Article artifact.
2. **Read** a running instance via the JSON:API surface in `openapi.yaml`
   (anonymous = published only).
3. **Prepare and validate** a local draft Article artifact, then have a human
   editor review it in Drupal (Draft → review → publish). Network/API/MCP
   writes are optional, unsupported by this recipe, and never a base dependency;
   see `../OPTIONAL_MCP.md`.

See `../AGENT_GUIDE.md` for the end-to-end install → inspect → modify → verify loop.
