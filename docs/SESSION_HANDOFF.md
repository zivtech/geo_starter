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

- `main` = the 2026-07-15 session head (security truth-up `860e563`, tool
  hardening `afcc8e4`, GitLab CI `186e875`+`cc3c8c0`, geo-install mode flip
  `3477afe`, this handoff update), **kept identical on origin and
  drupalcode** (verify with `git ls-remote <remote> main` before building
  on it). Both CIs are green on main: GitHub Actions and drupalcode
  pipeline 891062.
- Parked branches on origin: `wip/plans-005-009-reconciled` (`c860a7d`),
  `wip/phantom-depends-reconciled` (`82ac787`), plus the superseded
  snapshot `wip/plans-005-009-local` (`aafd488`). On drupalcode:
  `advisor-improve-trial` (jsonld repo) awaiting an MR to run CI.
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

1. **DONE 2026-07-15 — security coverage verified and docs truthed-up.**
   Shield confirmed live on the project page AND updates feed
   `covered="1"` for 1.0.0/1.0.1/1.1.0. Updated: `SECURITY.md` (covered +
   Security-Team confidential reporting path), `docs/LIMITATIONS.md` (new
   coverage bullet), `docs/PROJECT_PAGE_DRAFT.md` (stale "not covered yet"
   bullet replaced), `docs/PUBLISHING_AND_ACCEPTANCE_PLAN.md`,
   `docs/MARKETPLACE_SUBMISSION_PACKET.md`. **Residue: the LIVE project
   page still shows the old "Not covered by the Drupal Security Team yet"
   bullet under Current Limitations, contradicting its own shield — needs
   a project-page sync (outward-facing; use the PROJECT_PAGE_DRAFT +
   memory recipe) once approved.**
2. **DONE 2026-07-15 — plans-005-009 reconciled and parked on origin.**
   `wip/plans-005-009-reconciled` (`c860a7d`) = the parked work rebased
   onto post-1.1.0 main with conflicts resolved (LIMITATIONS content_format
   paragraph precedes the Canvas-pins section; quickstart keeps the 1.1.0
   TAG under the new RELEASE-COUPLED comment); the AUTHORING_MODEL
   availability matrix was verified against shipped field config
   (service 8 / article 6 / answer 2). The pre-1.1.0 snapshot stays at
   `wip/plans-005-009-local` (`aafd488`, also on origin) — delete it when
   comfortable. **Gate unchanged: the recipe.yml jsonapi read_only config
   action needs its own released-artifact install proof (1.1.1/1.2.0).**
3. **geo_starter advisor branch LANDED; jsonld branch trial-parked.**
   - `5f44b50` (GitLab CI) cherry-picked to main + a parity commit adding
     the mcp-residue and schema-drift gates (`cc3c8c0`). Proven the honest
     way: trial branch on drupalcode failed first (python:3.12-slim has no
     `git` for mcp-residue-check's `git archive` — fixed with apt install +
     safe.directory), second trial green, then main pushed — drupalcode
     main pipeline **891062 success**, GitHub CI green on the same SHA.
     The "CI only on GitHub while drupalcode is canonical" finding is
     closed. `advisor/improve-2026-06` itself is now superseded — safe to
     delete after confirming nothing else is wanted from it.
   - `226e24d` (phantom-depends strip + resolvability lint) reconciled as
     `wip/phantom-depends-reconciled` (`82ac787`, on origin): resolvability
     renumbered to **invariant 4** (main's 3 is component coverage), sits on
     top of the shape-based collection hardening. Verified: stripped content
     reproduces June's exact 49/117 numbers; same lint against main content
     fails with exactly the 28 phantoms. **PARKED — edits `content/`, so it
     rides the same install-proof cycle as strand 2.**
   - geo_starter_jsonld `advisor/improve-2026-06`: the old handoff's
     "spelling/lint sweep" undersold it — it carries real emission fixes
     (HowTo name from section heading; malformed-URL guards +
     potentialAction cacheability; llms_txt co-enablement requirements
     guard) plus ~850 lines of new kernel tests, then the en-US sweep.
     Pushed to drupalcode as `advisor-improve-trial`, but d.o workflow
     rules only run pipelines for MRs/default-branch/tags — **opening the
     MR needs maintainer action** (one click from the branch page, or
     authorize the push-option MR). Merge decision after a green suite.
   - jsonld worktrees pruned (`git worktree prune` done).
4. **geo-install redesign** — unchanged: experimental/known-broken, five
   live-run findings + draft fix in
   `docs/plans/2026-07-15-geo-install-redesign.md`. Gate before un-marking
   experimental: clean clone → `ddev start && ddev geo-install` →
   `GEO_STARTER_READY url=…` → `/` + Service page 200. (The live-run's
   stray mode flip on `.ddev/commands/web/geo-install` is now committed.)
5. **DONE 2026-07-15 — both tool hardenings shipped (`afcc8e4`).**
   Schema generator derives bundles from `glob(config/node.type.*.yml)` and
   exits 2 with an actionable message on unmapped bundles (fixture-tested);
   content-graph-lint collects component_ids from any content file
   (shape-based, not entity_type-gated) — old lint PASSED a planted
   non-canvas carrier with an unshipped component, new lint fails it; real
   content output byte-identical to baseline. contentTypes key order in
   docs/api/content-model.schema.json is now alphabetical (verified
   content-identical modulo order).
6. **Queued follow-ups:** `geo-demo.zivtech.com` is **GONE — DNS record
   removed** (parent zivtech.com resolves; checked 2026-07-15), so the
   Google Rich-Results URL-mode run (last open WS-D item) stays blocked
   until a public instance exists — decide: re-provision demo vs. drop the
   demo-URL Marketplace row. Canvas upstream repro comment on
   #3563959/#3571366 still drafted-not-posted (maintainer declined June
   round); watch the four June filings + #3611199 for responses.

## Hard boundaries (unchanged — AGENTS.md)

No guaranteed-citation/rich-result/Marketplace claims in anything you post
(security-coverage claims pending the strand-1 verification); no edits to
`config/` or `content/` without re-running the released-artifact install
proof; recipe stays `type: Site` / `drupal-recipe`; the content model is
frozen additive-only within 1.x.
