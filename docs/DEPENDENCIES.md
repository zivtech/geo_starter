# Dependency Summary

**Last checked:** 2026-05-29

This starter declares stable Composer constraints and must not pin exact versions or require patches.

## Direct Requirements

| Package | Constraint | Purpose | Current compatibility notes |
| --- | --- | --- | --- |
| `drupal/drupal_cms_admin_ui` | `^2` | Drupal CMS admin experience | Provided by Drupal CMS ecosystem. |
| `drupal/drupal_cms_media` | `^2` | Media handling baseline | Provided by Drupal CMS ecosystem. |
| `drupal/drupal_cms_privacy_basic` | `^2` | Basic privacy baseline | Provided by Drupal CMS ecosystem. |
| `drupal/drupal_cms_seo_basic` | `^2` | Basic SEO baseline | Provided by Drupal CMS ecosystem. |
| `drupal/canvas` | `^1.4` | Canvas visual authoring lane | 1.4.1 metadata requires Drupal core `^11.2` and PHP 8.3. |
| `drupal/paragraphs` | `^1.20` | Paragraphs structured-node authoring lane | 8.x-1.20 metadata supports Drupal `^10.3 || ^11` and depends on Entity Reference Revisions. |
| `drupal/entity_reference_revisions` | `^1.14` | Revisioned Paragraph references | 8.x-1.14 metadata supports Drupal `^10.2 || ^11`; composer metadata conflicts with Drush `<12.5.1`. |

## Canvas Module Dependencies Reflected In `recipe.yml`

Canvas 1.4.1 lists these core/module dependencies that are relevant to the recipe install list:

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

1. Verify current stable/security-covered status on Drupal.org.
2. Re-check `.info.yml` and `composer.json` metadata from Drupal GitLab for direct required packages.
3. Confirm no required package is dev, alpha, beta, or RC.
4. Confirm no dependency patches are needed.
5. Confirm the target Drupal CMS project resolves all constraints.
6. Rerun clean install/apply validation.

Use Drupal GitLab metadata directly when checking modules that are not yet installed in the local Composer project.
