#!/usr/bin/env bash
#
# GEO Starter — one-command quickstart
# ------------------------------------
# Stands up a working, opinionated Drupal CMS site with the GEO Starter recipe
# applied: structured content model (Service / Answer / Article / Evidence
# Source), sample content, schema.org JSON-LD, JSON:API, and an XML sitemap.
#
# This is the non-DDEV fallback. If DDEV is installed, prefer:
#     ddev start && ddev geo-install
#
# Usage (run from a clone of this recipe repo):
#     ./scripts/quickstart.sh [TARGET_DIR]
#
# TARGET_DIR defaults to ./.site . The recipe repo itself is a `drupal-recipe`
# package, not an installable project, so we create a fresh drupal/cms project
# in TARGET_DIR and wire this repo in as a Composer path repository.
#
# Requirements: php 8.3+, composer 2, a database reachable by Drush, and
# `openssl`. The "one minute" target depends on Composer/network cache; first
# run is slower. NOTE: this script must be verified on a live stack — this
# repo ships no live Drupal (see docs/DEMO_RUNBOOK.md).
#
# Idempotent: re-running reuses an existing TARGET_DIR project.

set -euo pipefail

RECIPE_SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
RECIPE_NAME="geo_starter"
TARGET_DIR="${1:-${RECIPE_SRC}/.site}"

log() { printf '▶ %s\n' "$*"; }

require_bin() {
  command -v "$1" >/dev/null 2>&1 || { echo "✗ Missing required tool: $1"; exit 1; }
}

require_bin php
require_bin composer
require_bin openssl

log "[1/5] Creating a fresh Drupal CMS project in ${TARGET_DIR} ..."
if [ ! -f "${TARGET_DIR}/web/index.php" ] && [ ! -f "${TARGET_DIR}/index.php" ]; then
  composer create-project drupal/cms "${TARGET_DIR}" --no-interaction
fi
cd "${TARGET_DIR}"

DRUSH="vendor/bin/drush"
[ -x "${DRUSH}" ] || DRUSH="drush"

log "[2/5] Wiring the local recipe as a Composer path repository ..."
composer config repositories.geo_starter path "${RECIPE_SRC}" --no-interaction
composer require "drupal/${RECIPE_NAME}:@dev" --no-interaction

log "[3/5] Installing Drupal and applying the GEO Starter recipe ..."
RECIPE_PATH="$(find . -maxdepth 4 -name recipe.yml -path "*${RECIPE_NAME}*" 2>/dev/null | head -1 | xargs -r dirname)"
if [ -z "${RECIPE_PATH}" ]; then echo "✗ Could not locate the applied recipe.yml"; exit 1; fi

# Prefer single-shot recipe install; fall back to install + recipe:apply.
if ! "${DRUSH}" site:install "${RECIPE_PATH}" --account-pass="$(openssl rand -base64 18)" -y; then
  echo "  site:install <recipe> unsupported — falling back to recipe:apply"
  "${DRUSH}" site:install -y --account-pass="$(openssl rand -base64 18)"
  "${DRUSH}" recipe "${RECIPE_PATH}" -y
fi

log "[4/5] Generating the XML sitemap (recipes are config-only and cannot) ..."
"${DRUSH}" simple-sitemap:generate || true

log "[5/5] Done."
LOGIN_URL="$("${DRUSH}" user:login 2>/dev/null || true)"
echo ""
echo "✅ GEO Starter site is ready in ${TARGET_DIR}"
echo "   • Admin login: ${LOGIN_URL}"
echo "   • Serve it:    cd ${TARGET_DIR} && ${DRUSH} runserver"
echo ""
echo "GEO_STARTER_READY url=${LOGIN_URL}"
