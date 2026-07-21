# Installed GEO Starter Project — Agent Handoff

This is a fresh-install Drupal CMS project built with GEO Starter. Treat the
running site and its editorial workflow as the source of truth; do not infer
field names or publish content directly from a draft artifact.

## 80% path

1. Confirm the project root and runtime before editing:

   ```bash
   test -f composer.json && test -x vendor/bin/drush
   vendor/bin/drush status
   ```

2. Read `recipes/geo_starter/docs/AGENT_GUIDE.md` and inspect
   `recipes/geo_starter/docs/api/content-model.schema.json` before making
   field, bundle, or workflow assumptions.
3. Keep site customizations in a custom module/theme and exported project
   configuration. Do not edit installed contrib or `recipes/geo_starter/` in
   place to customize this site.
4. Preserve the authoring lanes: Canvas is for composed visual pages and
   Paragraphs (`field_sections`) are for canonical Service, Answer, and Article
   content. Do not mix them freely on one canonical page.
5. Extend JSON-LD only in the companion module through its tagged normalizer
   and paragraph-contributor services; rendered content remains the parity
   boundary.
6. For config/content work, run the recipe's graph lint from its checkout,
   then in this project run `vendor/bin/drush config:export --diff`,
   `vendor/bin/drush cache:rebuild`, and local route/JSON-LD checks before
   handing work to an editor.

## Draft-article artifact contract

For an Article, use the explicit local draft-only lane:

```bash
mkdir -p artifacts/drafts
cp recipes/geo_starter/docs/api/examples/draft-article.valid.json \
  artifacts/drafts/<slug>.json
php recipes/geo_starter/tools/validate-draft-article.php \
  artifacts/drafts/<slug>.json
vendor/bin/drush php:script recipes/geo_starter/tools/import-draft-article.php -- \
  --artifact="$PWD/artifacts/drafts/<slug>.json" --actor-uid=<editor-uid>
```

Do not import the fixture unchanged. Replace its demo content and dates, use a
new lowercase RFC 4122 `entityUuid`, and resolve every reference UUID from this
site rather than inventing one from the example.

The default is dry-run, but both modes require explicit attribution to an active
editor with `create article content` and run the same access checks:

```bash
vendor/bin/drush php:script \
  recipes/geo_starter/tools/import-draft-article.php -- \
  --artifact="$PWD/artifacts/drafts/<slug>.json" \
  --actor-uid=<editor-uid> --apply
```

It creates one new unpublished Article in `draft`, refuses an existing UUID,
excludes Paragraph sections, and cannot publish, update, archive, or delete.
The actor UID is audit attribution, not terminal authentication: the operator
already needs trusted Drush/server access. The command returns the node ID,
UUID, and SHA-256 of the exact artifact bytes it parsed. Keep that artifact
immutable; record those identifiers and the review decision in a separate
ticket or log. A human editor owns the workflow transition.

## Boundaries

- Do not apply the recipe to an existing site; GEO Starter supports fresh
  installs only.
- Do not assume a network/API write endpoint, MCP server, or AI provider.
- Do not claim Google/AI rankings, citations, or rich-result display from this
  site configuration.
- Keep rendered text and JSON-LD aligned. Make site-specific structural work
  in project-owned config/code. If the reusable recipe itself must change,
  make that change in a separate upstream GEO Starter source checkout—not this
  installed copy—and prove it with a new fresh install.
