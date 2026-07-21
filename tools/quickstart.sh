#!/usr/bin/env bash
#
# GEO Starter quickstart — one command from nothing to a running site.
#
# Wraps the verified install path (docs/INSTALL.md; proven in
# docs/VALIDATION.md → "Released-Artifact Install Proof") without changing
# it: create a Drupal CMS project, require the recipe's dependency set,
# place the recipe tree at a release tag, install, run cron, print a
# one-time login link.
#
# Usage:
#   tools/quickstart.sh <directory> [tag]
#
#   tools/quickstart.sh my-site            # recipe at latest stable tag 1.1.0
#   tools/quickstart.sh my-site 1.1.0      # explicit stable tag
#   DB_URL='mysql://user:pass@host/db' tools/quickstart.sh my-site
#
# DB_URL defaults to SQLite (zero configuration) for a quick local trial.
# The release acceptance proofs ran on MariaDB under DDEV; for anything
# beyond a trial, pass a real database via DB_URL. The manual commands in
# docs/INSTALL.md are diagnostic/reference material, not a second supported
# one-command installer.
#
# Drush runs with PHP_MEMORY_LIMIT (default 512M): the stock 128M CLI
# limit OOMs mid-install while the recipe installs Canvas (live-run
# finding, 2026-06-10 — see docs/VALIDATION.md).
#
# This script is an operator convenience and is NOT run by the recipe
# install. It needs: composer, git, php (per Drupal CMS requirements).

set -euo pipefail

TARGET_DIR="${1:-}"
TAG="${2:-1.1.0}"
DB_URL="${DB_URL:-sqlite://localhost/sites/default/files/.ht.sqlite}"
PHP_MEMORY_LIMIT="${PHP_MEMORY_LIMIT:-512M}"
RECIPE_REPO="https://git.drupalcode.org/project/geo_starter.git"

usage() { grep '^#' "$0" | tail -n +2 | sed 's/^# \{0,1\}//'; }

if [ -z "$TARGET_DIR" ] || [ "$TARGET_DIR" = "-h" ] || [ "$TARGET_DIR" = "--help" ]; then
  usage
  exit 1
fi

for bin in composer git php; do
  command -v "$bin" >/dev/null 2>&1 || { echo "error: '$bin' is required but not found." >&2; exit 1; }
done

if [ -e "$TARGET_DIR" ]; then
  echo "error: '$TARGET_DIR' already exists — refusing to touch it. Pick a new directory." >&2
  exit 1
fi

echo "==> Creating Drupal CMS project in '$TARGET_DIR'"
composer create-project --no-interaction drupal/cms "$TARGET_DIR"
cd "$TARGET_DIR"

echo "==> Placing the recipe at tag $TAG"
# Site templates are not served by the packages.drupal.org Composer facade,
# so the recipe tree is placed from its release tag:
git clone --branch "$TAG" --depth 1 "$RECIPE_REPO" recipes/geo_starter
rm -rf recipes/geo_starter/.git

echo "==> Requiring the recipe's declared dependencies at the project root"
# Read the selected tag's composer.json instead of duplicating constraints in
# this wrapper. That keeps a historical tag paired with the companion version
# it actually declared, while future tags automatically get their own set.
RECIPE_REQUIREMENTS_OUTPUT="$(php -r '
$document = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$requirements = $document["require"] ?? null;
if (!is_array($requirements) || $requirements === []) {
  fwrite(STDERR, "error: recipe composer.json has no require map.\n");
  exit(1);
}
ksort($requirements);
foreach ($requirements as $package => $constraint) {
  echo $package, ":", $constraint, PHP_EOL;
}
' "$PWD/recipes/geo_starter/composer.json")"
RECIPE_REQUIREMENTS=()
while IFS= read -r requirement; do
  [ -n "$requirement" ] && RECIPE_REQUIREMENTS+=("$requirement")
done <<< "$RECIPE_REQUIREMENTS_OUTPUT"
composer require --no-interaction --with-all-dependencies "${RECIPE_REQUIREMENTS[@]}"

DRUSH_PHP_ENTRY="$PWD/vendor/drush/drush/drush.php"
[ -f "$DRUSH_PHP_ENTRY" ] || { echo "error: drush not found at $DRUSH_PHP_ENTRY (did create-project succeed?)." >&2; exit 1; }

# The stock 128M CLI memory_limit OOMs while the recipe installs Canvas.
# Run drush through its PHP front controller so the raised limit actually
# applies — `php vendor/bin/drush` would print the shell proxy instead of
# executing drush:
drush_run() { php -d memory_limit="$PHP_MEMORY_LIMIT" "$DRUSH_PHP_ENTRY" "$@"; }

echo "==> Installing the site (database: ${DB_URL%%:*})"
# Generated secret, never echoed or stored — use the login link below.
if command -v openssl >/dev/null 2>&1; then
  ACCOUNT_PASS="$(openssl rand -base64 18)"
else
  ACCOUNT_PASS="$(head -c 18 /dev/urandom | base64)"
fi
drush_run site:install "$PWD/recipes/geo_starter" \
  --db-url="$DB_URL" --account-pass="$ACCOUNT_PASS" -y

echo "==> Running cron (populates /sitemap.xml — empty until first cron)"
drush_run cron

HANDOFF_TEMPLATE="$PWD/recipes/geo_starter/docs/INSTALLED_PROJECT_AGENT_HANDOFF.md"
if [ -f "$HANDOFF_TEMPLATE" ]; then
  if [ -e "$PWD/AGENTS.md" ]; then
    echo "==> Preserving existing AGENTS.md (installed-project handoff not copied)"
  else
    cp "$HANDOFF_TEMPLATE" "$PWD/AGENTS.md"
    echo "==> Added AGENTS.md installed-project handoff"
  fi
fi

echo
echo "Done. GEO Starter $TAG is installed in '$TARGET_DIR'."
echo
echo "  Front page:          /            (composed Canvas homepage)"
echo "  Sample Service page: /apply-emergency-food-and-utility-assistance"
echo "                       (view source for the application/ld+json block)"
echo "  Editorial dashboard: /admin/content/geo"
echo "  Sitemap:             /sitemap.xml"
echo
echo "One-time admin login link:"
drush_run uli
