# Release Checklist

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

### Recipe publish (Phase 7 — `geo_starter`) — MAINTAINER
- [ ] Tag annotated `geo_starter 1.0.0`; push to drupalcode + origin.
- [ ] Create d.o release node from the tag. Set supported + recommended flags.
- [ ] Verify via API: `field_release_project=3552789`, `status:1`.
      (Confirm the correct project nid before publishing — see `VALIDATION.md`.)
- [ ] Update `docs/VALIDATION.md` with the released-artifact proof evidence.

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
