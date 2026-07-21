# Install

GEO Starter is a Drupal CMS site-template recipe package. **Fresh install is
the only supported path** (see "Stability contract" in `README.md`): recipes
are apply-once configuration artifacts, and no upgrade or migration path ships.

## Requirements

- A fresh Drupal CMS project (Drupal 11; validated on Drupal CMS 2.1.3 /
  core 11.4.4).
- Composer configured for Drupal packages (the drupal/cms project template
  already is).
- PHP per the required packages (Canvas requires PHP 8.3+).
- PHP CLI `memory_limit` of 256M+ (512M recommended) for the install step —
  the stock 128M limit runs out of memory mid-install while the recipe
  installs Canvas. `tools/quickstart.sh` raises it automatically
  (`PHP_MEMORY_LIMIT`, default 512M); for the manual path, run
  `php -d memory_limit=512M vendor/drush/drush/drush.php site:install …`
  if your CLI limit is low.
- All direct dependencies in a tagged release resolve at the default `stable`
  Composer minimum-stability floor — no stability override is permitted. The
  `1.2.0` pair was proven after `geo_starter_jsonld` 1.2.0 was published.

Required packages are declared in `composer.json`:

- `drupal/canvas` (`>=1.4 <1.6`)
- `drupal/drupal_cms_admin_ui` (`^2`)
- `drupal/drupal_cms_media` (`^2`)
- `drupal/drupal_cms_privacy_basic` (`^2`)
- `drupal/drupal_cms_seo_basic` (`^2`)
- `drupal/entity_reference_revisions` (`^1.14`)
- `drupal/geo_starter_jsonld` (`^1.2`) — the required JSON-LD companion module
- `drupal/mercury` (`>=1.0.5 <1.1`) — the public frontend theme
- `drupal/office_hours` (`^1.29`)
- `drupal/paragraphs` (`^1.20`)
- `drupal/simple_sitemap` (`^4.2`)

The Canvas and Mercury constraints are deliberately capped to the validated
minor range — see `docs/LIMITATIONS.md`, "bounded risk".

## Install Steps

Site templates are **not** served by the packages.drupal.org Composer facade
(`composer require drupal/geo_starter` will not resolve). The recipe tree is
placed from its release tag instead; this is the path validated in
`docs/VALIDATION.md` ("Released-Artifact Install Proof").

**Quick start (sole supported one-command path):**

From a GEO Starter checkout, run `tools/quickstart.sh <directory> [tag]`. It
wraps every step below, runs cron, prints a one-time login link, and defaults
to SQLite for a zero-configuration local trial. The acceptance proofs also ran
with MariaDB; pass `DB_URL='mysql://…'` for anything
production-representative. After a successful install it copies
`AGENTS.md` from the included installed-project handoff only if the new project
does not already have one.

`ddev geo-install` is deliberately unavailable. It fails closed rather than
attempting the known-broken installer.

**AI agents:** see `docs/AGENT_GUIDE.md` for the install → inspect → modify →
verify loop and `docs/api/` for the versioned machine-readable content model.

The manual steps below are reference/diagnostic instructions for the latest
public stable pair, `1.1.0`, not an alternate one-command path. The 1.2.0
candidate has passed its pre-tag proof but is not installable by tag yet:

```bash
composer create-project drupal/cms my-site
cd my-site

# A bare drupal/cms project does not carry the recipe's dependencies —
# the Drupal CMS installer adds them at install time, so the manual
# path must require them at the project root:
composer require 'drupal/geo_starter_jsonld:^1.0' \
  'drupal/canvas:>=1.4 <1.6' 'drupal/mercury:>=1.0.5 <1.1' \
  'drupal/paragraphs:^1.20' 'drupal/entity_reference_revisions:^1.14' \
  'drupal/office_hours:^1.29' 'drupal/simple_sitemap:^4.2' \
  'drupal/drupal_cms_admin_ui:^2' 'drupal/drupal_cms_media:^2' \
  'drupal/drupal_cms_privacy_basic:^2' 'drupal/drupal_cms_seo_basic:^2'

# Place the recipe at the release tag:
git clone --branch 1.1.0 https://git.drupalcode.org/project/geo_starter.git recipes/geo_starter
rm -rf recipes/geo_starter/.git
```

Then install, either way:

- **Drupal CMS installer (browser):** start the installer and select the
  GEO Starter site template.
- **Command line (the path used for validation):**

  ```bash
  drush site:install "$(pwd)/recipes/geo_starter" \
    --account-pass="$(openssl rand -base64 18)" -y
  ```

  Use a generated password (as above) and `drush uli` for admin access —
  never `--account-pass=admin` on anything public.

## Post-Install

1. **Run cron** (`drush cron`) and confirm `/sitemap.xml` is non-empty.
   `simple_sitemap` only populates on cron — recipes are config-only and
   cannot generate the sitemap at install time.
2. Confirm the install looks right:
   - The front page (`/geo-starter` Canvas shell) renders the composed
     homepage.
   - The demo content imported automatically — 4 Services, 8 Answers,
     3 Articles, 6 Evidence Sources, plus the Audience and Topic terms.
   - The editorial dashboard is at `/admin/content/geo`.
   - The sample Service page
     (`/apply-emergency-food-and-utility-assistance`) renders its sections
     and emits one `application/ld+json` block containing `Service` and
     `FAQPage`.

> **Do not run the `tools/*.php` generator scripts on a fresh install.** The
> recipe imports the demo content for you. `create-alpha-sample-content.php`
> deletes and re-creates the demo nodes, which collides with the content the
> recipe already imported. (`tools/quickstart.sh` and
> `tools/content-graph-lint.py` are fine — installer wrapper and dev lint.)
> See `docs/VALIDATION.md`, "Local Helper Scripts," for their intended use.

## Not Supported

- Applying this recipe to an established production site (fresh install only).
- In-place upgrades from pre-1.0 releases (the content model changed without
  an upgrade path before 1.0.0).
- Turnkey migration from another CMS (`docs/MIGRATION_MAP.md` is a destination
  map, not an importer).
- Marketplace installation claims.
- AI provider setup, autonomous agent access, or network/API agent-write
  workflows. The documented local draft-Article CLI is a separate, bounded
  operator tool: it requires trusted Drush execution, creates one unpublished
  draft only, and cannot publish, update, or delete content.

See `docs/VALIDATION.md` for the current acceptance evidence and
`docs/LIMITATIONS.md` for known limits.
