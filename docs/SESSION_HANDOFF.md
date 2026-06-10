# Session Handoff — Launch polish complete; Drupal.org posting queue (2026-06-09)

Internal working note. Not part of the recipe's public documentation set
(export-ignored). Supersedes the 2026-05-30 vertical-slice handoff — that
work shipped long ago (see git history).

**Audience:** the local agent (with browser + drupal.org credentials) that
will post to drupal.org. Everything below is queued, paste-ready work; the
posting itself needs d.o access this remote session does not have (d.o
returns 503/Fastly to automated fetch, and release-node creation hits a
CAPTCHA — expect to need the human at the browser for those steps).

## Repo state you are starting from

Branch `claude/stoic-heisenberg-d4vby4`, four commits ahead of `main`
(`a857008` docs truth pass → `fdfae25` audit + acceptance-plan sync →
`be974fe` quickstart/agent-ref/Issue-4 draft → this handoff). `main` is at
`3016600` (the pre-polish 1.0.0 docs flip). All verification is green on the
branch: content-graph-lint (49 entities, no cycles), all YAML parses,
`composer validate --strict`, `git diff --check`. `config/` and `content/`
were deliberately NOT touched (they are what the released-artifact proof
validated).

**Step 0 — human decision:** review + merge this branch to `main` before any
tagging. Do not tag from the feature branch.

## Task A — Publish recipe `1.0.0` (RELEASE_CHECKLIST Phase 7)

1. On merged `main`: create the **annotated** tag `1.0.0`; push to **both**
   remotes (drupalcode is canonical for d.o packaging; origin/GitHub second).
2. Create the d.o release node from the tag.
   - Paste source: `docs/DRUPAL_ORG_RELEASE_NOTES_1.0.0.md`. **Note:** the
     Install section was corrected this session (the old draft's
     `composer require drupal/geo_starter` instruction never worked — site
     templates are not served by the Composer facade). If any earlier copy
     of the notes was staged anywhere on d.o, replace it wholesale.
   - Set the **supported + recommended** release/branch flags (a stable left
     unflagged is invisible as "recommended" — same as module Phase 4).
3. Verify the release node via API: `field_release_project=3592789`,
   `status:1`. (Nid discrepancy resolved 2026-06-09: the live api-d7
   project node for `geo_starter` is **3592789** — the readiness plan was
   correct; `RELEASE_CHECKLIST.md` carried a `3552789` transcription typo,
   now fixed. Calibration: `geo_starter_jsonld` → 3592912, matching its
   field-validated value. Note the house caveat: api-d7 indexing can lag
   minutes behind a fresh release node — the release page returning 200 and
   the facade carrying the version are the authoritative signals.)
4. Replace the live project page body with `docs/PROJECT_PAGE_DRAFT.md`
   (synced for `1.0.0`, 2026-06-09; summary is 195 chars, under the 200
   bar). Do **not** add a demo "Try it" link unless
   `https://geo-demo.zivtech.com` is verified live AND its lifetime is
   confirmed with Alex — the runbook's teardown rule requires removing the
   link before teardown (`docs/DEMO_RUNBOOK.md`).
5. Record completion: check off the Phase 7 boxes in
   `docs/RELEASE_CHECKLIST.md`, commit, push.

## Task B — File the upstream issues (`docs/plans/2026-06-07-upstream-issue-submissions.md`)

All four are drafted paste-ready in that doc. d.o issues are permanent —
confirm every Version/Component against the live dropdowns before
submitting. Order:

1. **Issue 1 (Drupal core, MR-ready):** file → issue fork → apply
   `docs/plans/patches/core-defaultcontent-cycle-detection.patch` → open MR.
2. **Issue 2 (ERR):** do NOT file new — post the drafted comment on
   **#2706883**, cross-linking #2675076.
3. **Issue 3a (canvas bug)** and **3b (canvas docs question):** two separate
   postings per the split rationale in the doc.
4. **Issue 4 (drupalorg queue — Composer facade ignores site templates):**
   **run the dedup gate first** — it could not be run from the remote
   sandbox. Searches and the ADR check are spelled out in the draft. If the
   ADR documents installer-only distribution as intended, file only the 4b
   docs ask.

After each posting: write the issue number/URL back into the submissions
doc, commit, push (house discipline: the doc is the record).

## Task C — Optional proofs (valuable, not blocking)

- Run `tools/quickstart.sh` end-to-end on the local machine (real network)
  before pointing public copy at it; record the result in
  `docs/VALIDATION.md`. It wraps the verified sequence but has never had a
  live run itself; the SQLite default path in particular is unproven.
- Check `geo-demo.zivtech.com` liveness/lifetime (feeds the Task A demo-link
  decision).
- Queued post-1.0 follow-ups (not this session's scope): Drupal Security
  Team opt-in application; `llms.txt` agent manifest as a
  `geo_starter_jsonld` feature.

## Hard boundaries (unchanged — AGENTS.md)

No guaranteed-citation/rich-result/Marketplace/security-coverage claims in
anything you post; no edits to `config/` or `content/` without re-running
the released-artifact install proof; recipe stays `type: Site` /
`drupal-recipe`; the content model is frozen additive-only within 1.x.
