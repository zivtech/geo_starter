# Session Handoff — 1.1.0 release queue (2026-07-13)

Internal working note. Not part of the recipe's public documentation set
(export-ignored). Supersedes the 2026-06-09 posting queue: its Task A items
1–3 (publish `1.0.0`) and Task B (all four upstream filings) are done and
recorded; its quickstart live-run proof shipped with `1.0.1`. Carried
forward below: the project-page paste, the missing `1.0.0` tag on origin,
and the post-1.0 follow-ups.

**Audience:** the local agent (Claude with the Chrome browser + drupal.org
credentials + Docker/DDEV + unrestricted network) on the maintainer's
machine. The remote container cannot do these steps: packages.drupal.org
and git.drupalcode.org are egress-blocked, www.drupal.org Fastly-blocks
automated fetch, release-node creation hits a CAPTCHA, and there is no
Docker. Everything below is queued, paste-ready work.

## Repo state you are starting from

Branch `claude/dazzling-shannon-12k0me`, ahead of `main` (`b37dbc4`, the
merged 1.1.0 subset) with: schema-generator fixes + regenerated
`content-model.schema.json` (corrects the answer/article section-bundle
over-claim), content-graph-lint invariant 3 (component-config coverage),
`tools/mcp-residue-check.py`, CI (`.github/workflows/ci.yml`), the 1.1.0
gate results + publish checklist, and `DRUPAL_ORG_RELEASE_NOTES_1.1.0.md`.

Container-runnable gates are green on this branch (2026-07-13):
`composer validate --strict`; 227 YAML files parse; content-graph-lint OK
(49 entities, 145 edges, no cycles, 11 placed components all have shipped
configs); mcp-residue-check OK (263 shipped files, 0 capability signatures,
machine surfaces clean); schema drift `--check` OK. The two install-shaped
gates (stable floor, `ddev geo-install`) are **open** — they are Task A.

**Step 0 — human decision:** review + merge this branch to `main` before
any tagging. Do not tag from the feature branch.

## Task A — Run the two remaining 1.1.0 gates (local resources required)

1. **Clean stable floor survives (released-artifact method).** On merged
   `main`: `git archive <ref> | tar -x` into `recipes/geo_starter` of a
   fresh `composer create-project drupal/cms` at **default stability** (no
   override); root-`require` the recipe's dependency set (a bare
   `drupal/cms` project does not carry the `drupal_cms_*` recipes — see
   `docs/VALIDATION.md`, "Stable 1.0.0 Released-Artifact Proof", and the
   `geo-phase5` harness in `~/Documents/Codex/ddev-tests/`). Assert: the
   resolved set contains **no `mcp_server` / `simple_oauth`** and no
   `@beta`/`@rc`/`@dev` among geo_starter's added requires;
   `drush site:install recipes/geo_starter` exit 0; JSON-LD probe 23/23;
   `tools/content-graph-lint.py` OK; `/`, a Service page, `/sitemap.xml`
   (post-cron) render; `composer audit` clean.
2. **One-command scaffolding.** Clean `ddev geo-install` build emits
   `GEO_STARTER_READY url=…`; `tools/quickstart.sh my-site` prints a login
   link.
3. Check both gate boxes in `docs/RELEASE_CHECKLIST.md` with dates; record
   the evidence in `docs/VALIDATION.md`; commit.

## Task B — Publish `1.1.0`

Follow the new **"1.1.0 publish"** section of `docs/RELEASE_CHECKLIST.md`
verbatim. Summary: one release commit on merged `main` flips every version
string at tag time (CHANGELOG `unreleased` → date; README status +
quick-start examples; `docs/INSTALL.md` example; `tools/quickstart.sh`
default `TAG`; `AGENTS.md` example — the checklist lists exact lines);
annotated tag `1.1.0`; push **both** remotes and verify with
`git ls-remote --tags` on each; d.o release node from
`docs/DRUPAL_ORG_RELEASE_NOTES_1.1.0.md` with **supported + recommended**
flags (CAPTCHA — expect the human at the browser); verify
`field_release_project=3592789`, `field_release_version=1.1.0`,
`status:1` (api-d7 lags; release page 200 is authoritative).

## Task C — Drupal.org hygiene (browser, carried over)

1. **Paste the project page** (`docs/PROJECT_PAGE_DRAFT.md`, synced
   2026-06-09) to the live project page — the last open Phase 7 box. Same
   demo-link caveat: do **not** add a "Try it" link unless
   `https://geo-demo.zivtech.com` is verified live AND its lifetime is
   confirmed with Alex (`docs/DEMO_RUNBOOK.md` teardown rule).
2. **Push the `1.0.0` tag to origin/GitHub.** Phase 7 recorded it as pushed
   to both remotes, but `git ls-remote --tags origin` showed no `1.0.0` as
   of 2026-07-13 (verified from the remote container; drupalcode could not
   be checked from there). From a clone with both remotes:
   `git fetch drupalcode tag 1.0.0 && git push origin 1.0.0`.

## Task D — Queued post-1.0 follow-ups (valuable, not blocking)

- Drupal Security Team **opt-in coverage application** (stable-release
  prerequisite met since 1.0.0; SECURITY.md already frames the current
  not-yet-covered state).
- Google **Rich-Results URL-mode run** against a public instance — the last
  open WS-D item; unblocks any rich-result eligibility claim
  (`docs/VALIDATION.md`, "Not Proven Yet").
- `geo-demo.zivtech.com` liveness/lifetime check (feeds the Task C
  demo-link decision).
- `llms.txt`/agent manifest on the installed site as a `geo_starter_jsonld`
  feature (candidate; a recipe cannot ship docroot files).

## Hard boundaries (unchanged — AGENTS.md)

No guaranteed-citation/rich-result/Marketplace/security-coverage claims in
anything you post; no edits to `config/` or `content/` without re-running
the released-artifact install proof; recipe stays `type: Site` /
`drupal-recipe`; the content model is frozen additive-only within 1.x.
