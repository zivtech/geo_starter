# GEO Starter Public Demo — Runbook

Internal working note. Not part of the recipe's public documentation set.

| | |
|---|---|
| URL | `https://geo-demo.zivtech.com` |
| Owner | Alex Urevick-Ackelsberg (alex@zivtech.com) |
| Host | Zivtech infrastructure — provisioning ticket [IN-386](https://zivtechjira.atlassian.net/browse/IN-386) (Jonathan DeLaigle) |
| Posture | **Public and crawlable by design** (the demo dogfoods the GEO thesis: answer engines can only cite what they can fetch). Sample/fictional content only — no client data, ever. |
| Lifetime | Lives through the beta cycle. Teardown/refresh is coordinated with the owner; remove the project page "Try it" link *before* tearing down. |

## Build (from released artifacts — mirrors the proven install,
## `docs/VALIDATION.md` → "Released-Artifact Install Proof")

```bash
mkdir geo-demo && cd geo-demo
composer create-project drupal/cms .

# A bare drupal/cms project does NOT include these recipe dependencies —
# the CMS installer adds them at install time; the manual path must
# require them. The module's @beta flag must be at the ROOT (composer
# ignores stability flags on transitive constraints).
composer require 'drupal/geo_starter_jsonld:^1.0@beta' \
  'drupal/drupal_cms_admin_ui:^2' 'drupal/drupal_cms_media:^2' \
  'drupal/drupal_cms_privacy_basic:^2' 'drupal/drupal_cms_seo_basic:^2' \
  'drupal/office_hours:^1.29'

# Site templates are not served by the packages.drupal.org composer
# facade — place the recipe at the release tag (1.0.0-beta2 once cut):
git clone --branch <TAG> https://git.drupalcode.org/project/geo_starter.git recipes/geo_starter
rm -rf recipes/geo_starter/.git

# Generated secret — NEVER --account-pass=admin. Do not store the value;
# use `drush uli` for admin access.
drush site:install "$(pwd)/recipes/geo_starter" \
  --account-pass="$(openssl rand -base64 18)" -y
```

## Post-install (required, in order)

1. **Run cron** (`drush cron`) and confirm `/sitemap.xml` is non-empty —
   simple_sitemap only populates on cron; a fresh install ships an empty
   sitemap. Then add the system crontab entry for Drupal cron.
2. **Demo disclosure** — add a short visible line to the homepage stating
   this is a demo with sample/fictional content. **On the demo instance
   only — never in the recipe's `content/`** (that would ship demo copy
   into every user's install).
3. **Indexability** — confirm `robots.txt` permits crawling (deliberate
   decision, Alex 2026-06-07: crawlable).

## Verification matrix (WS-E DoD — verified, not assumed; record in VALIDATION.md)

| Check | Expected |
|---|---|
| `https://geo-demo.zivtech.com/` anonymous | 200 over HTTPS, renders C-01 |
| Sample Service/Answer/Article/Evidence pages anonymous | 200; `application/ld+json` in source |
| Draft content anonymous (HTML + JSON:API) | denied (403 / not found) |
| `/admin`, `/user/1` anonymous | denied / redirect to login |
| Seed or test accounts | none exist (admin only, generated secret) |
| `robots.txt` | crawl permitted |
| `/sitemap.xml` | non-empty, lists canonical types + canvas pages |

## Agent-readiness checks (1.1.0 subset — run on a live stack)

These verify the dependency-free agent work. No MCP is involved (the recipe
ships none — see `docs/OPTIONAL_MCP.md`).

| Check | Expected |
|---|---|
| `ddev start && ddev geo-install` (fresh clone) | Ends on `GEO_STARTER_READY url=…`; site renders. Time it (the "one minute" target). |
| `tools/quickstart.sh my-site` (no DDEV) | Same site stood up; one-time login link printed. |
| `php tools/generate-content-model-schema.php --check` | Exit 0 (schema matches `config/`). |
| `composer require drupal/geo_starter` resolves at default stable floor | No `mcp_server` / `simple_oauth` pulled in (clean-floor property intact). |
| JSON-LD acceptance probe | 23/23 (no regression — subset adds no config/content). |

## Refresh / teardown

- **Refresh:** re-run `drush site:install` from the current release tag
  (drops the DB; the demo intentionally carries no persistent data). Then
  repeat **Post-install** above.
- **Teardown:** remove the "Try it" link from the drupal.org project page
  first, then DNS + host (coordinate via a new IN ticket referencing
  IN-386).
