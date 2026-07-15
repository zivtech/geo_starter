# Drupal.org Release Notes — geo_starter 1.1.0

Paste-ready source for the drupal.org release node. All four agent-readiness
gates green as of 2026-07-15 — see `docs/RELEASE_CHECKLIST.md` and
`docs/VALIDATION.md`.

**Short description (form field, plain text):**

Fresh-install fix + agent-readiness release. Re-exports canvas component
version hashes so fresh installs on current Drupal core work again (fresh
1.0.x installs on core 11.4 are broken — install 1.1.0; running sites are
unaffected). Adds a machine-readable content-model schema and JSON:API
OpenAPI reference (docs/api/), an agent guide, and CI-enforced verification
gates. MCP is documented as an optional experimental opt-in, not shipped.

---

## Release notes (body)

Fresh-install fix + agent-readiness release. **Install-affecting change**:
the only `config/` change is a canvas component-version hash re-export (6
files, 7 hash strings — content otherwise identical); `content/`,
`recipe.yml`, and `composer.json` are identical to `1.0.0`/`1.0.1`. Sites
already installed and running need no action, and the recipe still installs
at the default `stable` Composer floor with no new dependencies.

### Fresh-install fix (canvas component versions)

**Fresh installs of 1.0.0/1.0.1 on current Drupal core (11.4) are broken**
— every HTML page throws `OutOfRangeException` from canvas's
`assertVersionExists()`, because the shipped Mercury header/footer page
regions pin component-version hashes exported under the June core era, and
core 11.4 changed the schema data canvas hashes over (upstream: canvas
[#3563959], drupal_cms [#3573892], and the pin-less-recipes proposal canvas
[#3571366]). 1.1.0 re-exports the affected hashes from a live current-floor
install; the full released-artifact proof on the fixed tree (install, 23/23
JSON-LD probe, all pages rendering) is recorded in `docs/VALIDATION.md`.
A known-issue note for the 1.0.x line is posted on the project issue queue.

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
`tools/quickstart.sh` stands up a working site in one command (verified
live). The committed `ddev geo-install` DDEV command is **experimental and
currently known-broken** on current `drupal/cms` — its redesign is tracked
in the project issue queue; use `tools/quickstart.sh`.

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
