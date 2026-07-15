# Plan 009: Fix stale documentation facts in geo_starter (probe version, historical counts, quickstart tag, config count)

> **Executor instructions**: Follow step by step; run every verification and
> confirm expected results. On any STOP condition, stop and report. When done,
> update this plan's row in `plans/README.md` (workspace root).
>
> **Drift check (run first)**:
> `cd /Users/AlexUA_1/claude/ai-initiative-modules/geo_starter && git diff --stat c1724c4..HEAD -- docs/LIMITATIONS.md docs/VALIDATION.md docs/RELEASE_CHECKLIST.md tools/quickstart.sh AGENTS.md`
> On any drift, locate each target string by content (the greps in each step)
> rather than by line number.

## Status

- **Priority**: P3
- **Effort**: S
- **Risk**: LOW
- **Depends on**: none (touches docs/RELEASE_CHECKLIST.md like plan 006 —
  execute serially with 006, either order)
- **Category**: docs
- **Planned at**: commit `c1724c4`, 2026-06-11

## Why this matters

This repo's docs are explicitly agent-consumed (AGENTS.md instructs agents to
treat them as ground truth). Four facts are stale or fragile: a stable-release
claim backed by an alpha-era probe citation, historical acceptance tables with
superseded taxonomy counts and no local warning, a quickstart script whose
default tag will silently go stale on the next release, and a hardcoded config
file count that drifts on the next config addition. Each is small; together
they are exactly the kind of wrong-fact surface that misleads the next agent
session.

## Current state

Repo: `/Users/AlexUA_1/claude/ai-initiative-modules/geo_starter` (branch `main`).
All excerpts verified at `c1724c4`.

1. `docs/LIMITATIONS.md` (~line 62): "…the full-surface acceptance probe
   (23/23 on a fresh \`1.0.0-alpha4\` install)…" — stale: the probe was re-run
   at 1.0.0 stable (documented in `docs/VALIDATION.md` under "Stable 1.0.0
   Released-Artifact Proof"). NOTE: the OTHER alpha4 mention (~line 74, the
   office_hours upgrade-path example) is legitimate history — leave it.
2. `docs/VALIDATION.md` (~line 133): historical bullet "Import of 4 Services,
   8 Answers, 3 Articles, 6 Evidence Sources, 5 Audience terms, 8 Topic
   terms, and 4 Service area terms" plus two "Service Area terms | 4" table
   rows (~lines 185, 256). The shipping model is 4 topic + 5 audience terms
   and NO service_area vocabulary. A top-of-file banner exists but the stale
   tables appear 100+ lines below it with no local marker.
3. `tools/quickstart.sh` (~line 14): `TAG="${2:-1.0.1}"` — hardcoded default;
   the usage comment block (~lines 10–18) also names 1.0.1.
4. `AGENTS.md` (~line 12): "(176 exported config files; no `uuid:`/`_core:`
   keys — keep it that way)" — the count is accurate today and will silently
   rot.

## Commands you will need

| Purpose | Command (repo root) | Expected |
|---|---|---|
| Current config count | `ls config/ \| wc -l` | a number (176 at `c1724c4`) |
| Lint tripwire | `python3 tools/content-graph-lint.py` | exit 0 |
| Shell syntax | `bash -n tools/quickstart.sh` | exit 0, no output |

## Scope

**In scope**:
- `docs/LIMITATIONS.md`, `docs/VALIDATION.md`, `docs/RELEASE_CHECKLIST.md`,
  `tools/quickstart.sh`, `AGENTS.md`

**Out of scope**:
- `README.md` (its 1.0.1 references are correct for the current release; the
  checklist item added below covers future bumps).
- Deleting or restructuring the historical VALIDATION sections — mark them,
  don't rewrite history.

## Git workflow

- Branch from `main`: `advisor/009-docs-staleness`.
- Commit style: `docs: refresh stale probe/count references; future-proof release-coupled defaults`.
- Do NOT push.

## Steps

### Step 1: LIMITATIONS.md probe reference

Locate `rg -n "23/23 on a fresh" docs/LIMITATIONS.md`. Replace
"(23/23 on a fresh \`1.0.0-alpha4\` install)" with
"(23/23 on the 1.0.0 released-artifact install — see \`docs/VALIDATION.md\`,
'Stable 1.0.0 Released-Artifact Proof')".

**Verify**: `rg -n "alpha4" docs/LIMITATIONS.md` → exactly 1 remaining match
(the office_hours upgrade-path example).

### Step 2: VALIDATION.md historical callouts

Find each pre-correction section containing the stale counts:
`rg -n "Service area terms|Service Area terms" docs/VALIDATION.md`.
Immediately ABOVE each section heading that contains those hits, insert:

```markdown
> **HISTORICAL (pre-taxonomy-correction).** Counts below include the removed
> `service_area` vocabulary and superseded topic terms; they do not describe
> the shipping recipe. Authoritative numbers: "Stable 1.0.0
> Released-Artifact Proof".
```

One callout per section (if two hit-lines share a section, one callout).

**Verify**: `rg -c "HISTORICAL \(pre-taxonomy-correction\)" docs/VALIDATION.md`
→ equals the number of distinct historical sections you found (expect 2–3);
each callout sits within 5 lines above its section's heading.

### Step 3: quickstart.sh tag default

Two edits in `tools/quickstart.sh`:
- Add a marker comment directly above the TAG line:
  `# RELEASE-COUPLED: bump this default when tagging (see docs/RELEASE_CHECKLIST.md).`
- Leave the default value itself at the current release (`1.0.1`) — switching
  to `main` would hand quickstart users unreleased HEAD.

Then in `docs/RELEASE_CHECKLIST.md`, in the pre-release/tagging section, add:

```
- [ ] Bump `TAG` default in `tools/quickstart.sh` (and its usage examples + the README clone example) to the new tag
```

**Verify**: `bash -n tools/quickstart.sh` → clean;
`rg -n "RELEASE-COUPLED" tools/quickstart.sh` → 1;
`rg -n "Bump \`TAG\` default" docs/RELEASE_CHECKLIST.md` → 1.

### Step 4: AGENTS.md config count

Replace the parenthetical "(176 exported config files; no `uuid:`/`_core:`
keys — keep it that way)" with "(exported config files — count with
`ls config/ | wc -l`; none carry `uuid:`/`_core:` keys — keep it that way)".

**Verify**: `rg -n "176" AGENTS.md` → no matches.

### Step 5: Tripwires

**Verify**: `python3 tools/content-graph-lint.py` → exit 0;
`git diff --name-only` → exactly the 5 in-scope files.

## Test plan

Docs/script-comment changes; the per-step greps + `bash -n` are the checks.

## Done criteria

- [ ] All five per-step verifies pass.
- [ ] `git diff --name-only` = the 5 in-scope files.
- [ ] `plans/README.md` row updated.

## STOP conditions

- A target string from "Current state" cannot be found (drift) — report what
  the file says now instead of guessing a different edit.
- You find ADDITIONAL stale version references while in these files — list
  them in your report; do not expand scope silently.

## Maintenance notes

- The real fix for doc-fact rot is plan 006's CI plus checklist discipline;
  this plan clears the existing debt.
- Reviewer: read the two VALIDATION callout placements in rendered markdown —
  blockquote position relative to headings is easy to get visually wrong.
