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

**Status:** Issue 1 is finalized + MR-ready (patch at
`docs/plans/patches/core-defaultcontent-cycle-detection.patch`). Issues 2 & 3
are ready to use; each carries ONE file-time confirmation to make in your own
browser (noted inline) — automated reads of #2706883 / #3532514 / #3537695 were
blocked by drupal.org's Cloudflare wall (Chrome 148 wouldn't expose a CDP port
on the live session).

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

**File-time check — glance at #2706883** to confirm the root cause matches:
- If identical (NULL entity → unguarded `getRevisionId()`): post the comment below **on #2706883**.
- If #2706883 is specifically *deleted-target* and this is *never-set-target*: post as a narrow **sibling issue** linking both #2706883 and #2675076 — "the NULL-on-set companion to the NULL-on-delete case."

**Comment draft (for #2706883):**

> Still reproduces on 1.14, and there's a clear asymmetry that makes the fix a one-liner: `EntityReferenceRevisionsItem::onChange()` guards the `target_id` write for a new/unsaved target via `isTargetNew()`, but the sibling `target_revision_id` write calls `$property->getValue()->getRevisionId()` with no null-guard — so when the computed `entity` property is NULL, it fatals with `Call to a member function getRevisionId() on null`.
>
> Real-world trigger seen in the wild: Drupal core's `DefaultContent` importer passes a failed dependency lookup (NULL) into an ERR field when recipe default content contains a `_meta.depends` cycle (filed separately against core — core shouldn't pass NULL, but ERR fataling here makes it much harder to diagnose). Any `$item->set('entity', NULL)` reproduces it.
>
> Fix: mirror the `target_id` branch — guard the `getRevisionId()` call and write NULL to `target_revision_id` when there is no referenced entity. Happy to roll a patch + test.

---

## Issue 3 — canvas — SPLIT: one tight bug + one docs question

**Why changed:** overlaps active work (#3537695 `componentMeetsRequirements`, #3532514 component-DX, #3567586 theme-change-blocked-by-SDC-deps). The original "two questions for maintainers" format reads as a support request and tends to get redirected. Two maintainers' documented positions suggest that skipping un-generatable components and making recipes ship the config explicitly may be the **intended contract** — so the only cleanly defensible defect is **the silence** (no watchdog when a component is skipped).

**File-time check — glance at #3532514 + #3537695** to confirm whether the silent-skip is already tracked (→ comment there) or genuinely new (→ file 3a tightly).

**3a — Bug (file tight, or comment on #3532514):**
- Title: `Component-config generation skips some theme SDCs without logging why (no watchdog) — surface the exclusion`
- Body: on a fresh Drupal CMS install (drupal_cms_* 2.1.3, core 11.3.11, Mercury 1.0.5), Canvas 1.5.0 generates `canvas.component.sdc.mercury.*` config for most SDCs but **silently skips `cta`, `section`, `hero-billboard`** with no watchdog entry. Recipe-shipped `canvas_page` content referencing them then fails import validation (`The 'canvas.component.sdc.mercury.section' config does not exist`). Ask: name the disqualifier (what the `componentMeetsRequirements`-style gate rejects — a prop/slot/schema shape), and at minimum **log a notice when a component is excluded** instead of skipping silently. Cross-link #3537695, #3532514.

**3b — Docs / change-record question (NOT a bug):**
- Is shipping `canvas.component.*` config the intended contract for recipes that ship Canvas content? geo_starter ships **11** such files; the Drupal CMS `byte` and `haven` site templates follow the same pattern. If that is the contract, documenting it would save the next recipe author this diagnosis.
- Secondary observation (own paragraph): on this installer stack, component-config generation also runs *after* recipe content import, so even generatable components are unavailable when recipe-shipped `canvas_page` content validates.

---

*Source repro detail: `docs/VALIDATION.md` → "Released-Artifact Install Proof";
commits `ebdfc26` (core/ERR cycle workaround) and `596a0d5` (canvas component
configs). Core fix patch: `docs/plans/patches/core-defaultcontent-cycle-detection.patch`.*
