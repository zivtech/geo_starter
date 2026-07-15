# Plan 006: Add a minimal GitLab CI pipeline to geo_starter (yaml parse, content-graph lint, composer validate)

> **Executor instructions**: Follow step by step; run every verification and
> confirm expected results. On any STOP condition, stop and report. When done,
> update this plan's row in `plans/README.md` (workspace root).
>
> **Drift check (run first)**:
> `cd /Users/AlexUA_1/claude/ai-initiative-modules/geo_starter && git diff --stat c1724c4..HEAD -- tools/content-graph-lint.py .gitlab-ci.yml`
> If `.gitlab-ci.yml` already exists, STOP (someone added CI; reconcile
> instead of overwriting). This plan assumes plan 005's lint upgrade has
> landed — check `rg -n "PHANTOM DEPENDS" tools/content-graph-lint.py` → must
> match; if not, execute plan 005 first.

## Status

- **Priority**: P2
- **Effort**: M
- **Risk**: LOW
- **Depends on**: plans/005 (the lint it wires in must include the reverse check)
- **Category**: dx
- **Planned at**: commit `c1724c4`, 2026-06-11

## Why this matters

`geo_starter` is a stable (1.0.1) drupal.org release with zero CI: YAML
syntax, the content dependency graph, and composer.json validity are checked
only when a human remembers to run scripts (`docs/VALIDATION.md` and
`docs/RELEASE_CHECKLIST.md` are entirely manual checklists). Plan 005's
finding — 28 phantom depends entries shipped in a *stable* release — is the
concrete proof of cost. Three fast, database-free jobs close most of the gap.

## Current state

Repo: `/Users/AlexUA_1/claude/ai-initiative-modules/geo_starter` (branch
`main`, canonical remote git.drupalcode.org/project/geo_starter, mirror on
GitHub). Type: Drupal **Site recipe** (`recipe.yml` `type: Site`) — NOT a
module; the drupal.org GitLab module template's PHPUnit/PHPStan matrix does
not apply.

- No `.gitlab-ci.yml`, no `.github/workflows/` (verified at `c1724c4`).
- `tools/content-graph-lint.py` — pure-python (pyyaml) lint, exit 0/1.
- `composer.json` — exists; `composer validate --strict` passes today.
- Exemplar for the drupal.org template include (do NOT use it here — see
  Step 1 note): `../geo_starter_jsonld/.gitlab-ci.yml` in the sibling module
  repo uses `include: project: $_GITLAB_TEMPLATES_REPO …`.

## Commands you will need

| Purpose | Command (repo root) | Expected |
|---|---|---|
| Lint | `python3 tools/content-graph-lint.py` | `content-graph-lint: OK …` exit 0 |
| YAML parse-all | `python3 -c "import glob,yaml; [yaml.safe_load(open(p)) for p in glob.glob('config/**/*.yml',recursive=True)+glob.glob('content/**/*.yml',recursive=True)+['recipe.yml']]; print('YAML OK')"` | `YAML OK` |
| Composer | `composer validate --strict` | exit 0 |
| CI file syntax | `python3 -c "import yaml; yaml.safe_load(open('.gitlab-ci.yml')); print('CI YAML OK')"` | `CI YAML OK` |

## Scope

**In scope**:
- `.gitlab-ci.yml` (create)
- `docs/RELEASE_CHECKLIST.md` (one added line pointing at CI)

**Out of scope**:
- A full install-test job (needs a Drupal site + DB — deliberately deferred;
  record as a maintenance note, do not attempt).
- The drupal.org GitLab template include (`$_GITLAB_TEMPLATES_REPO`) — its
  behavior for Site recipes cannot be verified locally; proposing it is the
  maintainer's follow-up, not this plan's.
- `.github/workflows/` — the GitHub remote is a mirror; CI belongs on the
  canonical drupalcode remote.

## Git workflow

- Branch from `main`: `advisor/006-recipe-ci`.
- Commit style: `chore(ci): GitLab pipeline — yaml parse, content-graph lint, composer validate`.
- Do NOT push (maintainer pushes; first pipeline run happens then).

## Steps

### Step 1: Write `.gitlab-ci.yml`

Self-contained jobs only — every command must be one you can also run
locally (that's the verification story; the d.o. template include is
explicitly avoided because a recipe-type project's template behavior can't be
verified without pushing):

```yaml
# Minimal validation pipeline for the geo_starter Site recipe.
# Database-free by design: recipes are config+content artifacts, so the
# high-value gates are structural (YAML syntax, content dependency graph,
# composer manifest). A full install test is a deliberate non-goal here —
# see docs/RELEASE_CHECKLIST.md for the manual acceptance run.

stages:
  - validate

yaml-and-graph:
  stage: validate
  image: python:3.12-slim
  before_script:
    - pip install --quiet pyyaml
  script:
    - python3 -c "import glob,yaml; [yaml.safe_load(open(p)) for p in glob.glob('config/**/*.yml',recursive=True)+glob.glob('content/**/*.yml',recursive=True)+['recipe.yml']]; print('YAML OK')"
    - python3 tools/content-graph-lint.py

composer-validate:
  stage: validate
  image: composer:2
  script:
    - composer validate --strict
```

**Verify**: CI file syntax command → `CI YAML OK`.

### Step 2: Run every job's script locally

Run the three script lines exactly as written in the CI file, from the repo
root.

**Verify**: YAML parse-all → `YAML OK`; lint → exit 0 (with plan 005 landed);
`composer validate --strict` → exit 0.

### Step 3: Point the release checklist at CI

In `docs/RELEASE_CHECKLIST.md`, in the pre-release gate section (locate the
checklist of validation steps; it includes the manual lint/yaml items), add
one line:

```
- [ ] GitLab CI green on the release commit (yaml-and-graph + composer-validate jobs)
```

Do not delete the manual items — CI supplements, the manual DDEV acceptance
run remains authoritative for installs.

**Verify**: `rg -n "GitLab CI green" docs/RELEASE_CHECKLIST.md` → 1 match.

## Test plan

The pipeline is its own test; local execution of each job's script (Step 2)
is the machine check. First remote run happens on the maintainer's next push
— note this explicitly in your completion report.

## Done criteria

- [ ] `.gitlab-ci.yml` exists, parses, contains exactly the two jobs above.
- [ ] All three script commands pass locally from a clean checkout state.
- [ ] `docs/RELEASE_CHECKLIST.md` references CI.
- [ ] `git status`: only the two in-scope files changed.
- [ ] `plans/README.md` row updated (note "remote run pending first push").

## STOP conditions

- `.gitlab-ci.yml` already exists (drift).
- `python3 tools/content-graph-lint.py` fails locally — plan 005 isn't done
  or has regressed; do not weaken the job to allow_failure to get green.
- `composer validate --strict` fails — report the manifest error; fixing
  composer.json is out of scope here.

## Maintenance notes

- Known trap for the maintainer: on drupal.org, a pipeline that "passed with
  warnings" means an `allow_failure` job failed. These two jobs deliberately
  have NO allow_failure so green means green.
- Follow-ups deferred: (a) trial the d.o. GitLab template include on a branch
  to pick up cspell for free; (b) an install-test job (DDEV-in-CI or
  core's recipe kernel tests) if/when d.o. CI offers a sane recipe harness.
- When new validation tools are added to `tools/`, add them to the
  `yaml-and-graph` job in the same commit.
