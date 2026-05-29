# Technical Acceptance Plan

**Status:** Required before public release
**Last reviewed:** 2026-05-29

## Install And Package

- `composer validate --strict` passes.
- `git diff --check` passes.
- `composer.json` has `"type": "drupal-recipe"`.
- `recipe.yml` has `type: Site`.
- No dependency patches are required.
- No exact dependency pins are used.
- Required dependencies are stable and compatible with the target Drupal CMS release.
- Fresh Drupal CMS install/apply succeeds with Canvas, Paragraphs, and Entity Reference Revisions. Passed in a clean DDEV project on 2026-05-29; see `docs/VALIDATION.md`.

## Authoring

- Editors can create and edit a Canvas homepage.
- Editors can create and edit a Canvas migration landing page.
- Editors can create and edit one Canvas hub page.
- Editors can create and edit one Canvas campaign page.
- Editors can add, edit, reorder, and remove Paragraph sections on Service nodes.
- Editors can add, edit, reorder, and remove Paragraph sections on Answer nodes.
- Editors can add, edit, reorder, and remove Paragraph sections on Article nodes.
- Content moderation keeps publishing as a human action.

## Rendering

- Service, Answer, Article, and Evidence Source pages render without errors.
- Canvas pages render without errors.
- Paragraph sections render with the shared visual vocabulary.
- Sources, reviewed dates, direct answers, and next actions are visible in HTML.
- Desktop and mobile screenshots are captured for the core proof pages.
- `screenshot.webp` is replaced with a representative rendered site screenshot.

## Access And Security

- Anonymous users can access only published public content.
- Draft nodes are not exposed through JSON:API detail endpoints.
- Draft content is excluded from JSON:API collection endpoints.
- Paragraph content follows parent entity access.
- No secrets, tokens, credentials, private URLs, or unpublished data are present in committed files or rendered pages.
- Any future write-capable agent/context interface has a separate threat model.

## Accessibility

- Keyboard navigation works for header, cards, links, CTAs, and accordions.
- Heading hierarchy has one primary H1 per page.
- Links and buttons have meaningful accessible names.
- Color contrast meets WCAG 2.2 AA targets.
- Images have useful alt text or are marked decorative when appropriate.
- Form/editor workflows do not block keyboard users.

## Performance And Responsive

- Core pages are responsive at mobile, tablet, and desktop widths.
- Layout shifts are checked on pages with cards, media, and accordions.
- Cache behavior is reviewed after Canvas and Paragraph rendering.
- No oversized placeholder images ship in the final screenshot/sample content.

## Retrieval And Schema

- Rendered HTML exposes the sourceable content patterns.
- JSON:API access proof passed for the current node model on 2026-05-29. Rerun after Paragraph and Canvas content exists.
- Sitemap/search behavior is documented after rendered pages exist.
- JSON-LD is emitted only where visible page content supports it.
- FAQPage schema is disabled unless eligibility and visible content rules are satisfied.

## Review Gates

- Drupal implementation review.
- Drupal theme/render review.
- Accessibility review.
- QA/test review.
- Copy/proposal review.
- Security/access review before Marketplace submission.
