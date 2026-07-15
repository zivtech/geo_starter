# Plan 005: Remove 8 phantom taxonomy-term UUIDs from sample-content depends blocks and make the lint catch the class

> **Executor instructions**: Follow step by step; run every verification and
> confirm expected results. On any STOP condition, stop and report. When done,
> update this plan's row in `plans/README.md` (workspace root,
> `/Users/AlexUA_1/claude/ai-initiative-modules/plans/`).
>
> **Drift check (run first)**:
> `cd /Users/AlexUA_1/claude/ai-initiative-modules/geo_starter && git diff --stat c1724c4..HEAD -- content/ tools/content-graph-lint.py`
> Any change since `c1724c4` → re-run the Step 0 census before editing;
> if the phantom set differs from the 8 below, STOP and report the new set.

## Status

- **Priority**: P1
- **Effort**: S
- **Risk**: LOW
- **Depends on**: none. Plan 006 (CI) should land AFTER this one so the
  upgraded lint is what CI runs from day one.
- **Category**: bug
- **Planned at**: commit `c1724c4`, 2026-06-11

## Why this matters

`geo_starter` is a released-1.0.1 Drupal CMS site-template recipe whose sample
content ships as hand-authored YAML with `_meta.depends` maps. All 15 sample
node files declare dependencies on **8 taxonomy-term UUIDs that have no
shipped content file** — leftovers from an earlier generator run (the removed
`service_area` vocabulary era). Installs don't crash (the importer treats
unresolved depends keys as external), but the shipped dependency graph is
false: any tool or agent that reads `depends:` as authoritative (the repo's
own AGENTS.md tells agents to do exactly that) hits unresolvable UUIDs. The
repo's lint (`tools/content-graph-lint.py`) only checks the forward direction
(field refs ⊆ depends), so this whole defect class is invisible to the
existing release gate. Fix the data AND the gate.

## Current state

Repo: `/Users/AlexUA_1/claude/ai-initiative-modules/geo_starter` (branch `main`).

**Shipped taxonomy terms** — exactly 9 files in `content/taxonomy_term/`
(UUIDs `051a0930…`, `1f67ce47…`, `2dba0288…`, `3dcd92ee…`, `4f04ae7a…`,
`63c603f6…`, `7763e035…`, `782e744d…`, `fcc1dc48…`).

**The 8 phantom UUIDs** (verified at `c1724c4`: each appears ONLY inside
`_meta.depends` maps of `content/node/*.yml`, never in field values, never in
docs/config):

```
15354b34-fe0b-4470-8f1f-8b5f1dddfa0f
546e56f1-4b32-4304-9019-aff6daad5c28
5ed66004-3b85-461b-bbdc-0637a10c9c8c
8463ae30-23c1-41d3-a031-3a57fa7cb537
a4a3e9ce-b781-4ee1-92a9-4ccba3217bc8
a9e3da47-2d20-45c4-b312-f600adbabbb1
aff6ee5a-d419-4940-92b8-3d9c8810f229
c9395a5d-3462-4db9-bfb4-70042f1b4a36
```

They occur across the 15 node files (`content/node/41…`, `42…`, `43…` series),
28 depends entries total, each shaped like:

```yaml
_meta:
  depends:
    15354b34-fe0b-4470-8f1f-8b5f1dddfa0f: taxonomy_term
```

**The lint blind spot** — `tools/content-graph-lint.py:57–58` records depends
keys into the graph, and the cycle-walk at lines 79–80 skips unknown UUIDs:

```python
depends = (data['_meta'].get('depends') or {})
graph[uuid] = set(depends)
...
            if child not in graph:
                continue  # external dep (e.g., user 1); importer handles it
```

Nothing checks the reverse direction (every depends key resolves to a shipped
file, or is a legitimately-external type like `user`).

**Hard boundary from project history**: sample content is edited ONLY in
`content/`. There is a dev utility script in the WORKSPACE (outside this repo,
`../tools/create-alpha-sample-content.php`) that delete-and-recreates nodes —
do NOT run it, do NOT edit it, do NOT treat it as a content-loading path.

## Commands you will need

| Purpose | Command (from the geo_starter repo root) | Expected on success |
|---|---|---|
| Graph lint | `python3 tools/content-graph-lint.py` | `content-graph-lint: OK — 49 entities, …` exit 0 |
| YAML parse-all | `python3 -c "import glob,yaml; [yaml.safe_load(open(p)) for p in glob.glob('config/**/*.yml',recursive=True)+glob.glob('content/**/*.yml',recursive=True)+['recipe.yml']]; print('YAML OK')"` | `YAML OK` |
| Composer manifest | `composer validate --strict` | exit 0 |

(`python3` needs `pyyaml`; it is already used by the existing lint, so the
environment that runs the lint has it.)

## Scope

**In scope**:
- `tools/content-graph-lint.py` (add the reverse check)
- The 15 `content/node/*.yml` files (remove ONLY the 28 phantom depends lines)

**Out of scope**:
- `content/taxonomy_term/` — do NOT create term files for the phantoms; the
  correct fix is removal (the UUIDs are not referenced by any field value).
