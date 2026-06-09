# GEO Starter

GEO Starter is a Drupal CMS site-template scaffold for teams moving from headless/composable or legacy page/post CMS stacks into a governed foundation for Generative Engine Optimization (GEO). It provides sourceable services, answers, articles, and evidence records designed to be readable by humans and inspectable by retrieval systems.

This scaffold is intentionally lean. The Starter Kit/DDEV spike proved that `drush site:export` can generate a valid site template after a Drupal CMS site is installed, but raw export output from the default non-interactive Starter template was too broad for the Community alpha. This package starts from the verified `type: Site` and `drupal-recipe` shape instead.

## Status

Stable (`1.0.0`). Not Marketplace-ready.

## Stability contract

From `1.0.0`, the 1.x line promises discipline, not in-place upgrades:

1. **Fresh install is the only supported path.** Recipes are apply-once
   configuration artifacts; no `hook_update_N` migration ships. This is the
   Drupal CMS site-template posture, not a project deficiency.
2. **Content-model freeze, additive-only.** Within 1.x: no field deletions,
   no field-type or storage changes, no vocabulary restructuring, no changes
   to the `@id` scheme or entity-type set. New optional fields and bundles
   may be added. Breaking changes force `2.0.0`.
3. **Breaking-change documentation duty.** If rule 2 must be broken (security
   or data-integrity only), the release notes carry an explicit manual
   migration note — as the `1.0.0-alpha4` `office_hours` change did.
4. **Sample content is exempt** — demo content in `content/` may change
   freely within 1.x.

A `stable`-floor Composer project (default `minimum-stability`) can require
this recipe without any stability override. The companion module
`drupal/geo_starter_jsonld` carries its own parallel contract (see its README).

## Current Scope

- Drupal CMS recipe package shape.
- Core modules needed for content modeling, moderation, JSON:API retrieval proof, media, taxonomy, paths, and views.
- Drupal CMS admin, media, privacy, and basic SEO recipes.
- Required package dependencies for Canvas and Paragraphs authoring lanes.
- Schema.org JSON-LD emission via the required companion module `drupal/geo_starter_jsonld` (its own composer package — a recipe cannot bundle a module): `Service` with provider `ContactPoint`/`PostalAddress`/`hoursAvailable`, `Question`/`Answer`, `Article`, `CreativeWork` evidence records with resolvable cross-page `citation` `@id`s, and gated `FAQPage`, `HowTo`, and `ItemList` from section content. Full canonical published pages only; never beyond what the visible page renders.
- A ten-bundle section library attached to Service, Answer, and Article nodes via `field_sections`: `geo_starter_section`, `section_faq`(+`_item`), `section_step_list`(+`_item`), `section_card_grid`, `section_contact_panel` (structured `office_hours`), `section_cta`, `section_alert`, `section_media_text`.
- Four component-composed Canvas sample pages (homepage, migration landing, topic hub, campaign) plus the front-page Canvas shell, all built from stock Mercury SDCs.
- Mercury as the public frontend theme, with `geo_starter_jsonld_markup` semantic templates for the ten section bundles.
- MVP content model config for Service, Answer, Article, Evidence Source, Audience and Topic.
- Public-service sample content: 4 Services, 8 Answers, 3 Articles, 6 Evidence Sources, seeded taxonomy terms, and worked FAQ, step-list, card-grid, and contact-panel sections that exercise the JSON-LD path on install.
- Planning docs frame the starter as a migration destination for headless/composable and page/post CMS patterns; importer automation is not included.
- Representative `screenshot.webp` captured from the composed homepage on a fresh install.

## GEO Readiness

This section shows what the 1.x stable line supports now, what is partial, and what remains open. It focuses on capabilities a content platform needs when search engines and AI systems decide what to cite.

**Content structure and operations**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Structured content model | Yes | Typed Service, Answer, Article, and Evidence Source entities with shared fields and controlled Topic and Audience vocabularies. |
| Composable content operations | Yes | Typed content, taxonomy, entity references, moderation, JSON:API, the ten-bundle section library, four component-composed Canvas pages, and an editorial dashboard View at `/admin/content/geo`. |
| Editorial governance | Yes | Draft → needs-review → published → archived moderation workflow across all four content types, with reviewed-date provenance. |

**Retrieval and rendering output**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Rendered semantic HTML | Partial | Section bundles render through `geo_starter_jsonld_markup` semantic templates (h2/h3 hierarchy, `<dl>` FAQ, `<ol>` steps, `<address>` contact, accented CTA/alert, card grid, media/text) on top of Mercury's stock styling. The node field-stack above the sections still renders through core's classless field template — a grouped provenance footer and visual de-emphasis are documented future work. A GEO-specific design system is not included. |
| Schema.org / metadata from fields | Yes, with a validation boundary | `drupal/geo_starter_jsonld` emits one parity-correct schema.org `@graph` per full canonical published page, from the same fields the page renders. Covered by PHPUnit suites in Drupal.org CI, a full-surface acceptance probe (23/23 on a fresh install), an offline schema.org domain-correctness check, and a hosted schema.org validator pass (zero errors/warnings, all four node types). Google Rich-Results findings are still pending (URL-mode run required), so no rich-result eligibility is claimed. |
| Structured API access | Yes | JSON:API (core) with published/draft access proven on a fresh install. This is a machine-readable integration surface — not, by itself, the channel through which answer engines form citations (those read rendered pages and the public web). |

