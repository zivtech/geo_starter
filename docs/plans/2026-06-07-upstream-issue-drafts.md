# Upstream Issue Drafts (2026-06-07)

Internal working note. Three upstream bugs isolated during the WS-E
released-artifact install proof (`docs/VALIDATION.md` → "Released-Artifact
Install Proof"). Drafts ready to file; filing is a maintainer action.
geo_starter is no longer blocked on any of them (workarounds shipped in
`ebdfc26` / `596a0d5`), so these are contributions, not dependencies.

---

## 1. Drupal core — DefaultContent importer: detect dependency cycles instead of silently importing NULL references

**Project:** Drupal core · **Component:** default content / recipe system · **Type:** bug

The `Drupal\Core\DefaultContent\Importer` performs no cycle detection over
`_meta.depends`. When recipe default content contains a dependency cycle
(entity A depends on B, B on C, C on A), no topological order exists, so in
*every* import order at least one reference resolves before its target is
imported. `Importer::setFieldValues()` passes the failed
`loadEntityDependency()` lookup (NULL) straight to `$property->setValue()`
(Importer.php line ~318, 11.3.11):

- For core `entity_reference` items this **silently imports an empty
  reference** — data loss with no warning, and which reference is dropped
  depends on filesystem iteration order (a recipe can pass CI on one
  machine and lose different data on another).
- For `entity_reference_revisions` items it is **fatal** (see ERR issue
  below), aborting `site:install`.

Repro: three YAML files forming a depends cycle where at least one edge is
an ERR field; `drush site:install <recipe>`. Observed with a service node →
paragraph (ERR `field_sections`) → node → service cycle.

Suggested fix: topologically sort in the Finder and fail with an explicit
`ImportException` naming the cycle when sorting is impossible. Silent NULL
import is the worst outcome; a loud error at import time would have caught
this at authoring time.

---

## 2. entity_reference_revisions — NULL-guard `EntityReferenceRevisionsItem::onChange()`

**Project:** entity_reference_revisions (1.14) · **Type:** bug

`EntityReferenceRevisionsItem::onChange()` (line 230):

```php
$this->writePropertyValue('target_revision_id', $property->getValue()->getRevisionId());
```

When the computed `entity` property is set to NULL (e.g., core's
DefaultContent importer passing a failed dependency lookup — see core issue
above — but any caller doing `$item->set('entity', NULL)` triggers it), this
fatals with `Error: Call to a member function getRevisionId() on null`.
The sibling `target_id` branch already guards via `isTargetNew()`; the
`target_revision_id` write should null-guard the same way and write NULL.

Full trace available from `drush site:install` of a recipe whose content
contains a depends cycle through an ERR field.

---

## 3. canvas — 1.5.0 component-config generation silently skips some theme SDCs (mercury cta / section / hero-billboard)

**Project:** canvas (1.5.0) · **Type:** bug

On a fresh Drupal CMS install (drupal_cms_* 2.1.3, core 11.3.11) with
Mercury 1.0.5 as the default theme, canvas generates `canvas.component.sdc.
mercury.*` config entities for 15 of Mercury's components but silently
skips `cta`, `section`, and `hero-billboard` — no watchdog entry observed.
Deleting other generated component configs and rebuilding caches regenerates
them; the three named components are never created. Any recipe-shipped
canvas_page content referencing them then fails import validation with
"The 'canvas.component.sdc.mercury.section' config does not exist."

Two questions for the maintainers:

1. What disqualifies these three SDCs from generation (all three render
   fine when their component configs are shipped as recipe config), and
   should the skip at least log?
2. Timing: on this installer stack, generation also runs *after* recipe
   content import, so even generatable components are unavailable when
   recipe-shipped canvas_page content validates. Site templates work around
   both issues by shipping `canvas.component.*` config (byte ships 41 such
   files) — if that is the intended contract for recipes that ship Canvas
   content, documenting it would save the next recipe author this hunt.

---

*Disposition: file 1 and 2 together (they pair: core should fail loudly,
ERR should not fatal). 3 is canvas-queue. All repro detail is in
`docs/VALIDATION.md` and commits `ebdfc26` / `596a0d5`.*
