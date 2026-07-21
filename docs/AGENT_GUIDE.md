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

Parse the final `drush uli` line for the one-time admin login link. On a new
project, the installer also copies `AGENTS.md` from the included handoff
template; it never replaces an existing `AGENTS.md`.

`ddev geo-install` is deliberately unavailable and fails closed. Do not use it
as a fallback or an agent shortcut.

Timing/behavior is verified on a live stack — see `docs/VALIDATION.md`.

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

Expected: an array of published Service titles. Anonymous callers must not get
draft content. The recorded fresh-install proof returned `403`; a site may
return `403` or `404` depending on its access/routing policy, so never make an
agent workflow depend on one status code.

The machine-readable model in `docs/api/` is the canonical inspect surface;
`docs/api/openapi.yaml` describes the JSON:API read endpoints.

---

## 3. Modify — make a reviewable draft-article artifact, then use the editorial workflow

Content is created and published through Drupal's editorial workflow
(`geo_starter_editorial`: Draft → Needs review → Published → Archived). The
recipe ships no network/API/MCP write endpoint. It does ship one narrow local
CLI lane: a schema-validated artifact can create a new unpublished Article in
Draft only. It cannot publish, update, archive, or delete content.

Required fields per type are in `docs/api/content-model.schema.json`
(`$defs.<type>.required`); reference fields (`field_topic`, `field_audience`,
`field_evidence_sources`, `field_related_*`, `field_sections`) take arrays of
target UUIDs. That describes the general content model: the Article draft
contract below intentionally excludes `field_sections`. Validate the planned
shape against the applicable contract before writing.

For a new Article, start from the strict JSON contract and validate it before
touching Drupal:

```bash
mkdir -p artifacts/drafts
cp recipes/geo_starter/docs/api/examples/draft-article.valid.json \
  artifacts/drafts/<article-slug>.json
${EDITOR:-vi} artifacts/drafts/<article-slug>.json
php recipes/geo_starter/tools/validate-draft-article.php \
  artifacts/drafts/<article-slug>.json
```

Do not import the fixture unchanged. Replace its demo title, dates, prose, and
references with reviewed site data, and replace `entityUuid` with a new
lowercase RFC 4122 UUID. Resolve reference UUIDs from the current site; do not
invent them from the example.

Use the exact keys and UUID-only references in
`docs/api/DRAFT_ARTICLE_CONTRACT.md`. The required `moderationState` is
`draft`; the validator rejects unknown keys and any publish/update/delete
instruction. Paragraph sections are excluded from this contract and need their
own reviewed workflow. Review the result with no mutation first, using the same
explicit editor attribution and access checks as apply:

```bash
drush php:script recipes/geo_starter/tools/import-draft-article.php -- \
  --artifact="$PWD/artifacts/drafts/<article-slug>.json" --actor-uid=<editor-uid>
drush php:script recipes/geo_starter/tools/import-draft-article.php -- \
  --artifact="$PWD/artifacts/drafts/<article-slug>.json" \
  --actor-uid=<editor-uid> --apply
```

The apply command refuses an existing UUID and creates exactly one unpublished
Article revision. It returns the node ID, UUID, and SHA-256 of the exact bytes
it parsed. Keep the imported artifact immutable so that fingerprint remains
meaningful; record the identifiers and editorial decision in a separate review
ticket or log. The actor UID is provenance, not authentication; running the
script already requires trusted Drush/server access. A human editor still
transitions Draft to publication.

> **Want a programmatic, agent-driven write/introspection surface (MCP)?** It's
> an **optional, experimental opt-in** — not shipped or required by the recipe.
> See `docs/OPTIONAL_MCP.md` for the manual setup and its boundaries.

---

## 4. Verify — confirm the change

```bash
# A Draft is NOT in anonymous JSON:API yet:
curl -s -o /dev/null -w '%{http_code}\n' \
  https://geo-starter.ddev.site/jsonapi/node/article/<uuid>     # → 403 or 404, never draft data

# Once an editor publishes, it appears and emits JSON-LD:
curl -s https://geo-starter.ddev.site/<published-path> | grep -c 'application/ld+json'  # → ≥1

# Structural anchor: the JSON-LD acceptance probe expects 23/23 on a fresh
# install (see docs/VALIDATION.md).
```

Verification anchors that already exist: `docs/VALIDATION.md` (JSON-LD 23/23
and JSON:API access proofs) and the schema.org validator pass. The Rich
Results Test is an implementation check, not evidence of Google display,
ranking, or citation.

---

## What this site will NOT do (don't waste a session)

- **No network/API/MCP write endpoint.** The local draft-only Article CLI is
  the sole shipped mutation lane; it cannot publish, update, archive, or
  delete. A programmatic MCP surface remains outside the recipe
  (`docs/OPTIONAL_MCP.md`).
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
| Draft-only Article artifact and CLI | `docs/api/DRAFT_ARTICLE_CONTRACT.md` |
| Optional (experimental) MCP add-on | `docs/OPTIONAL_MCP.md` |
| Authoring lanes (Canvas vs Paragraphs) | `docs/AUTHORING_MODEL.md` |
| Schema.org mapping | `docs/SCHEMA_MAP.md` |
| Installed-project handoff template | `docs/INSTALLED_PROJECT_AGENT_HANDOFF.md` |