**Agent and ownership readiness**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Agent / tool protocol story | Planned | Drupal AI and agent paths are documented as future integration surfaces; no write-capable agent workflow ships. |
| AI provider choice | Open | The recipe configures no AI provider; provider choice stays an open Drupal AI integration decision, with no proprietary runtime. |
| Open ownership / no lock-in | Yes | Distributed as an open Drupal recipe with no proprietary runtime dependency. |

**Migration readiness**

| Capability | Status | What proves it today |
| --- | --- | --- |
| Migration destination for headless/composable and page/post stacks | Partial | A destination content model and migration map exist; turnkey importer automation is not included. |

**Scope and honest limits**

- AI citation, ranking, rich results, and answer-engine placement are never guaranteed. This recipe builds the structural foundation that helps; it does not promise outcomes.
- AI engines do not behave identically, so "AI visibility" is not a single switch. GEO Starter strengthens the rendered-HTML, structured-content, and cited-source path that documentation- and answer-citing engines favor. It does not, by itself, address community/forum or homepage-led citation patterns. Identify the engines your audience actually uses and measure per engine.

## Provenance

This repository contains the starter package only. It does not vendor the externally hosted Drupal modules that were present in the broader research workspace. See `docs/PROVENANCE.md`.

## Install

Fresh install only. Site templates are not served by the packages.drupal.org
Composer facade, so the recipe tree is placed from its release tag.

**Quick start** — the wrapper script runs the verified steps below as one
command (SQLite by default, for a local trial):

```bash
git clone --branch 1.0.0 https://git.drupalcode.org/project/geo_starter.git
./geo_starter/tools/quickstart.sh my-site 1.0.0
```

**Manual path** (what the script does):

```bash
composer create-project drupal/cms my-site
cd my-site

# Require the recipe's dependencies at the project root (the Drupal CMS
# installer adds these at install time; the manual path must require them):
composer require 'drupal/geo_starter_jsonld:^1.0' \
  'drupal/canvas:>=1.4 <1.6' 'drupal/mercury:>=1.0.5 <1.1' \
  'drupal/paragraphs:^1.20' 'drupal/entity_reference_revisions:^1.14' \
  'drupal/office_hours:^1.29' 'drupal/simple_sitemap:^4.2' \
  'drupal/drupal_cms_admin_ui:^2' 'drupal/drupal_cms_media:^2' \
  'drupal/drupal_cms_privacy_basic:^2' 'drupal/drupal_cms_seo_basic:^2'

# Place the recipe at the release tag:
git clone --branch 1.0.0 https://git.drupalcode.org/project/geo_starter.git recipes/geo_starter
rm -rf recipes/geo_starter/.git

# Install (or use the Drupal CMS installer and select GEO Starter):
drush site:install "$(pwd)/recipes/geo_starter" \
  --account-pass="$(openssl rand -base64 18)" -y
drush cron   # populates /sitemap.xml
```

Full instructions, post-install checks, and what a successful install looks
like: `docs/INSTALL.md`.

## Install And Release Readiness

- `docs/INSTALL.md`
- `docs/LIMITATIONS.md`
- `docs/PUBLISHING_AND_ACCEPTANCE_PLAN.md`
- `docs/TECHNICAL_ACCEPTANCE_PLAN.md`
- `docs/PROJECT_PAGE_DRAFT.md`
- `docs/MARKETPLACE_SUBMISSION_PACKET.md`
- `docs/RELEASE_CHECKLIST.md`
- `docs/DEPENDENCIES.md`
- `docs/CONTENT_LICENSES.md`
- `docs/AUTHORING_MODEL.md`
- `docs/MIGRATION_MAP.md`
- `docs/SCHEMA_MAP.md`

## Validation

See `docs/VALIDATION.md` for the current smoke-test evidence and the helper scripts used to reproduce the sample-content and JSON:API checks.

## Not In This Scaffold Yet

- GEO-specific theme/design-system implementation. The section bundles have
  lightly-styled semantic rendering via `geo_starter_jsonld_markup`; the node
  field-stack above them and a full visual design system are not included.
- Google Rich-Results check on the emitted JSON-LD (schema.org hosted validator
  passed; URL-mode rich-results run is still pending — required before any
  rich-result eligibility claim).
- Turnkey source-CMS import automation.
- Marketplace submission metadata, final support commitments, or preview URL.
- Required AI provider, agent, or credential setup.
- An `llms.txt`/agent manifest on the installed site — a recipe cannot ship
  docroot files; tracked as a candidate `drupal/geo_starter_jsonld` feature.