- Everything under `config/`, `recipe.yml`, all docs.
- `../tools/create-alpha-sample-content.php` (workspace dev script — destructive; never run/edit).

## Git workflow

- Branch from `main`: `advisor/005-phantom-depends`.
- Commit style observed in repo: `fix(content): strip phantom taxonomy_term depends; lint now rejects unresolvable depends keys`.
- Do NOT push (dual-remote repo; maintainer pushes).

## Steps

### Step 0: Census (establish ground truth before editing)

```bash
ls content/taxonomy_term/ | sed 's/\.yml$//' | sort > /tmp/shipped.txt
rg -o "'?([0-9a-f-]{36})'?: taxonomy_term" content/node/ -r '$1' --no-filename | sort -u > /tmp/referenced.txt
comm -13 /tmp/shipped.txt /tmp/referenced.txt
```

**Verify**: the `comm` output is EXACTLY the 8 UUIDs listed above. Different
set → STOP.

Also confirm no usage outside depends:
`for u in $(comm -13 /tmp/shipped.txt /tmp/referenced.txt); do rg "$u" . -n | rg -v ': taxonomy_term' ; done`
**Verify**: empty output.

### Step 1: Add the reverse (phantom-depends) check to the lint

In `tools/content-graph-lint.py`, the per-file loop currently keeps only
depends KEYS. Preserve the type map and add a check after the loop (before
cycle detection is fine). Concrete change:

```python
# In the file loop, alongside `graph[uuid] = set(depends)`:
raw_depends[uuid] = dict(depends)          # add; init raw_depends = {} at top

# After the file loop:
EXTERNAL_TYPES = {'user'}
for owner, deps in raw_depends.items():
    for dep_uuid, dep_type in deps.items():
        if dep_type in EXTERNAL_TYPES:
            continue
        if dep_uuid not in graph:
            problems.append(
                f"PHANTOM DEPENDS: {labels[owner]} declares depends on "
                f"{dep_uuid} ({dep_type}) which has no shipped content file")
```

Update the module docstring's invariant list (add "3. Resolvability — every
depends key must be a shipped entity or an allowlisted external type").

**Verify**: `python3 tools/content-graph-lint.py` now FAILS, printing exactly
28 `PHANTOM DEPENDS` lines, all of type `taxonomy_term`, all from the 8 UUIDs.
Any phantom of a DIFFERENT type or UUID → STOP (don't strip what you haven't
diagnosed).

### Step 2: Remove the 28 phantom depends lines

For each of the 8 UUIDs, locate its lines:
`rg -n "<uuid>: taxonomy_term" content/node/`
and delete that single mapping line from each file's `_meta.depends` map.
Do not touch neighboring depends entries; do not reformat the files.

**Verify**: `python3 tools/content-graph-lint.py` →
`content-graph-lint: OK — 49 entities, 117 depends edges, no cycles, all field entity-refs declared.`
The entity count must remain 49; the edge count must equal (previous edge
count − lines you removed) — at `c1724c4` that is 145 − 28 = 117. A different
arithmetic → STOP.

### Step 3: Full offline gates

**Verify**: YAML parse-all → `YAML OK`; `composer validate --strict` → exit 0;
`git diff --stat` touches exactly 16 files (15 node YAML + the lint).

### Step 4 (optional, only if DDEV is available and you were told to run it):
Fresh-install acceptance per `docs/RELEASE_CHECKLIST.md` / `docs/VALIDATION.md`.
Not required for done: the change removes metadata the importer ignores.

## Test plan

The upgraded lint IS the test: Step 1's verify proves it detects the defect
class (red), Step 2's verify proves the data is clean (green). No PHPUnit —
this repo has no PHP runtime code.

## Done criteria

- [ ] Step 0 census matched the 8 UUIDs.
- [ ] Lint contains the PHANTOM DEPENDS check and exits 0 on the cleaned tree
      reporting 49 entities / 117 edges / no cycles.
- [ ] `rg "15354b34|546e56f1|5ed66004|8463ae30|a4a3e9ce|a9e3da47|aff6ee5a|c9395a5d" .` → no matches anywhere in the repo.
- [ ] YAML parse-all OK; `composer validate --strict` exit 0.
- [ ] Only the 16 in-scope files changed (`git status`).
- [ ] `plans/README.md` row updated.

## STOP conditions

- Census mismatch (Step 0) or arithmetic mismatch (Step 2).
- The new lint check flags phantoms of any type other than `taxonomy_term`
  (e.g. `file`, `paragraph`) — that is a different, undiagnosed defect.
- Any phantom UUID appears OUTSIDE a depends map.
- You feel the need to run or modify `create-alpha-sample-content.php` — never.

## Maintenance notes

- Plan 006 wires this lint into CI; once both land, this class of defect is
  release-blocking automatically.
- The allowlist `EXTERNAL_TYPES = {'user'}` is intentionally tight: if future
  sample content legitimately depends on other never-shipped types, widen it
  deliberately (one-line change) rather than weakening the check.
- Reviewer: spot-check 2–3 node files to confirm only depends lines were
  removed (no field values touched).
