# geo-install redesign — live-run findings and draft fix (2026-07-15)

Internal working note (export-ignored). Public record: issue **#3611199**
(descope announcement inside the 1.0.x fresh-install known-issue note).
Status of the shipped command: **experimental, known-broken**, warns on
invocation and is documented as such (AGENT_GUIDE, LIMITATIONS, CHANGELOG,
1.1.0 release notes).

## The five live-run findings (in discovery order)

Each was found by actually running `ddev geo-install` on a clean clone —
the command had never been executed end-to-end before 2026-07-15.

1. **Composer partial-update conflict.** A fresh `drupal/cms` lock pins
   transitive packages (observed: `drupal/canvas 1.8.0`) outside the
   recipe's bounds (`>=1.4 <1.6`). `composer require drupal/geo_starter:@dev`
   is a partial update and refuses the downgrade.
   Fix: `--with-all-dependencies` on the require. **Verified working.**
2. **`drush` not on PATH.** The cms project lands in `web/`, so drush lives
   at `web/vendor/bin/drush`; the container PATH has neither.
   Fix: `export PATH="$PWD/vendor/bin:$PATH"` after `cd web`.
   **Verified working.**
3. **Recipe placement.** The recipe-unpack composer plugin merges a
   path-repo recipe's dependencies and then REMOVES the package — nothing
   is materialized under `recipes/`. And core's `RecipeConfigurator`
   resolves `recipes:` entries relative to the recipe's PARENT directory,
   so the tree must sit inside the project's `recipes/` next to the
   unpacked `drupal_cms_*` recipes (pointing site:install at the repo root
   fails with "Can not find the drupal_cms_admin_ui recipe").
   Fix: copy the repo tree (minus `web/`, `.git/`, `.ddev/`) into
   `web/recipes/geo_starter` before install. **Verified diagnosis (host
   repro); container fix draft below.**
4. **No `--db-url`.** Without it drush falls into interactive DB prompting
   inside a non-TTY container and dies
   (`getInstallTasks() on null` in `SiteInstallCommands::validate()`s
   catch-path). DDEV's generated settings can't be relied on either (see 5).
   Fix: pass `--db-url=mysql://db:db@db/db` explicitly.
5. **Docroot mismatch.** `.ddev/config.yaml` says `docroot: web`, but the
   built site's real docroot is `web/web` (cms project nested in `web/`).
   nginx serves the wrong directory (403 on `/`) and DDEV writes its
   settings include where Drupal never reads it.
   Fix candidate: `docroot: web/web` — BUT the one attempt with this change
   hit a new create-project/`mv` failure (partial `web/web/core` state), so
   treat the whole nested-`mv` design as the suspect, not just the docroot
   value.

## Draft corrected command

A draft incorporating fixes 1–4 (and the recipe-copy approach for 3) was
validated piecemeal but never green end-to-end. Key steps:

```bash
cd web
export PATH="$PWD/vendor/bin:$PATH"
composer config repositories.geo_starter path "${RECIPE_SRC}" --no-interaction
composer require "drupal/${RECIPE_NAME}:@dev" --no-interaction --with-all-dependencies
rm -rf "recipes/${RECIPE_NAME}"; mkdir -p "recipes/${RECIPE_NAME}"
tar -C "${RECIPE_SRC}" --exclude=./web --exclude=./.git --exclude=./.ddev -cf - . \
  | tar -xf - -C "recipes/${RECIPE_NAME}"
drush site:install "$PWD/recipes/${RECIPE_NAME}" --db-url="mysql://db:db@db/db" \
  --account-pass="$(openssl rand -base64 18)" -y
```

## Redesign direction (recommendation)

The `composer create-project → mv into web/` nesting is the structural
problem: it produces a project-inside-the-recipe layout that DDEV's
docroot, drush's discovery, and composer's autoload scanning all fight.
Two candidate shapes for the redesign:

- **Sibling dir, honest docroot:** create-project into `app/` (no `mv`),
  set `docroot: app/web`, recipe copied to `app/recipes/geo_starter`.
  Minimal delta from today's design; removes the fragile `mv` and the
  `web/web` weirdness.
- **quickstart-in-container:** make geo-install a thin wrapper that runs
  `tools/quickstart.sh` (the verified path) inside the container with a
  DDEV `DB_URL`. One code path to maintain; the quickstart already
  encodes fixes 1/2/4 conceptually.

Gate for whichever lands: clean clone → `ddev start && ddev geo-install`
→ `GEO_STARTER_READY url=…` → `/` and a Service page render 200 — run it
in CI-like conditions before un-marking experimental.
