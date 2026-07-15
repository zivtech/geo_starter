# Session Handoff — post-1.1.0 state and open strands (2026-07-15)

Internal working note (export-ignored). **Supersedes the 2026-07-13
release-queue handoff — that queue is DONE:** both agent-readiness gates were
run (and caught real defects), `1.1.0` is published (release node **3611198**,
api-d7 `status:1`, tarball packaged, release types Bug fixes + New features),
the project-page paste was verified live, and the missing `1.0.0` tag is on
GitHub. Full evidence: `docs/VALIDATION.md`, "Stable 1.1.0 Released-Artifact
Proof + Fresh-Install Regression (2026-07-15)"; publish record:
`docs/RELEASE_CHECKLIST.md`, "1.1.0 publish".

## Repo state you are starting from

- `main` = `3f89fc6`, **identical on origin and drupalcode** (verify with
  `git ls-remote <remote> main` before building on it).
- Remotes were repaired this session: `origin` →
  `https://github.com/zivtech/geo_starter.git` (was the project's original
  repo name `zivtech/ai-visibility-starter`, now **archived** on GitHub —
  that stale twin was why sessions kept resolving the wrong project name).
  `drupalcode` stays canonical. There is no `public` remote anymore.
- Public record of the 1.0.x fresh-install breakage + geo-install descope:
  **issue #3611199** (filed Fixed against 1.0.1).
- The 1.x line now carries a documented fragility: `canvas.page_region.*`
  pins component-version hashes coupled to the Drupal core era
  (`docs/LIMITATIONS.md`, "Canvas Component-Version Pins"). **Any core-era
  move can re-break fresh installs** — the cheap check is a stable-floor
  build + render check (`/`, a Service page, `/sitemap.xml` all 200); the
  fix is re-exporting the drifted `canvas.component.*` `active_version`s +
  the two page_region pin sites from a live install (see the 1.1.0 fix
  commit `1bb278a` for the exact shape).

## Open strands (rough priority order)

1. **SECURITY.md / LIMITATIONS may now UNDERCLAIM security coverage.** The
   updates feed reports `security covered="1"` for 1.0.0/1.0.1/1.1.0 and
   the release form no longer demanded the June-era "no coverage" confirm —
   the Security-Team opt-in appears to have landed since 2026-06-10.
   Verify the shield on https://www.drupal.org/project/geo_starter, then
   truth-up `SECURITY.md` and the `docs/LIMITATIONS.md` coverage line.
   Small, do first.
2. **Local WIP branch `wip/plans-005-009-local` (`aafd488`) — LOCAL ONLY,
   not pushed anywhere.** Parked implementations of plans/005–009: JSON:API
   read-only pin (`recipe.yml` config action + SECURITY.md section),
   per-type section-availability matrix (AUTHORING_MODEL), VALIDATION
   staleness banners, checklist quickstart-TAG item (now shipped
   separately). Reconcile against the shipped 1.1.0 docs (overlap likely in
   AGENTS/README/RELEASE_CHECKLIST), and note the recipe.yml change is a
   config action → **needs its own released-artifact install proof** before
   any release (1.1.1/1.2.0 scope). Recommend pushing the branch to origin
   first so it survives the machine.
3. **Advisor branches unmerged.** geo_starter `advisor/improve-2026-06`
   (2 ahead: `5f44b50` GitLab CI pipeline — this directly answers the
   review finding that CI covers only GitHub while drupalcode is canonical;
   `226e24d` phantom-depends lint fix — check it against the lint's
   invariant-3 evolution since June). geo_starter_jsonld
   `advisor/improve-2026-06` (6 ahead, spelling/lint sweep). The
   `/private/tmp/advisor-wt/*` worktrees are gone; run `git worktree prune`
   in the jsonld repo.
4. **geo-install redesign** — currently shipped as experimental/known-broken
   (warns on invocation). Five live-run findings + draft fix + two candidate
   redesign shapes: `docs/plans/2026-07-15-geo-install-redesign.md`.
   Gate before un-marking experimental: clean clone → `ddev start && ddev
   geo-install` → `GEO_STARTER_READY url=…` → `/` + Service page 200.
5. **Review-agent tool hardening (from the 1.1.0 branch review):**
   `tools/generate-content-model-schema.php` hardcodes
   `$bundles = ['service','answer','article','evidence_source']` — a new
   bundle added to `config/` passes `--check` silently; derive from
   `glob('config/node.type.*.yml')`. Same pattern:
   `tools/content-graph-lint.py` gates invariant 3 on
   `entity_type == 'canvas_page'` only.
6. **Queued follow-ups (unchanged):** Google Rich-Results URL-mode run
   against a public instance (last open WS-D item);
   `geo-demo.zivtech.com` liveness/lifetime check; a canvas upstream repro
   comment on #3563959/#3571366 was drafted but NOT posted (maintainer
   declined this round — revisit if the canvas issues move); watch the four
   June filings + #3611199 for responses.

## Hard boundaries (unchanged — AGENTS.md)

No guaranteed-citation/rich-result/Marketplace claims in anything you post
(security-coverage claims pending the strand-1 verification); no edits to
`config/` or `content/` without re-running the released-artifact install
proof; recipe stays `type: Site` / `drupal-recipe`; the content model is
frozen additive-only within 1.x.
