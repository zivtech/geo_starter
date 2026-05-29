# Validation

## Current Smoke-Test Evidence

The current alpha package has been validated in a disposable Drupal CMS DDEV install.

Passed checks:

- `composer validate --strict`
- Fresh Drupal CMS install with the recipe selected
- Import of 4 Services, 8 Answers, 3 Articles, 6 Evidence Sources, 5 Audience terms, 8 Topic terms, and 4 Service area terms
- Anonymous JSON:API detail endpoints return `200` for published sample content
- Anonymous JSON:API detail endpoints return `403` for draft probe content
- JSON:API collection endpoints exclude draft probe content

## Local Helper Scripts

The helper scripts are not part of Drupal runtime behavior. They are included to make the alpha smoke-test process reproducible:

- `tools/create-alpha-sample-content.php`
- `tools/create-jsonapi-access-probes.php`

Run them from a Drupal root with Drush, for example:

```bash
drush php:script /path/to/tools/create-alpha-sample-content.php
drush php:script /path/to/tools/create-jsonapi-access-probes.php
```

## Not Proven Yet

- Final theme implementation
- Rendered JSON-LD/schema output
- Accessibility review
- Internal search behavior
- Sitemap behavior
- Marketplace submission readiness

