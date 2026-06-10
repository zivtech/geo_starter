# Publishing And Acceptance Plan

**Status:** Track 1 (Community project) is complete through stable `1.0.0`
(2026-06-08); Tracks 2 and 4 (Marketplace readiness and submission packet)
remain open. Kept as the working plan of record.
**Last reviewed:** 2026-06-09

This project should pursue all four tracks together:

1. Community Drupal.org project readiness.
2. Official Marketplace acceptance readiness.
3. Technical install, authoring, rendering, and validation gates.
4. Submission packet and support materials.

## Source Requirements

- Community templates can be published as general Drupal.org projects without formal review, while Marketplace templates are curated and reviewed.
- Marketplace templates must be built on Drupal CMS and follow best practices for security, accessibility, performance, and code quality.
- Marketplace templates need clear documentation, maintenance commitments, and support expectations.
- Creator eligibility currently depends on Drupal Certified Partner status for organizations or Ripple Maker status for individuals/small creators.
- Site templates cannot include non-stable required releases or patches.
- Site templates are "Big Recipes": `composer.json` type `drupal-recipe`, `recipe.yml` type `Site`, no install-profile dependency, no patches, no update paths, and no pinned dependency versions.

References:

- https://www.drupal.org/about/initiatives/cms/blog/differentiating-marketplace-site-templates-and-community-site-templates
- https://new.drupal.org/site-template/application
- https://git.drupalcode.org/project/drupal_cms/-/wikis/Architecture-Decision-Records/Site-Templates
- https://www.drupal.org/docs/develop/managing-a-drupalorg-theme-module-or-distribution-project/documenting-your-project/project-page-template

## Track 1: Community Drupal.org Project

Goal: publish a clear Community project that is honest about its limits.
**Done** — published through alphas, betas, and stable `1.0.0`
(`docs/RELEASE_CHECKLIST.md` records the gates). Kept as the recurring bar
for each release:

- Push current repository to DrupalCode at `https://git.drupalcode.org/project/geo_starter`.
- Confirm the Drupal.org general project page at `https://www.drupal.org/project/geo_starter`.
- Use `docs/PROJECT_PAGE_DRAFT.md` for the project-page description.
- Include a representative `screenshot.webp`.
- Tag the release and publish release notes from the paste-ready doc.
- Link install docs, validation docs, limitations, support, and changelog.
- Keep "Not Marketplace-ready" visible until the Marketplace gates pass.
- State the security-advisory position accurately: the project is not
  Security-Team-covered until the post-1.0 opt-in application is granted.

Acceptance bar:

- A Drupal site builder can understand what the starter does, what it does not do, and how to try it.
- The project page summary is under 200 characters.
- The release has no hidden importer, AI, or rich-result guarantee claims.

## Track 2: Official Marketplace Readiness

Goal: prepare a future curated Marketplace submission without letting Marketplace claims leak into public copy.

Required before submission:

- Decide applicant path: Zivtech as Drupal Certified Partner, individual Ripple Maker path, or collaborator.
- Name the support owner and support model.
- Provide access to all repositories and marketing materials required for review.
- Confirm all required dependencies are stable and no patches are required.
- Finish the theme/design-system proof and Canvas-compatible visual pages.
- Finish Paragraphs authoring proof on structured nodes.
- Provide representative default content and image/license documentation.
- Produce accessibility, performance, responsive, privacy, and security evidence.
- Provide preview URL, screenshots, listing copy, dependency list, support link, and FAQ.

Acceptance bar:

- The template feels like a near-feature-complete Drupal CMS starting point, not a recipe-only scaffold.
- Reviewers can inspect support, ownership, quality evidence, and demo behavior without asking for missing basics.

## Track 3: Technical Proof Gates

Goal: make the template work before copy asks people to trust it.

Blocking gates (status as of `1.0.0` — evidence in `docs/VALIDATION.md`):

1. Fresh Drupal CMS install/apply proof after the Canvas and Paragraphs dependency expansion. **Done** (released-artifact proofs, 2026-06-07/08).
2. Canvas pages for homepage, migration landing page, hub, and campaign page. **Done** (C-01..C-04).
3. Paragraph bundles and `field_sections` on Service, Answer, and Article nodes. **Done** (ten-bundle library).
4. Rendered page proof across desktop and mobile. **Done for the section bundles** (WS-B per-bundle screenshots); full responsive review remains for Marketplace.
5. Keyboard accessibility and color contrast checks. **Spot-check done** (WS-F); full WCAG gate remains for Marketplace.
6. JSON:API access checks after Paragraph content exists. **Done** (re-proof 2026-06-07).
7. Sitemap proof after rendered pages exist. **Done** (WS-F; internal search deliberately not shipped).
8. JSON-LD only where rendered visible content supports it. **Done** (parity probe + external validator).
9. Screenshot kept representative of the current rendered site. **Done for `1.0.0`**; replace with final imagery before Marketplace submission.

Acceptance bar:

- Installers, editors, anonymous users, and reviewers can all exercise the expected behavior without special local knowledge.

## Track 4: Submission Packet

Goal: assemble the material reviewers and users need.

Packet contents:

- `README.md`
- `docs/INSTALL.md`
- `docs/LIMITATIONS.md`
- `docs/VALIDATION.md`
- `docs/TECHNICAL_ACCEPTANCE_PLAN.md`
- `docs/PROJECT_PAGE_DRAFT.md`
- `docs/MARKETPLACE_SUBMISSION_PACKET.md`
- `SUPPORT.md`
- `SECURITY.md`
- `CHANGELOG.md`
- `docs/DEPENDENCIES.md`
- `docs/CONTENT_LICENSES.md`
- `docs/AUTHORING_MODEL.md`
- `docs/MIGRATION_MAP.md`
- `docs/SCHEMA_MAP.md`
- final `screenshot.webp`
- preview/demo URL
- dependency and license summary
- accessibility/performance/security evidence
- release notes

Acceptance bar:

- The packet can be handed to a Drupal.org project reviewer, Marketplace reviewer, or internal release approver without needing a separate oral history of the project.
