# Dependency Summary

**Last checked:** 2026-06-08 (for `1.0.0` — Phase 0 dependency stability gate,
verified against the packages.drupal.org Composer p2 facade)

This starter declares stable Composer constraints and must not pin exact versions or require patches.

## Direct Requirements

All direct requirements resolve stable releases at the default `stable`
Composer minimum-stability floor (verified 2026-06-08; the full resolved tree
was additionally proven by the released-artifact install proof — see
`docs/VALIDATION.md`).

| Package | Constraint | Purpose | Current compatibility notes |
| --- | --- | --- | --- |
| `drupal/drupal_cms_admin_ui` | `^2` | Drupal CMS admin experience | Provided by Drupal CMS ecosystem. |
| `drupal/drupal_cms_media` | `^2` | Media handling baseline | Provided by Drupal CMS ecosystem. |
| `drupal/drupal_cms_privacy_basic` | `^2` | Basic privacy baseline | Provided by Drupal CMS ecosystem. |
| `drupal/drupal_cms_seo_basic` | `^2` | Basic SEO baseline | Provided by Drupal CMS ecosystem. |
| `drupal/canvas` | `>=1.4 <1.6` | Canvas visual authoring lane | Validated on 1.5.0 and 1.5.1; requires Drupal core `^11.2` and PHP 8.3. Minor range deliberately capped — see the bounded-risk note in `docs/LIMITATIONS.md`. |
| `drupal/mercury` | `>=1.0.5 <1.1` | Public frontend theme; its SDCs are the components the shipped Canvas page trees use | Floor is 1.0.5 (the committed trees and shipped `canvas.component.*` configs encode 1.0.5 prop schemas, e.g. the required CTA `overlay_opacity`). Minor range capped — raising it requires re-export and re-validation. |
| `drupal/paragraphs` | `^1.20` | Paragraphs structured-node authoring lane | 8.x-1.20 metadata supports Drupal `^10.3 \|\| ^11` and depends on Entity Reference Revisions. |
| `drupal/entity_reference_revisions` | `^1.14` | Revisioned Paragraph references | 8.x-1.14 metadata supports Drupal `^10.2 \|\| ^11`; composer metadata conflicts with Drush `<12.5.1`. |
| `drupal/geo_starter_jsonld` | `^1.0` | Companion module emitting schema.org JSON-LD from the recipe's content model (a recipe cannot bundle a module, so it ships as its own package) | Published stable at `1.0.0` on drupal.org (2026-06-08); carries its own 1.x stability contract. Resolves without any stability override. |
| `drupal/office_hours` | `^1.29` | Structured opening-hours field on the contact panel (`field_section_hours`), the source for JSON-LD `hoursAvailable` | Added in `1.0.0-alpha4`; the module reads it defensively (recipe-side requirement only). |
| `drupal/simple_sitemap` | `^4.2` | XML sitemap for crawler/agent discoverability; indexes the four canonical node types + Canvas pages | Added at beta1 (WS-F). Sitemap populates on the first cron run — recipes cannot generate it at install time. |

## Canvas Module Dependencies Reflected In `recipe.yml`

Canvas (1.4–1.5) lists these core/module dependencies that are relevant to the recipe install list:

- `block`
- `ckeditor5`
- `datetime`
- `editor`
- `file`
- `filter`
- `image`
- `link`
- `media_library`
- `options`
- `path`
- `text`

## Paragraphs Dependencies Reflected In `recipe.yml`

Paragraphs 8.x-1.20 lists:

- `entity_reference_revisions`
- `file`

Entity Reference Revisions lists:

- `field`

## Release Checks

Before each public release:

1. Verify current release status on Drupal.org. Verify against the
   packages.drupal.org Composer p2 facade — it is the authoritative
   resolution source (the api-d7 endpoint paginates and undercounts).
2. Re-check `.info.yml` and `composer.json` metadata from Drupal GitLab for direct required packages.
3. Confirm no required package is dev, alpha, beta, or RC.
4. Confirm no dependency patches are needed.
5. Confirm the target Drupal CMS project resolves all constraints at the default `stable` floor.
6. If the Canvas or Mercury cap moves: re-export the shipped `canvas.component.*` configs and re-validate the committed Canvas trees before widening the constraint.
7. Rerun the released-artifact install/apply validation (`docs/VALIDATION.md`).

Use Drupal GitLab metadata directly when checking modules that are not yet installed in the local Composer project.
