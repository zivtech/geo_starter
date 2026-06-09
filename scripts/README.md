# scripts/

End-user / agent **scaffolding** helpers. One command to a running, opinionated
Drupal CMS site with the GEO Starter recipe applied.

| Script | Purpose |
| --- | --- |
| `quickstart.sh` | Non-DDEV one-command install. Creates a fresh `drupal/cms` project, wires this recipe in as a Composer path repository, installs Drupal, applies the recipe, generates the sitemap, and prints a one-time admin login link. |

If you have [DDEV](https://ddev.com) (recommended), use the DDEV-native command
instead — see the repo root `.ddev/commands/web/geo-install`:

```bash
ddev start && ddev geo-install
```

## scripts/ vs tools/ — keep the wall clear

- **`scripts/`** is for *standing up a new site* (fresh-install scaffolding).
  Safe to run on a clean machine.
- **`tools/`** is **development-only** and must **not** run on a fresh install.
  `tools/create-alpha-sample-content.php` deletes and re-creates demo nodes,
  which collides with the content the recipe already imports. See
  `docs/INSTALL.md` and `docs/VALIDATION.md` → "Local Helper Scripts."

## Verification status

These scripts orchestrate live tooling (Composer, Drush, a database) and have
**not** been executed in this repository's CI sandbox (no live Drupal here).
Treat the "one command, one minute" claim as a target to confirm on a real
stack — the live verification checklist lives in `docs/DEMO_RUNBOOK.md`.
