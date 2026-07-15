# Plan 007: Reconcile the "ten-bundle section library" claim with the per-type target_bundles reality (docs path)

> **Executor instructions**: Follow step by step; run every verification and
> confirm expected results. On any STOP condition, stop and report. When done,
> update this plan's row in `plans/README.md` (workspace root).
>
> **Drift check (run first)**:
> `cd /Users/AlexUA_1/claude/ai-initiative-modules/geo_starter && git diff --stat c1724c4..HEAD -- config/field.field.node.service.field_sections.yml config/field.field.node.answer.field_sections.yml config/field.field.node.article.field_sections.yml README.md AGENTS.md docs/AUTHORING_MODEL.md`
> If any target_bundles list changed since `c1724c4`, re-derive the matrix in
> Step 1 from the live files (the matrix below is the `c1724c4` state).

## Status

- **Priority**: P3
- **Effort**: S
- **Risk**: LOW
- **Depends on**: none
- **Category**: docs
- **Planned at**: commit `c1724c4`, 2026-06-11
- **MAINTAINER DECISION ENCODED HERE**: this plan implements the
  **documentation** resolution (describe the divergence as intentional). The
  alternative — widening `answer`/`article` target_bundles to match `service`
  — is config surgery on a stable release that requires a DDEV acceptance
  pass, and is OUT OF SCOPE unless the maintainer explicitly re-scopes this
  plan. If you (executor) were instructed to do the config path instead, STOP
  and ask for a rewritten plan.

## Why this matters

README.md and AGENTS.md state a "ten-bundle section library attached to
Service, Answer, and Article nodes via `field_sections`". The shipped config
attaches **8** section bundles to Service, **6** to Article, and **2** to
Answer. Editors on Answer nodes see two section choices, not ten; agents
reading AGENTS.md plan against a field surface that doesn't exist. Nothing in
LIMITATIONS.md or AUTHORING_MODEL.md documents the divergence. Whether the
narrowing is intentional design (FAQ sections make little sense on an Answer
node, contact panels are service-shaped) — the docs must say what ships.

## Current state

Repo: `/Users/AlexUA_1/claude/ai-initiative-modules/geo_starter` (branch `main`).

Verified matrix at `c1724c4` (from the three field configs'
`settings.handler_settings.target_bundles`):

| Bundle | service | article | answer |
|---|---|---|---|
| geo_starter_section | ✓ | ✓ | ✓ |
| section_faq | ✓ | — | — |
| section_step_list | ✓ | ✓ | ✓ |
| section_card_grid | ✓ | ✓ | — |
| section_contact_panel | ✓ | — | — |
| section_cta | ✓ | ✓ | — |
| section_alert | ✓ | ✓ | — |
| section_media_text | ✓ | ✓ | — |

(The "ten" in the docs counts these 8 attachable bundles plus the two child
item bundles `section_faq_item` and `section_step_list`'s `section_step_item`,
which are never directly attachable.)

The claims to correct:

- `README.md:39`: "A ten-bundle section library attached to Service, Answer,
  and Article nodes via `field_sections`: …" (enumerates all bundles with no
  per-type qualification).
- `README.md:56` (capability table): "…the ten-bundle section library…".
- `AGENTS.md:56`: "Ten-bundle section library on `field_sections`; four
  component-composed Canvas sample pages."

## Commands you will need

| Purpose | Command (repo root) | Expected |
|---|---|---|
| Re-derive matrix | `for b in service answer article; do echo "--- $b"; rg -A12 'target_bundles:' config/field.field.node.$b.field_sections.yml \| rg '^\s+section_\|^\s+geo_starter'; done` | matches the matrix above |
| YAML untouched | `git diff --name-only -- config/` | empty |

## Scope

**In scope**:
- `README.md` (the two lines above)
- `AGENTS.md` (line 56 area)
- `docs/AUTHORING_MODEL.md` (add the matrix + rationale sentence)

**Out of scope**:
- ALL files under `config/` — this is the docs path; zero config changes.
- `docs/LIMITATIONS.md` — the authoring model doc is the right home; don't
  duplicate.

## Git workflow

- Branch from `main`: `advisor/007-section-matrix-docs`.
- Commit style: `docs: per-type section availability matrix replaces blanket ten-bundle claim`.
- Do NOT push.

## Steps

### Step 1: Re-derive and confirm the matrix

Run the matrix command. **Verify**: output matches the table above (else STOP).

### Step 2: Add the matrix to AUTHORING_MODEL.md

In `docs/AUTHORING_MODEL.md`, find the section describing `field_sections` /
the section library (grep `section library` or `field_sections`). Add a
subsection "Per-type section availability" containing the markdown matrix
from Current state plus two sentences of rationale, e.g.:

> Not every section type is attachable to every governed content type:
> Answers are themselves answer-shaped pages (a FAQ inside an Answer would
> nest Q&A), and contact panels carry Service-specific structured hours. The
> matrix below is the shipped contract; widening a row is an additive 1.x
> change, narrowing one is breaking.

**Verify**: `rg -n "Per-type section availability" docs/AUTHORING_MODEL.md` → 1 match.

### Step 3: Qualify the README and AGENTS claims

- `README.md:39`: change "attached to Service, Answer, and Article nodes" to
  "attached to Service, Article, and Answer nodes per the availability matrix
  in `docs/AUTHORING_MODEL.md` (Service gets all eight attachable bundles;
  Article six; Answer two)". Keep the bundle enumeration.
- `README.md:56`: append "(per-type availability documented in
  `docs/AUTHORING_MODEL.md`)" inside the cell.
- `AGENTS.md:56`: change to "Ten-bundle section library on `field_sections`
  (per-type availability matrix: `docs/AUTHORING_MODEL.md`); four
  component-composed Canvas sample pages."

**Verify**: `rg -n "availability matrix|availability documented" README.md AGENTS.md` → 3 matches.

### Step 4: Offline gates

**Verify**: `git diff --name-only` → exactly the three docs files;
`python3 tools/content-graph-lint.py` still exits 0 (nothing should have
touched content, this is a tripwire).

## Test plan

Docs-only; the greps in Steps 2–4 are the checks.

## Done criteria

- [ ] Matrix present in AUTHORING_MODEL.md and matches live config.
- [ ] README/AGENTS no longer claim blanket attachment; 3 grep matches.
- [ ] `git diff --name-only` = the 3 docs files only.
- [ ] `plans/README.md` row updated.

## STOP conditions

- Matrix mismatch in Step 1.
- You're tempted to "just add the missing bundles to answer/article config" —
  that is the explicitly out-of-scope alternative path.
- AUTHORING_MODEL.md has no section-library section to anchor to — report its
  actual structure instead of inventing a new top-level organization.

## Maintenance notes

- If the maintainer later WIDENS target_bundles (additive 1.x change), update
  the matrix in the same commit — it is now the contract of record.
- Reviewer: read the rationale sentences critically; if the real design
  intent differs (e.g. answer narrowing was an oversight, not a choice),
  reopen the config-alignment alternative as a new plan with a DDEV
  acceptance gate.
