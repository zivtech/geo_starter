# Upstream Issue Submissions — REVISED PLAN (rev. 2026-06-08)

Supersedes the "file three issues as drafted" plan. After (a) a duplicate
search of all three queues and (b) a grounded maintainer-reaction review, the
plan changed to: **1 reframed core issue backed by a tested patch · 1 comment
on an existing ERR issue · a canvas split (1 tight bug + 1 docs question).**
Net: two likely-bounce filings avoided. Original drafts preserved in
`2026-06-07-upstream-issue-drafts.md` + git history; full review saved at
`~/claude/drupal-core-reviews/2026-06-08-geo-starter-upstream-issues.md`.

drupal.org issues are permanent (closeable, never deletable) — confirm every
Version/Component against the live drop-down before submitting.

**Status (filing log, 2026-06-10):**

- **Issue 1 — FILED: [#3595546](https://www.drupal.org/project/drupal/issues/3595546)**
  (queue `drupal`, Bug report, Normal, `11.x-dev`, component
  **`default content system`** — exact dropdown match, better than the
  guessed "recipe system"). Patch verified to apply clean on the 11.x tip
  (`364e29e1`); MR branch `3595546-defaultcontent-cycle-detection`
  committed locally — issue fork + push pending.
- **Issue 2 — POSTED:** comment on
  [#2706883](https://www.drupal.org/project/entity_reference_revisions/issues/2706883)
  (15th comment), cross-linking core #3595546 and #2675076, as drafted.
- **Issues 3a/3b — pending GitLab session:** canvas takes new issues as
  git.drupalcode.org **work items** (`/node/add/project-issue/canvas` 301s
  to `/project/canvas/-/work_items/new`); bodies converted to markdown
  (cross-refs as full d.o URLs).
- **Issue 4 — DEDUP GATE RUN (2026-06-10), verdict: FILE THE FULL ISSUE.**
  (a) Site-Templates ADR read in full — templates "MAY be published on
  drupal.org as general projects", MUST ship `composer.json`; **no
  installer-only distribution contract documented** → not an intentional
  omission on the record. (b) No duplicate found in the drupalorg or
  project_composer queues. (c) Premise re-verified same day: p2 facade 404s
  for `geo_starter` (now stable 1.0.1), `byte`, `haven`, **and
  `drupal_cms_starter`**. (d) NEW evidence: recipe_installer_kit
  [#3571905](https://www.drupal.org/project/recipe_installer_kit/issues/3571905)
  (closed/fixed) makes the installer `composer require` templates not in the
  codebase — which packages.drupal.org cannot serve today; added to the body.
  **Queue corrected to `project_composer`** (packages.drupal.org's own queue,
  where the analogous profiles issue #3131693 lives) — it also takes new
  issues as GitLab work items, so filing rides the same GitLab session.

Original rationale: Issue 1 reframed + patch-backed; issue 2 → comment on
#2706883 (a fresh filing would be a duplicate); issue 3 split (both related
canvas issues #3532514/#3537695 are closed/fixed).

---

## Issue 1 — Drupal core — FILE (reframed, patch-backed)

- **Queue:** https://www.drupal.org/node/add/project-issue/drupal
- **Category:** Bug report
- **Priority:** Normal  *(flag the silent-data-loss severity in-body; let maintainers decide Major)*
- **Version:** `11.x-dev`  *(or the current dev branch — confirm; the patch applies to both, Finder/Graph are unchanged across the lines)*
- **Component:** `recipe system`  *(confirm dropdown; else `base system` / `default content`)*
- **Title:** `Importer should detect unresolvable _meta.depends cycles and throw ImportException instead of importing NULL references`

**Body (paste):**

### Problem

`\Drupal\Core\DefaultContent\Finder::__construct()` builds the dependency graph keyed by UUID, sorts it with `\Drupal\Component\Graph\Graph::searchAndSort()`, and reduces it to an ordered `$this->data` — **with no check for dependency cycles**. When `_meta.depends` forms a cycle (e.g. A→B→C→A), no valid topological order exists, but `searchAndSort()` still returns an arbitrary order, so at least one entity is imported before a dependency it references.

In `\Drupal\Core\DefaultContent\Importer`, `setFieldValues()` then calls `loadEntityDependency()`, which returns **NULL** because the unresolved target isn't yet imported; that NULL is passed straight to `$property->setValue()`. (Method line numbers vary by branch; verified on the current dev tip.)

- For an `entity_reference` property this **silently stores an empty reference** — data loss whose occurrence depends on the filesystem iteration order of the Symfony Finder scan, so a recipe can pass CI on one machine and silently lose different data on another.
- For `entity_reference_revisions` it is **fatal**, aborting `drush site:install`.

There is no NULL-guard in `loadEntityDependency()` and no cycle detection upstream. Silent, machine-dependent data loss is the worst outcome here; failing loudly at import time surfaces the authoring mistake immediately. (Filing as Normal, but flagging the data-loss angle for a priority call.)

### Steps to reproduce

Author default-content YAML whose `_meta.depends` declarations form a cycle (e.g. a node → paragraph (ERR `field_sections`) → node → first node), then `drush site:install <recipe>`. Observed on a service node → paragraph (ERR) → node → service cycle.

### Proposed fix (patch + tests ready)

Detect cycles in `Finder` immediately after `Graph::searchAndSort()` and throw `ImportException` naming the offending source files, instead of returning a broken order. A cycle is detected by self-reachability (`isset($vertex['paths'][$uuid])`); the full member set of each cycle is the strongly-connected component `paths ∩ reverse_paths` (so the back-edge vertex is named too, not just the entry vertices).

Tests included:
- **Unit** (`FinderTest`): three-node cycle, self-loop, and two disjoint cycles — asserts every file in every cycle is named.
- **Kernel** (`ImporterTest`): a cyclic directory makes `importContent()` throw **before writing anything** (fail-before-write). Verified RED→GREEN at both levels; no regressions across DefaultContent unit + kernel tests; PHPCS (Drupal/DrupalPractice) clean.

### Related

- **#3442022** (Trigger entity validation in the core Importer — validate before save): adjacent. Cycle detection is upstream of, and complementary to, entity validation; worth deciding whether they should land together.
- A NULL-guard in `Importer::loadEntityDependency()` would be complementary defense-in-depth, but it can only see one unresolved UUID mid-import and **can't name the cycle**. Proposing the Finder fix as fix-of-record, with the guard as optional follow-up hardening.

> **Filing mechanics:** file the issue → create an issue fork on git.drupalcode.org → apply `docs/plans/patches/core-defaultcontent-cycle-detection.patch` to the fork branch → open the MR. Let DrupalCI run the full suite (this fix now *throws* where the Finder previously limped on a lucky order, so CI confirms no shipped fixture/recipe harbors a latent cycle this exposes).

---

## Issue 2 — entity_reference_revisions — DO NOT FILE NEW; comment on #2706883

**Why changed:** the NULL→`getRevisionId()` fatal is already **#2706883** ("Fatal error when call to getRevisionId()", open since 2016, same root); **#2675076** is the adjacent `onChange('entity')` revision-id concern. A fresh standalone filing gets closed as a duplicate. The novel, fix-shaped thing #2706883 has lacked is the **asymmetry**.

**Confirmed (read #2706883):** its framing is *deleted*-target ("references to paragraphs … that was deleted"); ours is *never-set* target (the importer passes NULL). It is the **same unguarded `getRevisionId()` call**, so one null-guard fixes both → **post the comment below on #2706883**, noting it generalizes to both triggers. Also link **#2675076** (the adjacent `setValue` / `onChange('entity')` revision-id concern). #2706883 is old and low-activity ("Fix it.", no real progress since 2016) — the asymmetry below is the concrete, fix-shaped nudge it has lacked.

**Comment draft (for #2706883):**

> Still reproduces on 1.14, and there's a clear asymmetry that makes the fix a one-liner: `EntityReferenceRevisionsItem::onChange()` guards the `target_id` write for a new/unsaved target via `isTargetNew()`, but the sibling `target_revision_id` write calls `$property->getValue()->getRevisionId()` with no null-guard — so when the computed `entity` property is NULL, it fatals with `Call to a member function getRevisionId() on null`.
>
> Real-world trigger seen in the wild: Drupal core's `DefaultContent` importer passes a failed dependency lookup (NULL) into an ERR field when recipe default content contains a `_meta.depends` cycle (filed separately against core — core shouldn't pass NULL, but ERR fataling here makes it much harder to diagnose). Any `$item->set('entity', NULL)` reproduces it.
>
> Fix: mirror the `target_id` branch — guard the `getRevisionId()` call and write NULL to `target_revision_id` when there is no referenced entity. Happy to roll a patch + test.

---

## Issue 3 — canvas — SPLIT: one tight bug + one docs question

**Why changed:** overlaps active work (#3537695 `componentMeetsRequirements`, #3532514 component-DX, #3567586 theme-change-blocked-by-SDC-deps). The original "two questions for maintainers" format reads as a support request and tends to get redirected. Two maintainers' documented positions suggest that skipping un-generatable components and making recipes ship the config explicitly may be the **intended contract** — so the only cleanly defensible defect is **the silence** (no watchdog when a component is skipped).

**Confirmed (read both — now GitLab work items, both CLOSED/fixed):** #3532514 added *verbose error logging for **broken** components* (SDCs that disappear/rename/break after having a config) — **not** for components silently **skipped at generation**, so it does not cover this. #3537695 removed a hardcoded Image-SDC check from `componentMeetsRequirements`, delegating UI-exclusion to the `noUi` flag + core #3535958. Neither tracks the silent generation skip → **file 3a as a NEW issue**, cross-referencing both (and core #3535958) as context.

**3a — Bug (file NEW — related canvas issues #3532514 / #3537695 are both closed/fixed):**
- Title: `Component-config generation skips some theme SDCs without logging why (no watchdog) — surface the exclusion`
- Body: on a fresh Drupal CMS install (drupal_cms_* 2.1.3, core 11.3.11, Mercury 1.0.5), Canvas 1.5.0 generates `canvas.component.sdc.mercury.*` config for most SDCs but **silently skips `cta`, `section`, `hero-billboard`** with no watchdog entry. Recipe-shipped `canvas_page` content referencing them then fails import validation (`The 'canvas.component.sdc.mercury.section' config does not exist`). Ask: name the disqualifier — is `componentMeetsRequirements` rejecting a prop/slot/schema shape, or are these flagged `noUi` (cf. #3537695 / core #3535958)? — and at minimum **log a notice when a component is excluded from generation** instead of skipping silently. (Note: #3532514 added verbose logging for *broken* components, but not for ones skipped at generation time, so this is an unaddressed gap.) Cross-link #3537695, #3532514, core #3535958.

**3b — Docs / change-record question (NOT a bug):**
- Is shipping `canvas.component.*` config the intended contract for recipes that ship Canvas content? geo_starter ships **11** such files; the Drupal CMS `byte` and `haven` site templates follow the same pattern. If that is the contract, documenting it would save the next recipe author this diagnosis.
- Secondary observation (own paragraph): on this installer stack, component-config generation also runs *after* recipe content import, so even generatable components are unavailable when recipe-shipped `canvas_page` content validates.

---

## Issue 4 — drupal.org Composer facade — DRAFT (2026-06-09); DEDUP REQUIRED BEFORE FILING

**Why this issue:** site templates are not served by the packages.drupal.org
Composer facade — `composer require drupal/geo_starter` fails for every user
who tries the obvious command (control: `drupal/haven`, a released Drupal CMS
site template, 404s identically — `VALIDATION.md` → "Released-Artifact Install
Proof"). Strategic frame: Dries's post on AI coding agents
(https://dri.es/do-ai-coding-agents-recommend-drupal-2026) sets "site templates
… discoverable and programmatically applicable" as the success metric; a
facade that 404s the canonical Composer verb for an entire project type is the
exact gap. An agent or developer who tries `composer require` on a site
template hits session-time risk in its purest form.

**Dedup gate (do this first — could not be run from the sandbox; d.o returns
503 to automated fetch):** search the `drupalorg` queue for
`site template composer`, `facade site template`, and `project type site
template packaging`; check whether the Drupal CMS site-template ADR
(git.drupalcode.org drupal_cms wiki → Site-Templates) or the Marketplace
application docs document an intended non-Composer distribution channel. If
distribution-by-installer-only is the documented contract, file only the 4b
docs ask below.

- **Queue:** https://www.drupal.org/node/add/project-issue/drupalorg
  (Drupal.org customizations) — confirm component against the live dropdown
  (likely `Composer & Packaging`; else ask in #drupalorg Slack before filing).
- **Category:** Bug report *(downgrade to Feature request if the omission is
  confirmed intentional)*
- **Priority:** Normal
- **Title:** `Composer facade does not serve site-template projects —
  'composer require' fails for every published site template`

**Body (paste):**

### Problem

Published site-template projects (recipe packages with `type: Site`) are not
served by the packages.drupal.org Composer facade. `composer require
drupal/<site_template>` fails with "could not be found in any version" even
when the project has a published release with supported/recommended flags.

Verified 2026-06-07/08 while validating `drupal/geo_starter` 1.0.0:

- `composer require drupal/geo_starter` → not found (project page and release
  node live, release packaged).
- Control: `drupal/haven` (a released Drupal CMS site template) 404s on the
  facade identically, so this is the project type, not one project's
  packaging.
- Ordinary modules resolve normally in the same session
  (`drupal/geo_starter_jsonld 1.0.0` resolves and installs at the default
  `stable` floor).

### Why it matters

The practical install path for a site template today is "git clone the tag
into `recipes/` and require the dependency set manually" — fine for a
documented runbook, invisible to anyone (or any tool) that tries the
canonical Composer verb first. With site templates positioned as the primary
starting point for Drupal CMS, the first command a developer — or an AI
coding agent — will try is `composer require drupal/<template>`; it failing
silently undermines the "discoverable and programmatically applicable"
goal for site templates (cf. https://dri.es/do-ai-coding-agents-recommend-drupal-2026).

### Ask

1. If site templates are intended to be Composer-installable: add the
   project type to the facade (and to the packaging pipeline if releases are
   not currently packaged for it).
2. If distribution-by-installer-only is the intended contract: document that
   contract prominently (site-template docs + project pages), so template
   maintainers can document the supported path instead of users discovering
   the 404 themselves.

### Workaround in use

`composer create-project drupal/cms` + root-level `composer require` of the
template's dependency set + `git clone --branch <tag>` of the template into
`recipes/` + `drush site:install recipes/<template>` — documented in
`drupal/geo_starter`'s INSTALL.md and proven in its release validation.

---

*Issue 4 source detail: `docs/VALIDATION.md` → "Released-Artifact Install
Proof" (facade behavior + haven control), `docs/INSTALL.md` (documented
workaround path).*
