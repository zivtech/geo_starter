# GEO Starter 1.0.0 Release Notes

First stable release of `drupal/geo_starter`.

A site at the default (`stable`) Composer minimum-stability floor can now
require this recipe without any stability override. The companion module
`drupal/geo_starter_jsonld` is published stable at `1.0.0`; the recipe
constraint is plain `^1.0`.

This is a stabilization release on top of `1.0.0-beta2`. No content-model
change; no new features. The recipe is not Marketplace-ready (see "Current
limits" below).

## What changed in 1.0.0

- **Default-stability installs now work.** `drupal/geo_starter_jsonld 1.0.0`
  is published stable on drupal.org. The recipe's `^1.0` constraint resolves
  it without `minimum-stability: beta`. Proven by a released-artifact install
  proof on 2026-06-08: fresh `composer create-project drupal/cms`, default
  stability, real d.o module package, JSON-LD probe 23/23, content-graph-lint
  OK, all pages render.
- **Canvas/Mercury minor range bounded.** `drupal/canvas >=1.4 <1.6` and
  `drupal/mercury >=1.0.5 <1.1` cap the minors the recipe accepts, guarding
  against a future minor that changes component prop schemas and silently
  invalidates the shipped Canvas trees and `canvas.component.*` configs.
- **1.x stability contract in effect.** Fresh install is the only supported
  path. Within 1.x the content model, `@id` scheme, and entity-type set are
  frozen (additive-only); breaking changes force `2.0.0`.

## What this release includes

Everything from `1.0.0-beta2`:

- **Content model** — Service, Answer, Article, and Evidence Source node types
  with a shared Topic/Audience taxonomy, Draft → Needs review → Published →
  Archived moderation workflow, and reviewed-date provenance fields.
- **Ten-bundle section library** — `geo_starter_section`, `section_faq` (+
  `_item`), `section_step_list` (+ `_item`), `section_card_grid`,
  `section_contact_panel` (structured `office_hours`), `section_cta`,
  `section_alert`, `section_media_text` — attached to Service, Answer, and
  Article nodes via `field_sections`.
- **Semantic section rendering** — the `geo_starter_jsonld_markup` submodule
  (inside the `drupal/geo_starter_jsonld` package) provides semantic HTML
  templates for all ten section bundles: h2/h3 heading hierarchy, open `<dl>`
  FAQ, `<ol>` steps, `<address>` contact, accented CTA/alert callouts, card
  grid, and media/text two-column layout. Styled by Mercury design tokens with
  hard fallbacks.
- **Schema.org JSON-LD** — `drupal/geo_starter_jsonld` emits one parity-correct
  `@graph` per full canonical published page: `Service` (with provider
  `ContactPoint`, `PostalAddress`, `hoursAvailable`), `Question`/`Answer`,
  `Article`, `CreativeWork` evidence records with resolvable cross-page
  `citation` `@id`s, and gated `FAQPage`, `HowTo`, and `ItemList` from section
  content. Covered by PHPUnit Unit/Kernel/Functional suites in Drupal.org CI
  and a hosted schema.org validator pass (zero errors/warnings, all four node
  types).
- **Four component-composed Canvas pages** — homepage, migration landing, topic
  hub, and campaign — built from stock Mercury SDCs. Ships `canvas.component.*`
  config for every component the trees use.
- **Editorial dashboard** — `/admin/content/geo` View (gated on
  `access content overview`) for the governed content types.
- **XML sitemap** — `simple_sitemap` indexing the four canonical node types +
  Canvas pages; populates on first cron; unpublished excluded.
- **Sample content** — 4 Services, 8 Answers, 3 Articles, 6 Evidence Sources,
  seeded taxonomy terms, and worked FAQ, step-list, card-grid, and
  contact-panel sections.

## Install

Fresh install only. Site templates are not served by the packages.drupal.org
Composer facade, so the recipe tree is placed from its release tag rather
than `composer require`d:

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
git clone --branch 1.0.0 https://git.drupalcode.org/project/geo_starter.git recipes/geo_starter
rm -rf recipes/geo_starter/.git
```

Then either start the Drupal CMS installer in the browser and select the
GEO Starter site template, or install from the command line:

```bash
drush site:install "$(pwd)/recipes/geo_starter" \
  --account-pass="$(openssl rand -base64 18)" -y
drush cron   # populates /sitemap.xml (empty until the first cron run)
```

See `docs/INSTALL.md` in the repository for full instructions.

## Current limits

- **Not Marketplace-ready.** See `docs/LIMITATIONS.md` for the full blocker
  list (WCAG 2.2 AA audit, performance/responsive evidence, demo URL, named
  support owner, final marketing materials).
- **Google Rich-Results pending.** The hosted schema.org validator passes (zero
  errors/warnings). A URL-mode Google Rich-Results run is still pending;
  no rich-result eligibility is claimed.
- **Lightly-styled, not a design system.** The section bundles have semantic
  templates; the node field-stack above them renders through core's classless
  field template. A GEO-specific design system is not included.
- **Fresh install only.** No `hook_update_N` migration path. Pre-1.0 installs
  are not upgrade-compatible with 1.0.0.
- **Not Security Team covered.** As a Community project, `geo_starter` has not
  applied for Drupal Security Team opt-in coverage. Stable 1.0 is the
  prerequisite to apply — that follow-up is tracked separately.
- **Canvas/Mercury capped.** The recipe accepts `drupal/canvas >=1.4 <1.6` and
  `drupal/mercury >=1.0.5 <1.1`. Raising either cap requires re-exporting the
  shipped `canvas.component.*` configs and re-validating the Canvas page trees.

## No guaranteed outcomes

AI citation, ranking, rich results, and answer-engine placement are never
guaranteed. This recipe builds the structural foundation that helps; it does
not promise outcomes.
