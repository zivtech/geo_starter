# Release Checklist

## Planned 1.2.0 release gate

- [x] **Development candidate preflight.** A local-path companion declared as
      `1.2.0` and the development recipe passed a clean Drupal CMS install,
      static contracts, JSON-LD probes, public HTTP checks, and the draft-only
      Article workflow on 2026-07-20. Exact evidence and its limits are in
      `docs/VALIDATION.md`. This is not a published-artifact proof.
- [ ] **Publish the companion first.** Tag and publish
      `drupal/geo_starter_jsonld 1.2.0`; require its native Drupal.org CI to
      pass Unit, Kernel, and Functional suites before the recipe release can
      resolve it.
- [ ] **Prove the published pair.** From a clean default-stability Drupal CMS
      project, resolve the published companion through the recipe's `^1.2`
      constraint and install the exact candidate recipe tag archive. Do not
      use path repositories, local checkouts, or dev/alpha stability escapes.
- [ ] **Run the complete installed-site contract.** Require install and cron,
      content-graph lint, schema drift and fixture checks, semantic OpenAPI
      validation, the JSON-LD parity probe, public page/sitemap/`llms.txt`
      checks, and the draft Article dry-run/apply/duplicate/access tests.
- [ ] **Synchronize release truth.** Update the changelogs, release notes,
      README/project-page status, support policy, and validation record with
      the exact tag, resolved package versions, runtime versions, and CI URLs.
      Preserve the boundaries: no guaranteed indexing, ranking, rich result,
      AI citation, or autonomous publishing claim.

## Historical 1.1.0 agent-readiness gates

- [x] **No MCP residue in shipped artifact.** Mechanized as
      `tools/mcp-residue-check.py` (also run in CI): over the files
      `git archive HEAD` actually ships, capability signatures
      (`simple_oauth`, `geo_agent`, the typed `geo.<tool>` names) fail
      anywhere; the broad tokens `mcp`/`oauth` fail in machine surfaces
      (`recipe.yml`, `composer.json`, `config/`, `content/`, `tools/`,
      `docs/api/` data files). Shipped prose may carry the M1 deferral/opt-in
      statements — the checker lists them for review instead of failing,
      because the original "zero matches" wording was unsatisfiable: the M1
      re-architecture deliberately kept those statements, and this checklist
      itself quotes the pattern. **PASS 2026-07-13:** 263 shipped files, 0
      capability signatures, machine surfaces clean, 25 sanctioned prose
      mentions (all deferral statements).
- [x] **Schema freshness / no drift.** `php tools/generate-content-model-schema.php
      --check` exits 0 against the real `config/`; `docs/api/openapi.yaml` lints.
      The schema `version` tracks the content model (frozen `1.0.0` within 1.x) —
      do **not** bump it for a recipe version change. **PASS 2026-07-13**, after
      running the gate surfaced and fixed two generator defects (autoloader
      checked via `class_exists()` *before* `require`, so `--check` could never
      run anywhere; hand-authored per-type descriptions clobbered despite the
      prose-preserve design) and after regenerating the schema — which
      corrected a real error in the committed file: it over-claimed
      `field_sections` target bundles on `answer` (8 claimed, 2 real) and
      `article` (8 claimed, 6 real). The schema `version` stays `1.0.0`: the
      content model did not change; the file now matches it.
