# Drupal.org Release Notes — geo_starter 1.0.1

Paste-ready source for the drupal.org release node.

**Short description (form field, plain text):**

Documentation and tooling release — no recipe changes; sites on 1.0.0 need
no action. Corrects the published install instructions (composer require
does not resolve site templates; use the documented tag-tree path), adds a
proven one-command installer (tools/quickstart.sh), and syncs all public
docs to the stable 1.0.0 state.

---

## Release notes (body)

Documentation and tooling release. **No recipe changes**: `config/`,
`content/`, `recipe.yml`, and `composer.json` are identical to `1.0.0` —
sites installed from `1.0.0` need no action.

### Corrected install instructions

The `1.0.0` tag tree's docs still instructed
`composer require drupal/geo_starter`, which does not resolve — site
templates are not served by the packages.drupal.org Composer facade. All
install docs now document the verified path: tag-tree clone into
`recipes/`, root-level dependency `require` set, `drush site:install`, then
one cron run for the sitemap. See `docs/INSTALL.md`.

### One-command installer

`tools/quickstart.sh <directory> [tag]` wraps the verified install path
(SQLite trial default, `DB_URL` override), proven by a live end-to-end run
(`docs/VALIDATION.md`). The run surfaced and fixed a PHP out-of-memory
failure at the stock 128M CLI `memory_limit` during install; the script now
runs drush at `PHP_MEMORY_LIMIT` (default 512M), and `docs/INSTALL.md`
documents the memory requirement for the manual path.

### Docs synced to the shipping 1.0.0 state

Project page copy, INSTALL, SUPPORT/SECURITY, dependency summary, and the
validation/acceptance docs now reflect what `1.0.0` actually ships: Mercury
public theme, four composed Canvas sample pages, the ten-bundle section
library, the `/admin/content/geo` editorial dashboard, the XML sitemap, and
the hosted schema.org validator pass. An `AGENTS.md` Quick Reference was
added for coding agents consuming the repository.

## Install

```bash
git clone --branch 1.0.1 https://git.drupalcode.org/project/geo_starter.git
./geo_starter/tools/quickstart.sh my-site 1.0.1
```

or the manual path in `docs/INSTALL.md`.
