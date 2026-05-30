# Session Handoff — Alpha2 Published After Taxonomy Re-validation (2026-05-30)

Internal working note for the GEO Starter taxonomy work. Not part of the recipe's
public documentation set.

## State

`main` is at `9944133`, tagged `1.0.0-alpha2`, and pushed to **both** remotes
(`origin` = GitHub, `drupalcode` = drupal.org). The taxonomy-inversion
correction has been **validated on a real Drupal CMS install** and published as
the alpha2 Drupal.org release. Public Drupal.org checks on 2026-05-30 showed the
release listing exposes `1.0.0-alpha2`; the direct alpha1 release URL returns
`404`, so alpha1 is superseded history rather than an active release task.

## What re-validation found and fixed

The prior handoff's one unverified assumption ("field_topic required is satisfied
on every demo node") was **false** — the recipe did not install at all.

- **Bug:** the inversion re-pointed `field_topic` to the new `topic` subject terms
  but left a **stale `depends` graph**. Nine nodes (eight Answers + one Article)
  referenced the new terms without declaring them in `depends`. With `field_topic`
  required on Service/Answer, content import aborted:
  `field_topic=This value should not be null`.
- **Fix (`6ebf774`):** added the missing term dependencies to all nine nodes.
  Full-tree scan (`content/**/*.yml`, 31 files) now reports 0 missing references.
- **Tools/docs fix (`69ab473`):** `docs/INSTALL.md` had told end users to run the
  `tools/` helper scripts on a fresh install. `create-alpha-sample-content.php`
  calls `deleteExistingNodes()` and re-creates the demo nodes — destructive and
  colliding with the bundled `content/`. The access-probe script also referenced
  `$eligibility` (a removed page-aspect term). Pointed the article probe at
  `$benefits`, rewrote the INSTALL step, and marked the scripts dev-only/destructive
  in `docs/VALIDATION.md`.

## Validation evidence (fresh Drupal CMS DDEV install)

Test project: `/Users/AlexUA_1/Documents/Codex/ddev-tests/geostarter-reval-20260529-171053`

- `drush site:install recipes/geo_starter` → `Installation complete.` (3 clean runs)
- 22 nodes import (21 published, 1 draft = Privacy policy); bundles
  `service=4, answer=8, article=3, evidence_source=6, page=1`.
- Taxonomy `topic=4, audience=5`; `service_area`/`field_service_area` absent;
  `field_topic` present. `topic` returns the four subjects.
- JSON:API: published collection `200` (4 items), individual published `200`,
  all four draft probes `403`, no draft leakage. Front `/` and a service alias `200`.

Recorded in `CHANGELOG.md`, `docs/VALIDATION.md`
("Corrected-Taxonomy Acceptance Proof"), and
`docs/DRUPAL_ORG_RELEASE_NOTES_1.0.0-alpha2.md` (banner cleared).

## Remaining

- Alpha2 is the current public alpha. There is no remaining alpha1 admin gate.
- Optional: TraceAIO per-model GEO measurement of the sample content.
- Optional: `docs/AUTHORING_MODEL.md` paragraph on the required/optional asymmetry.
- Housekeeping: `ddev delete` the test project above when no longer needed.
