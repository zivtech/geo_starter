# Install

GEO Starter is a Drupal CMS site-template recipe package.

## Requirements

- Drupal CMS compatible project.
- Composer configured for Drupal packages.
- PHP and Drupal CMS versions compatible with the required packages.

Required packages are declared in `composer.json`:

- `drupal/drupal_cms_admin_ui`
- `drupal/drupal_cms_media`
- `drupal/drupal_cms_privacy_basic`
- `drupal/drupal_cms_seo_basic`
- `drupal/canvas`
- `drupal/paragraphs`
- `drupal/entity_reference_revisions`

## Community Alpha Path

Use this template as part of a new Drupal CMS site-template test install. The exact command path depends on the Drupal CMS project and recipe installer flow you are using.

The release gate is to test it like an end user:

1. Create a clean Drupal CMS project.
2. Require this recipe package.
3. Start the Drupal CMS installer.
4. Select or apply the GEO Starter site template.
5. Confirm the recipe applies without patches or manual config edits.
6. Confirm the demo content imported automatically — 4 Services, 8 Answers,
   3 Articles, 6 Evidence Sources, plus the Audience and Topic terms.

> **Do not run the `tools/` scripts on a fresh install.** The recipe imports the
> demo content for you. The scripts in `tools/` are development-only generators:
> `create-alpha-sample-content.php` deletes and re-creates the demo nodes, which
> collides with the content the recipe already imported. See
> `docs/VALIDATION.md`, "Local Helper Scripts," for their intended use.

## Not Supported Yet

- Applying this alpha to an established production site.
- Turnkey migration from another CMS.
- Marketplace installation claims.
- AI provider setup or agent workflows.

See `docs/VALIDATION.md` and `docs/TECHNICAL_ACCEPTANCE_PLAN.md` before using the alpha for a public demo.
