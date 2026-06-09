# Agent Guide

A task-first guide for AI coding agents working with a GEO Starter site. It
walks the loop agents reward — **install → inspect → modify → verify** — with
literal commands and the responses to expect. (Background: Dries Buytaert,
*"Do AI coding agents recommend Drupal?"*, 2026.)

Conceptual background lives elsewhere (`README.md`, `docs/AUTHORING_MODEL.md`,
`docs/SCHEMA_MAP.md`). This file is deliberately example-led.

---

## 1. Install — one command

With [DDEV](https://ddev.com) (recommended), from a clone of this recipe repo:

```bash
ddev start && ddev geo-install
```

Without DDEV:

```bash
./scripts/quickstart.sh
```

Either way you get a running Drupal CMS site with the GEO Starter content model,
sample content (4 Services, 8 Answers, 3 Articles, 6 Evidence Sources), JSON-LD,
and a generated sitemap. Success ends on a single machine-parseable line:

```
GEO_STARTER_READY url=https://geo-starter.ddev.site/user/reset/...
```

Parse `url=` for the one-time admin login. (Timing target: ~one minute on a warm
Composer cache; first run is slower. Verify on your stack — see
`docs/DEMO_RUNBOOK.md`.)

---

## 2. Inspect — learn the model, don't guess it

Drupal training data skews old. **Do not assume field names** — fetch them.

**Fetch the versioned schema** (works without a running site; ships in-repo):

```bash
cat docs/api/content-model.schema.json | jq '.contentTypes | keys'
# → ["answer","article","evidence_source","service"]

jq '.contentTypes.service.fields[] | {machineName, drupalType, required}' \
  docs/api/content-model.schema.json
```

**Query a running site over JSON:API** (anonymous = published only):

```bash
# What collections exist?
curl -s https://geo-starter.ddev.site/jsonapi | jq '.links | keys'

# List published Services, newest review first, with their topic + sources:
curl -s 'https://geo-starter.ddev.site/jsonapi/node/service?sort=-field_reviewed_date&include=field_topic,field_evidence_sources' \
  -H 'Accept: application/vnd.api+json' | jq '.data[].attributes.title'
```

Expected: an array of published Service titles. Draft content returns 403 /
not-found for anonymous requests (proven in `docs/VALIDATION.md`).

**Inspect via MCP** (typed, live — see `docs/MCP.md`):

```text
geo.describe_content_model        → live field definitions for all four types
geo.list_nodes  { "type": "service" }
geo.get_node    { "uuid": "41000000-0000-4000-8000-000000000001" }
```

---

## 3. Modify — author content (as Draft)

Writes go through the MCP write tools and **always land in Draft** — an agent
cannot publish (see `SECURITY.md`). Validate first, then create:

```text
geo.validate_node {
  "type": "answer",
  "values": {
    "title": "How do I apply for emergency assistance?",
    "field_direct_answer": { "value": "Apply online or call the office.", "format": "content_format" },
    "field_reviewed_date": "2026-06-09",
    "field_topic": ["<topic-term-uuid>"]
  }
}
# → [] (empty = valid). Missing field_reviewed_date → ["Missing required field ..."]

geo.create_node { "type": "answer", "values": { ... as above ... } }
# → { "uuid": "...", "nid": 42, "moderation_state": "draft" }
```

Required fields per type are in `docs/api/content-model.schema.json`
(`$defs.<type>.required`). Reference fields (`field_topic`, `field_audience`,
`field_evidence_sources`, `field_related_*`, `field_sections`) take arrays of
**UUIDs**.

To move a Draft toward publication, route it to review (an editor publishes):
update keeps it in Draft; the editorial UI handles the `publish` transition.

---

## 4. Verify — confirm the change

```bash
# The new node is a Draft, so it is NOT in anonymous JSON:API yet:
curl -s -o /dev/null -w '%{http_code}\n' \
  https://geo-starter.ddev.site/jsonapi/node/answer/<new-uuid>      # → 404 (correct)

# After an editor publishes, it appears and emits JSON-LD:
curl -s https://geo-starter.ddev.site/<published-path> | grep -c 'application/ld+json'  # → ≥1

# Structural regression: the JSON-LD acceptance probe expects 23/23 on a fresh
# install (see docs/VALIDATION.md); re-run it after enabling the MCP module.
```

Verification anchors that already exist: `docs/VALIDATION.md` (JSON-LD 23/23,
JSON:API access proofs), the schema.org validator pass, and the demo
verification matrix in `docs/DEMO_RUNBOOK.md`.

---

## What this site will NOT do (don't waste a session)

- **Agents cannot publish.** Writes are Draft-only by design.
- **No turnkey importer** — migration mapping is documented (`docs/MIGRATION_MAP.md`)
  but not automated.
- **No AI provider is configured**; provider choice is left open.
- **No RDF/RDFa/hypermedia API.** The machine surfaces are JSON:API, JSON-LD,
  the sitemap, and the MCP tools.
- **Fresh install only** within 1.x; recipes are apply-once (no update hooks).

## Reference map

| Need | File |
| --- | --- |
| Field machine names, types, required | `docs/api/content-model.schema.json` |
| JSON:API read surface | `docs/api/openapi.yaml` |
| MCP tools, scopes, write safety | `docs/MCP.md` |
| Authoring lanes (Canvas vs Paragraphs) | `docs/AUTHORING_MODEL.md` |
| Schema.org mapping | `docs/SCHEMA_MAP.md` |
| Live verification checklist | `docs/DEMO_RUNBOOK.md` |