- [x] **Clean stable floor survives.** **PASS 2026-07-15** on the fixed tree
      (recipe `1bb278a`) via the released-artifact method: fresh
      `composer create-project drupal/cms` at default stability (core
      11.4.3) + root-require of the dependency set → all stable versions,
      **no `mcp_server` / `simple_oauth`**; install clean; probe 23/23;
      content-graph-lint OK; `/`, Service page, `/sitemap.xml` 200 with
      JSON-LD present; `composer audit` clean. The run REQUIRED the canvas
      hash re-export — the pre-fix tree 500s on every HTML page (see
      CHANGELOG and `docs/VALIDATION.md`, "Stable 1.1.0 Released-Artifact
      Proof + Fresh-Install Regression").
- [x] **One-command scaffolding works.** `tools/quickstart.sh` **PASS
      2026-07-15** (live run: full build + one-time login link).
      `ddev geo-install` **FAILED** its first end-to-end live run (five
      distinct defects — see `docs/VALIDATION.md`) and is **descoped to
      experimental** for 1.1.0; the release notes and docs no longer claim
      it works, and the redesign is tracked in the project issue queue.

## Release-only cross-repository artifact smoke (required after dependency, recipe, config, or content changes)

This is a maintainer release gate, not push/PR CI. It proves the two published
artifacts together without a local path repository, credentials, or a public
demo dependency:

1. Start from a clean temporary directory and `composer create-project
   drupal/cms` at the default `stable` floor.
2. Require the exact dependency set, allowing Composer to resolve the
   published `drupal/geo_starter_jsonld` release from Drupal.org. Do not use a
   local checkout or `@dev`/`@alpha` override.
3. Materialize the exact candidate GEO Starter tag with `git archive` into
   `recipes/geo_starter`; run `site:install` and cron locally.
4. Record the recipe tag, resolved companion-module version, core/PHP/database
   versions, and command output. Require: install succeeds; content graph lint
   passes; the JSON-LD parity probe passes; `/`, the sample Service page, and
   `/sitemap.xml` return local 200 responses; and `composer audit` is clean.

The smoke establishes install and local rendered-artifact compatibility only.
It does not claim public-host availability, Google behavior, AI citations, or
Marketplace readiness.

## 1.1.0 publish (MAINTAINER — historical; followed the four 1.1.0 gates above)

- [x] **Release commit on merged `main`** — `644fe5f`, 2026-07-15 (all five
      flip sites: CHANGELOG date, README status + examples, INSTALL.md,
      quickstart.sh default TAG + usage, AGENTS.md quick reference; verified
      no stale `1.0.1` install examples remain).
- [x] Annotated tag `1.1.0` on `644fe5f`; pushed to **both** remotes and
      verified with `git ls-remote --tags` on each (same tag object
      `765042d` on drupalcode and origin/GitHub — the 1.0.0-miss lesson;
      that missing 1.0.0 tag was also pushed to GitHub this session).
- [x] d.o release node created 2026-07-15 from the tag — **nid 3611198**,
      https://www.drupal.org/project/geo_starter/releases/1.1.0, body from
      `docs/DRUPAL_ORG_RELEASE_NOTES_1.1.0.md` (HTML), release types Bug
      fixes + New features. Supported branches feed shows `1.0.,1.1.` —
      1.1.0 is the recommended download as latest in the highest supported
      branch.
- [x] Verified 2026-07-15: release page 200; api-d7
      `field_release_project=3592789`, `field_release_version=1.1.0`,
      `status:1` (nid 3611198); updates.drupal.org feed lists 1.1.0
      published with tar.gz + zip packaged.
- [x] Gate evidence recorded in `docs/VALIDATION.md` ("Stable 1.1.0
      Released-Artifact Proof + Fresh-Install Regression", landed with the
      pre-merge truth-up). Public record of the 1.0.x fresh-install
      breakage + geo-install descope: issue **#3611199**.

## Before Any Public Alpha Release

Latest clean install evidence is recorded in `docs/VALIDATION.md`. Rerun the install/apply proof if dependencies, recipe config, or imported content change before tagging.

- [x] Confirm Drupal.org project exists: `https://www.drupal.org/project/geo_starter`.
- [x] Confirm DrupalCode repository exists: `https://git.drupalcode.org/project/geo_starter`.
- [x] Confirm `composer validate --strict` passes.
- [x] Confirm `git diff --check` passes.
- [x] Confirm no secrets or credentials are present.
- [x] Rerun clean Drupal CMS install/apply after dependency, recipe config, content, or package-name changes. Last passed on 2026-05-29 for `1.0.0-alpha2` readiness after the corrected taxonomy and repaired content dependency graph.
- [x] Replace placeholder `screenshot.webp` with a representative alpha screenshot.
- [x] Confirm project page copy does not claim Marketplace readiness.
- [x] Confirm project page copy does not claim guaranteed AI placement.
- [x] Confirm limitations are linked from README/project page.
- [x] Confirm support and security policies are present.
- [x] Confirm dependency summary is current.
- [x] Confirm content and asset license summary is current.
- [x] Push current `main` to DrupalCode.
- [x] Tag `1.0.0-alpha1`.
- [x] Push the alpha tag to DrupalCode.
- [x] Tag `1.0.0-alpha2` (corrected taxonomy + repaired depends graph; supersedes the non-installing alpha1) and push to both remotes.
- [x] Create the `1.0.0-alpha2` release node on Drupal.org from the tag. Paste-ready source is in `docs/DRUPAL_ORG_RELEASE_NOTES_1.0.0-alpha2.md`.
- [x] Treat `1.0.0-alpha1` as superseded history; the public Drupal.org release listing exposes `1.0.0-alpha2`, and the direct alpha1 release URL returns `404`.

## Before Stable 1.0.0 Release

### Dependency stability gate (Phase 0)
- [x] Verify every direct `require` in `composer.json` resolves a stable release
      under a `stable` Composer floor. Confirmed via the packages.drupal.org p2
      facade (the authoritative resolution source; api-d7 paginates and
      undercounts). All dependencies stable as of 2026-06-08.
- [x] `drupal/geo_starter_jsonld` published stable at `1.0.0` on drupal.org.
      Facade carries `1.0.0`; release page returns 200.
- [x] Recipe `composer.json` constraint is plain `^1.0` (no `@beta` flag).
      Already set in beta2.

### Content-model freeze confirmation (pre-work gate)
- [x] Maintainer confirms nothing structural is pending. 1.x content-model
      freeze accepted: no field deletions, no field-type or storage changes,
      no vocabulary restructuring, no `@id` scheme changes within 1.x.
      Breaking changes will force `2.0.0`.

### Module prep (Phase 1 — `geo_starter_jsonld`)
- [x] Zero-diff assertion: `git diff 1.0.0-beta1 -- . ':!CHANGELOG.md' ':!README.md'`
      is empty — stable code is byte-identical to field-proven beta1 tree.
- [x] Module CHANGELOG updated with `## 1.0.0` entry.
- [x] Module README stability contract updated to 1.x stable framing.
- [x] Drupal.org GitLab CI green on the tagged commit (Unit + Kernel + Functional).

### Recipe constraint and docs (Phase 2 — `geo_starter`)
- [x] `composer.json` constraint is `drupal/geo_starter_jsonld: ^1.0` (no
      `@beta`). Already set since beta2.
- [x] Canvas/Mercury minor caps documented in `LIMITATIONS.md` bounded-risk
      note (already present since beta2).
- [x] `composer validate --strict` passes.
- [ ] GitLab CI green on the release commit (yaml-and-graph + composer-validate + schema-drift jobs).

### Tag-tree install rehearsal (Phase 3 — GATE)
- [x] Rehearsal built from `git archive` of the tag tree (not a working-tree
      rsync). Recipe `a38e31b`, module `ad1d8ab`.
- [x] `site:install` + recipe apply clean (exit 0); all sample content imports;
      `content-graph-lint.py` passes (49 entities, 145 edges, no cycles).
- [x] JSON-LD probe 23/23; Service page emits `Service` + `FAQPage`.
- [x] All four Canvas pages + front page render; `/sitemap.xml` generates
      post-cron, drafts excluded.
- [x] `composer audit` clean.
- Note: stable-floor resolution of `^1.0` is not locally testable (path repos
  bypass `minimum-stability`); deferred to Phase 5.

### Module publish (Phase 4 — `geo_starter_jsonld`)
- [x] Module `1.0.0-rc1` tagged + d.o release published.
- [x] RC released-artifact proof: fresh install under `minimum-stability: rc`,
      real d.o RC package, full Phase 3 DoD set + `composer audit` — GREEN
      (23/23 probe, no cycles, all pages render, canvas 1.5.1 within range).
- [x] Module `1.0.0` stable tagged + d.o release published (zero code change
      vs. rc1/beta1). Supported + recommended flags set.
- [x] Verified via API: release node status 1 for `geo_starter_jsonld 1.0.0`.

### Released-artifact proof (Phase 5 — GATE before recipe tag)
- [x] Fresh `composer create-project drupal/cms`, **default stability** (no
      override), pulling `geo_starter_jsonld 1.0.0` from drupal.org.
- [x] Dry-run resolves `^1.0` → `1.0.0` (no `@beta`/`@rc`/`@dev` in the set).
- [x] Full Phase 3 DoD assertions green on the real published artifact.
- [x] `composer audit` clean.

### Recipe docs flip (Phase 6 — `geo_starter`)
- [x] `README.md` updated to stable framing; Olivero → Mercury; GEO Readiness
      table corrected; "Not In This Scaffold Yet" pruned; stability contract
      updated to 1.x.
- [x] `CHANGELOG.md` `## 1.0.0` entry added.
- [x] `LIMITATIONS.md` refreshed: alpha framing removed; proof line updated to
      2026-06-08 stable released-artifact proof; bounded-risk and
      Marketplace-not-ready language preserved.
- [x] `docs/DRUPAL_ORG_RELEASE_NOTES_1.0.0.md` created.
- [x] `docs/RELEASE_CHECKLIST.md` "Before Stable 1.0.0 Release" section added.

### Launch copy truth pass (2026-06-09)
- [x] `docs/PROJECT_PAGE_DRAFT.md` re-synced from alpha4 to `1.0.0` (theme,
      Canvas pages, dashboard, sitemap, validator status, stability contract).
- [x] `docs/INSTALL.md` rewritten for stable with the **verified** install
      path (site templates are not composer-installable from
      packages.drupal.org — recipe tree placed from the release tag; full
      root `require` set; cron/sitemap post-install step).
- [x] `docs/DRUPAL_ORG_RELEASE_NOTES_1.0.0.md` install section corrected
      (previously instructed `composer require drupal/geo_starter`, which
      does not resolve).
- [x] `SUPPORT.md` / `SECURITY.md` flipped from alpha to 1.x stable framing
      (Security-Team opt-in stated as a pending post-1.0 follow-up).
- [x] `docs/DEPENDENCIES.md` re-checked for `1.0.0`: Mercury and
      simple_sitemap rows added; Canvas/Mercury bounded constraints recorded.
- [x] `docs/VALIDATION.md`: stale "authoritative evidence" banner fixed;
      "Not Proven Yet" list refreshed.
- [x] `docs/AUTHORING_MODEL.md`, `docs/SCHEMA_MAP.md`,
      `docs/MIGRATION_MAP.md`, `docs/PROVENANCE.md`,
      `docs/CONTENT_LICENSES.md`, `docs/MARKETPLACE_SUBMISSION_PACKET.md`,
      `docs/PUBLISHING_AND_ACCEPTANCE_PLAN.md`, `AGENTS.md` updated to the
      shipping 1.0.0 state.
- [x] Internal operations notes are excluded from the release archive.
- [x] `tools/content-graph-lint.py` green; all 226 YAML files parse;
      `composer validate --strict` passes.
- [x] Shipping-tree audit green (2026-06-09): no `uuid:`/`_core:` keys in
      `config/`; every internal URL in Canvas trees and link fields resolves
      to a shipped path alias; external links in sample content are
      fictional `example.org` only; shipped media has descriptive alt text;
      no future-dated content; no placeholder copy in rendered sample
      content (stock Mercury SDC defaults inside `canvas.component.*`
      excepted, by design).
- [x] `docs/TECHNICAL_ACCEPTANCE_PLAN.md` statuses re-synced for `1.0.0`;
      `README.md` install quick-start added (verified path); CHANGELOG
      1.0.0 entry notes the docs sync + corrected install instruction.

### Agent-era follow-through (2026-06-09)
- [x] `tools/quickstart.sh` added: one-command wrapper around the verified
      install path (SQLite trial default; `DB_URL` override; never run by
      the recipe). Wired into `README.md`, `docs/INSTALL.md`, and
      `docs/VALIDATION.md` "Local Helper Scripts".
- [x] `AGENTS.md` Quick Reference added: 80%-path map (install, layout,
      content model, JSON-LD extension points, validation gates) for
      coding agents consuming the repo.
- [x] Upstream Issue 4 drafted in the internal filing record:
      Composer facade does not serve site-template projects (haven control
      test; cites the agent-discoverability framing).
- [x] MAINTAINER: run the Issue 4 dedup gate (d.o queue search + Site
      Templates ADR) and file — d.o blocks automated search from the
      sandbox. **Done 2026-06-10** (recorded in
      filing gate verdict "file full", filed to the `project_composer` queue as
      work item #3583682 with RIK #3571905 evidence.
- [ ] MAINTAINER (optional community-proposal experiment): evaluate an
      `llms.txt`/agent manifest in `geo_starter_jsonld` only if an operator has
      a non-Google use case and measurable acceptance criteria. Google Search
      does not use this file, including for generative search, so it is not a
      ranking or citation feature.

### Recipe publish (Phase 7 — `geo_starter`) — MAINTAINER
- [x] Tag annotated `geo_starter 1.0.0`; push to drupalcode + origin.
      Done 2026-06-09: tag `1.0.0` = `cf48153`. (The tag predates the launch
      copy truth pass — those docs ship in `1.0.1`.)
      **Correction 2026-07-13:** the tag is live on drupalcode, but it never
      reached origin/GitHub — `git ls-remote --tags origin` shows no `1.0.0`.
      Queued as SESSION_HANDOFF Task C-2: push the original tag object from a
      clone that has both remotes; do **not** re-create the tag (a fresh tag
      object at the same commit would diverge from the canonical drupalcode
      one and break fetches for anyone tracking both).
- [x] Create d.o release node from the tag. Set supported + recommended flags.
      Done 2026-06-09: release node **3594492**, flags set.
- [x] Verify via API: `field_release_project=3592789`, `status:1`.
      Done 2026-06-09.
      (Project nid confirmed 2026-06-09 via
      `api-d7/node.json?field_project_machine_name=geo_starter` → 3592789,
      calibrated against the module's known-good 3592912. An earlier
      revision of this line said 3552789 — that was a transcription typo.)
- [x] Update `docs/VALIDATION.md` with the released-artifact proof evidence
      (added 2026-06-09: "Stable 1.0.0 Released-Artifact Proof (2026-06-08)").
- [x] Paste the corrected project-page copy (`docs/PROJECT_PAGE_DRAFT.md`,
      synced 2026-06-09) to the live drupal.org project page.
      Verified done 2026-07-15: the live page body (api-d7 nid 3592789)
      matches the draft (the paste itself was performed 2026-06-10; the
      remote container could not reach d.o to confirm, so the box stayed
      open until this local verification).

### Docs/tooling release (`1.0.1`, 2026-06-10) — MAINTAINER

- [x] PR #2 (launch copy truth pass) reviewed — drupal-critic
      ACCEPT-WITH-RESERVATIONS + claim verification against the published
      d.o state — and merged to `main`.
- [x] Verification gates on the merged tree: `content-graph-lint` OK
      (49 entities, no cycles); all 226 YAML files parse;
      `composer validate --strict`; `git diff --check`.
- [x] `tools/quickstart.sh` live end-to-end run (SQLite default path).
      The run caught a real defect — PHP OOM at the stock 128M CLI
      `memory_limit` during Canvas module install — fixed by running drush
      through its PHP entry point at `PHP_MEMORY_LIMIT` (default 512M).
      Recorded in `docs/VALIDATION.md`.
- [x] CHANGELOG `1.0.1` entry added (the docs-sync bullet was relocated out
      of the `1.0.0` entry — the published `1.0.0` tag predates the truth
      pass); install examples and the quickstart default tag bumped to
      `1.0.1`; `docs/DRUPAL_ORG_RELEASE_NOTES_1.0.1.md` created.
- [x] Tag annotated `1.0.1`; push `main` + tag to drupalcode + origin +
      public (GitHub). Done 2026-06-10: tag `1.0.1` = `d12a7e1` on all
      three remotes; PR #2 auto-closed as merged.
- [x] Create the d.o release node from the tag; verify via api-d7.
      Done 2026-06-10: release node **3595535**, `status:1`, page 200,
      Bug fixes + New features set, short description + HTML release
      notes body filled, stable-without-SA-coverage acknowledgment
      confirmed. The `1.0.x` branch flags carry from `1.0.0`, so `1.0.1`
      becomes the recommended release of the branch.

## Before Marketplace Submission

- [ ] Decide applicant path and support owner.
- [ ] Confirm all required dependencies are stable and patch-free.
- [ ] Complete theme/design-system proof.
- [ ] Complete Canvas authoring proof. Canvas shell proof exists; component-composed page proof remains.
- [ ] Complete Paragraphs authoring proof. Runtime save/render proof exists; manual editor UI/reorder proof remains.
- [ ] Complete rendered HTML proof.
- [ ] Complete JSON:API/access proof after Paragraphs exist.
- [ ] Complete sitemap/search proof.
- [ ] Add JSON-LD only where rendered content supports it.
- [ ] Complete WCAG 2.2 AA review.
- [ ] Complete performance review.
- [ ] Complete responsive screenshot review.
- [ ] Complete privacy/security attestations.
- [ ] Create preview/demo URL.
- [ ] Finalize screenshot and marketing images.
- [ ] Finalize content and asset license summary.
- [ ] Finalize project/listing copy.
- [ ] Run proposal/copy review.
- [ ] Submit complete repositories, marketing materials, draft content, and images.
