# Agent Guide

A task-first guide for AI coding agents working with a GEO Starter site. It
walks the loop agents reward — **install → inspect → modify → verify** — with
literal commands and the responses to expect. (Background: Dries Buytaert,
*"Do AI coding agents recommend Drupal?"*, 2026.)

Conceptual background lives elsewhere (`README.md`, `docs/AUTHORING_MODEL.md`,
`docs/SCHEMA_MAP.md`). This file is deliberately example-led.

---

## 1. Install — one command

The verified one-command path (creates a Drupal CMS project, requires the
dependency set, places the recipe at a release tag, installs, runs cron,
prints a login link):

```bash
tools/quickstart.sh my-site            # recipe at the latest release tag
```

Parse the final `drush uli` line for the one-time admin login link.

> **Experimental — currently broken.** The DDEV path
> (`ddev start && ddev geo-install`, ending in a machine-parseable
> `GEO_STARTER_READY url=…` line) failed its first end-to-end live run
> (2026-07-15, five distinct defects) and is being redesigned — see the
> project issue queue. Use `tools/quickstart.sh` until the redesign lands.

Timing/behavior is verified on a live stack — see `docs/VALIDATION.md` and
`docs/DEMO_RUNBOOK.md`.

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

The machine-readable model in `docs/api/` is the canonical inspect surface;
`docs/api/openapi.yaml` describes the JSON:API read endpoints.

---

## 3. Modify — author content through the editorial workflow

Content is created and published through Drupal's editorial workflow
(`geo_starter_editorial`: Draft → Needs review → Published → Archived). The
recipe ships **no built-in agent-write endpoint** — an authenticated editor (or
an integration you add) creates content via the admin UI or an authenticated
write channel, and the moderation workflow governs publication.

Required fields per type are in `docs/api/content-model.schema.json`
(`$defs.<type>.required`); reference fields (`field_topic`, `field_audience`,
`field_evidence_sources`, `field_related_*`, `field_sections`) take arrays of
target UUIDs. Validate a payload's shape against that schema before writing.

> **Want a programmatic, agent-driven write/introspection surface (MCP)?** It's
> an **optional, experimental opt-in** — not shipped or required by the recipe.
> See `docs/OPTIONAL_MCP.md` for the manual setup and its boundaries.

---

## 4. Verify — confirm the change

```bash
# A Draft is NOT in anonymous JSON:API yet:
curl -s -o /dev/null -w '%{http_code}\n' \
  https://geo-starter.ddev.site/jsonapi/node/answer/<uuid>      # → 404 while Draft

# Once an editor publishes, it appears and emits JSON-LD:
curl -s https://geo-starter.ddev.site/<published-path> | grep -c 'application/ld+json'  # → ≥1

# Structural anchor: the JSON-LD acceptance probe expects 23/23 on a fresh
# install (see docs/VALIDATION.md).
```

Verification anchors that already exist: `docs/VALIDATION.md` (JSON-LD 23/23,
JSON:API access proofs), the schema.org validator pass, and the verification
matrix in `docs/DEMO_RUNBOOK.md`.

---

## What this site will NOT do (don't waste a session)

- **No built-in agent-write endpoint.** Content goes through the editorial
  workflow; a programmatic MCP write surface is an optional opt-in
  (`docs/OPTIONAL_MCP.md`), not part of the recipe.
- **No turnkey importer** — migration mapping is documented (`docs/MIGRATION_MAP.md`)
  but not automated.
- **No AI provider is configured**; provider choice is left open.
- **No RDF/RDFa/hypermedia API.** The machine surfaces are JSON:API, JSON-LD,
  the sitemap, and the in-repo machine-readable schema.
- **Fresh install only** within 1.x; recipes are apply-once (no update hooks).

## Reference map

| Need | File |
| --- | --- |
| Field machine names, types, required | `docs/api/content-model.schema.json` |
| JSON:API read surface | `docs/api/openapi.yaml` |
| Optional (experimental) MCP add-on | `docs/OPTIONAL_MCP.md` |
| Authoring lanes (Canvas vs Paragraphs) | `docs/AUTHORING_MODEL.md` |
| Schema.org mapping | `docs/SCHEMA_MAP.md` |
| Live verification checklist | `docs/DEMO_RUNBOOK.md` |
