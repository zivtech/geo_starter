# Drupal.org Release Notes — geo_starter 1.1.0

Paste-ready source for the drupal.org release node. **Do not publish until
the two remaining agent-readiness gates are green** (stable-floor
released-artifact proof; `ddev geo-install` / quickstart run) — see
`docs/RELEASE_CHECKLIST.md`, "Agent-readiness gates (1.1.0 subset)".

**Short description (form field, plain text):**

Agent-readiness release — no recipe changes; sites on 1.0.x need no action.
Adds a machine-readable content-model schema and JSON:API OpenAPI reference
(docs/api/), an agent guide, one-command DDEV scaffolding, and CI-enforced
verification gates. MCP is documented as an optional experimental opt-in,
not shipped.

---

## Release notes (body)

Agent-readiness release. **No recipe changes**: `config/`, `content/`,
`recipe.yml`, and `composer.json` are identical to `1.0.0`/`1.0.1` — sites
installed from 1.0.x need no action, and the recipe still installs at the
default `stable` Composer floor with no new dependencies.

### Machine-readable API references (`docs/api/`)

`content-model.schema.json` describes the four node types, their fields,
reference targets, the editorial workflow, and schema.org mappings —
generated from the recipe's `config/` and drift-guarded in CI, so it cannot
silently diverge from what a site actually installs. `openapi.yaml`
describes the JSON:API read surface. This release also corrects the
schema's section-bundle claims: `answer` accepts 2 section bundles and
`article` 6 (an earlier draft over-claimed all 8 on every type).

### Agent documentation and scaffolding

`docs/AGENT_GUIDE.md` walks the install → inspect → modify → verify loop
for coding agents; content authoring routes through the editorial workflow.
`ddev geo-install` (a committed DDEV command) and `tools/quickstart.sh`
stand up a working site in one command.

### MCP is deferred, not shipped

A programmatic agent introspection/write surface depends on
`drupal/mcp_server`, which has no tagged release yet, so it is **not** a
recipe dependency. `docs/OPTIONAL_MCP.md` documents an experimental manual
opt-in; typed GEO content-model tools ship as a separate package once
`mcp_server` publishes a stable release. A mechanical residue gate
(`tools/mcp-residue-check.py`, run in CI) asserts the shipped artifact
never advertises MCP capability it does not contain.

### Verification gates in CI

Every push/PR now runs `composer validate --strict`, a full YAML parse,
the content dependency-graph lint (now also guarding that every placed
Canvas component ships its component config), the MCP-residue gate, and
the content-model schema drift guard.

## Install

```bash
git clone --branch 1.1.0 https://git.drupalcode.org/project/geo_starter.git
./geo_starter/tools/quickstart.sh my-site 1.1.0
```

or the manual path in `docs/INSTALL.md`.
