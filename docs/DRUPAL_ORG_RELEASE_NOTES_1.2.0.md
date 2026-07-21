# Drupal.org Release Notes — geo_starter 1.2.0

Paste-ready source for the Drupal.org release node. The companion
`geo_starter_jsonld 1.2.0` release is public and the exact recipe candidate
passed the released-pair install proof recorded in
`docs/RELEASE_CHECKLIST.md`. Post this only after the recipe `1.2.0` tag is
public; the install commands at the end intentionally target that future tag.

**Short description (plain text):**

Additive agent-safety and content-contract release. Adds a visible Article
publication date, strict machine-readable authoring contracts, a local
draft-only Article handoff, and an installed-project agent guide. Requires the
companion JSON-LD 1.2 release for stronger rendered-page and access parity.

---

## Release notes

GEO Starter 1.2.0 makes the site template easier for coding agents and local
LLM workflows to inspect and extend without weakening Drupal's editorial trust
boundary. The release remains fresh-install-only and additive within the 1.x
content-model contract.

### Public publication truth

Article now has an optional, visible `field_publication_date`, populated in the
three sample Articles. The companion module uses that governed field for
`Article.datePublished`; it does not substitute Drupal's internal node-created
timestamp when the field is absent or hidden.

### Executable content contracts

- `docs/api/content-model.schema.json` is generated from the shipped Drupal
  configuration and drift-checked in CI. Its governed payload types reject
  unknown properties and invalid UUID, date, and required-value shapes.
- `docs/api/draft-article.schema.json` defines a narrower Article authoring
  artifact, with examples and a separate contract guide.
- `docs/api/openapi.yaml` continues to describe the JSON:API read surface. It
  is not an autonomous publishing API.

### Draft-only Article handoff

The local validator and importer are dry-run by default. With explicit
`--apply`, a trusted Drush/server operator can create one new unpublished Draft
while attributing it to an active Drupal user with Article-create access. The
actor UID is checked for authorization and provenance; it does not authenticate
the caller. The importer cannot update, publish, delete, or write Paragraph
sections, and it exposes no network or MCP endpoint.

### Agent handoff and scaffolding

`tools/quickstart.sh` reads dependency constraints from the selected recipe
tag instead of duplicating them in the script. After installation, it copies a
bounded `AGENTS.md` handoff into a new project only when that project does not
already have one. The handoff points agents to the installed content model,
safe modification paths, and required verification commands.

### Structured-data access parity

The required `drupal/geo_starter_jsonld:^1.2` companion release checks the
active view display, configured formatters, field and entity access, and
canonical-URL access before emitting structured data. Nested Paragraphs follow
their rendered view mode. Hidden, inaccessible, ID-only, unlinked, or
incomplete reference paths fail closed.

The optional `/llms.txt` submodule is an anonymous-public, language-specific,
bounded projection for experimentation. It is not a ranking, indexing, or
citation mechanism.

### Release verification

The coordinated release gate passed in a clean default-stability Drupal CMS
2.1.3 project with Drupal 11.4.4. It resolved the published companion 1.2.0
dist zip through the candidate recipe's `^1.2` constraint, installed the exact
recipe candidate archive, and passed cron, static contract checks, the 23/23
installed-site JSON-LD probe, public route checks, Composer audit, and the
draft-Article dry-run/apply/duplicate/access tests. Exact evidence is recorded
in `docs/VALIDATION.md`.

No AI citation, ranking, rich-result, indexing, or answer-engine placement is
guaranteed.

## Install

```bash
git clone --branch 1.2.0 https://git.drupalcode.org/project/geo_starter.git
./geo_starter/tools/quickstart.sh my-site 1.2.0
```

See `docs/INSTALL.md` for the manual reference path and post-install checks.
