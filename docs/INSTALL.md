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
6. Run the sample-content helper scripts from the Drupal root with Drush if sample content was not imported by the recipe flow.

Example helper-script usage:

```bash
drush php:script /path/to/geo_starter/tools/create-alpha-sample-content.php
drush php:script /path/to/geo_starter/tools/create-jsonapi-access-probes.php
```

## Not Supported Yet

- Applying this alpha to an established production site.
- Turnkey migration from another CMS.
- Marketplace installation claims.
- AI provider setup or agent workflows.

See `docs/VALIDATION.md` and `docs/TECHNICAL_ACCEPTANCE_PLAN.md` before using the alpha for a public demo.
