# Plan 008: Pin JSON:API read-only mode in recipe.yml and document the content_format provenance

> **Executor instructions**: Follow step by step; run every verification and
> confirm expected results. On any STOP condition, stop and report. When done,
> update this plan's row in `plans/README.md` (workspace root).
>
> **Drift check (run first)**:
> `cd /Users/AlexUA_1/claude/ai-initiative-modules/geo_starter && git diff --stat c1724c4..HEAD -- recipe.yml SECURITY.md docs/LIMITATIONS.md`
> If recipe.yml's `config.actions` block changed since `c1724c4`, re-read it
> before inserting.

## Status

- **Priority**: P3
- **Effort**: S
- **Risk**: LOW
- **Depends on**: none
- **Category**: security (defense-in-depth)
- **Planned at**: commit `c1724c4`, 2026-06-11

## Why this matters

The recipe installs `jsonapi` and its sample-content story leans on JSON:API
access behavior (published → 200, draft → 403 is an acceptance gate). Write
protection currently rests on Drupal core's *default* `read_only: true` —
nothing in the recipe asserts it. Recipes compose: another recipe or profile
applied alongside could flip the setting, and this recipe would silently
inherit a writable API. One config action makes the security posture explicit
and order-proof. Similarly, the HTML filter posture of `content_format` (used
by all governed body fields) is inherited from the composed Drupal CMS
sub-recipes with no statement anywhere about that provenance.

## Current state

Repo: `/Users/AlexUA_1/claude/ai-initiative-modules/geo_starter` (branch `main`).

- `recipe.yml` — `install:` includes `jsonapi`; the `config:` section exists
  with `strict: false` and an `actions:` map that already uses
  `simpleConfigUpdate` (exemplar in the live file: the `system.theme` action
  setting `default: mercury`). There is NO `jsonapi.settings` action and no
  `config/jsonapi.settings.yml` file.
- `config/` ships no filter format named `content_format`
  (`rg -l 'filter.format.content_format' config/` → no definition file);
  governed sample content uses `format: content_format` throughout — the
  format is provided by the composed `drupal_cms_*` recipes listed in
  `recipe.yml`'s `recipes:` block.
- `SECURITY.md` exists at the repo root; `docs/LIMITATIONS.md` exists.

## Commands you will need

| Purpose | Command (repo root) | Expected |
|---|---|---|
| recipe.yml parses | `python3 -c "import yaml; yaml.safe_load(open('recipe.yml')); print('OK')"` | `OK` |
| Lint tripwire | `python3 tools/content-graph-lint.py` | exit 0 |
| Composer | `composer validate --strict` | exit 0 |

## Scope

**In scope**:
- `recipe.yml` (one new config action)
- `SECURITY.md` (one short paragraph)
- `docs/LIMITATIONS.md` (one short paragraph)

**Out of scope**:
- Shipping a `config/jsonapi.settings.yml` file (with `strict: false` an
  action is the correct forcing mechanism; a config file would be skipped when
  the object already exists).
- Any change to text formats themselves — provenance is documented, not
  overridden.

## Git workflow

- Branch from `main`: `advisor/008-jsonapi-pin`.
- Commit style: `feat(recipe): pin jsonapi read-only; document content_format provenance`.
- Do NOT push.

## Steps

### Step 1: Add the config action

In `recipe.yml`, inside `config: actions:`, add (alphabetical placement among
existing action keys is fine; match the file's 2-space indentation exactly):

```yaml
    jsonapi.settings:
      simpleConfigUpdate:
        # Core defaults to read-only, but composed recipes/profiles can flip
        # it; this recipe's JSON:API surface is read-only BY CONTRACT (the
        # acceptance gates probe 200-published/403-draft on GET only).
        read_only: true
```

**Verify**: recipe.yml parse command → `OK`; and
`python3 -c "import yaml; a=yaml.safe_load(open('recipe.yml'))['config']['actions']; print(a['jsonapi.settings'])"`
→ `{'simpleConfigUpdate': {'read_only': True}}`.

### Step 2: Document the posture in SECURITY.md

Add a short section (match the file's existing heading style):

> **JSON:API is read-only by contract.** The recipe pins
> `jsonapi.settings:read_only: true` via a config action. If your site needs
> JSON:API writes, flip the setting consciously after install — none of this
> recipe's functionality requires it.

**Verify**: `rg -n "read-only by contract" SECURITY.md` → 1 match.

### Step 3: Document content_format provenance in LIMITATIONS.md

Add one paragraph in the appropriate existing section (the file already
discusses what the recipe does/doesn't own):

> The `content_format` text format used by governed body fields is provided
> by the composed Drupal CMS sub-recipes (see `recipe.yml` `recipes:`), not
> by this recipe. Its allowed-HTML posture is therefore inherited; sites that
> tighten or loosen it do so outside this recipe's contract.

**Verify**: `rg -n "content_format text format" docs/LIMITATIONS.md` → 1 match.

### Step 4 (optional, only if a DDEV harness is available and you were told
to run it): fresh install, then
`ddev drush php:eval "var_export(\Drupal::config('jsonapi.settings')->get('read_only'));"`
→ `true`. Not required for done.

## Test plan

Offline YAML assertions (Step 1 verify) are the machine check; the action's
runtime effect rides on core's `simpleConfigUpdate` plugin, exercised by every
existing action in this recipe.

## Done criteria

- [ ] The action parses and round-trips via the python assertion.
- [ ] SECURITY.md and LIMITATIONS.md paragraphs present (greps).
- [ ] Lint + composer validate still green.
- [ ] `git diff --name-only` = the 3 in-scope files.
- [ ] `plans/README.md` row updated.

## STOP conditions

- `config/jsonapi.settings.yml` exists (drift — someone chose the file
  mechanism; reconcile, don't double-pin).
- recipe.yml's actions block uses a structure that doesn't match the
  `system.theme` exemplar (e.g. the schema changed) — mirror what's live and
  report the difference.

## Maintenance notes

- If a future feature needs JSON:API writes (none planned in 1.x), this
  action is the single line to revisit — and SECURITY.md must change in the
  same commit.
- Reviewer: confirm on the next DDEV acceptance run that install logs show no
  config-action error for `jsonapi.settings` (would appear during
  `drush site:install recipes/geo_starter`).
