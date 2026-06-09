# Technical Acceptance Plan

**Status:** Install, rendering, JSON-LD, sitemap, and access proofs passed for
`1.0.0`; manual editor-UI evidence and the full accessibility/performance
gates remain open (they block Marketplace, not the Community stable).
**Last reviewed:** 2026-06-09 (for `1.0.0`)

Evidence for every "passed" line below is recorded in `docs/VALIDATION.md`.

## Install And Package

- `composer validate --strict` passes.
- `git diff --check` passes.
- `composer.json` has `"type": "drupal-recipe"`.
- `recipe.yml` has `type: Site`.
- No dependency patches are required.
- No exact dependency pins are used (the bounded Canvas/Mercury ranges are
  documented constraints, not pins).
- Required dependencies are stable and resolve at the default `stable`
  Composer floor (verified 2026-06-08).
- Fresh install/apply succeeds from packaged artifacts — released-artifact
  proofs passed 2026-06-07 (beta2 tree) and 2026-06-08 (stable package shape,
  real drupal.org module artifact). Content imports acyclically
  (`tools/content-graph-lint.py` green).

## Authoring

Proven: all ten section bundles install with their fields; `field_sections`
attaches to Service, Answer, and Article; runtime Paragraph save/render
passes; the four component-composed Canvas pages import and render.
Still open: manual editor UI create/edit/reorder browser evidence.

- Editors can create and edit a Canvas homepage.
- Editors can create and edit a Canvas migration landing page.
- Editors can create and edit one Canvas hub page.
- Editors can create and edit one Canvas campaign page.
- Editors can add, edit, reorder, and remove Paragraph sections on Service nodes.
- Editors can add, edit, reorder, and remove Paragraph sections on Answer nodes.
- Editors can add, edit, reorder, and remove Paragraph sections on Article nodes.
- Content moderation keeps publishing as a human action.

## Rendering

- Service, Answer, Article, and Evidence Source pages render without errors. Passed.
- All four component-composed Canvas pages and the front-page shell render. Passed 2026-06-07/08 (Canvas 1.5.0/1.5.1 + Mercury 1.0.5).
- Section bundles render through the semantic templates — WS-B pass, 21/21
  markup/label/scoping assertions. Passed 2026-06-07.
- Sources, reviewed dates, direct answers, and next actions are visible in HTML. Passed.
- Desktop (1280px) and mobile (375px) screenshots captured per section bundle (WS-B). Passed.
- `screenshot.webp` is a representative rendered-site screenshot (composed homepage). Passed.

## Access And Security

- Anonymous users can access only published public content. Passed (re-proof 2026-06-07).
- Draft nodes are not exposed through JSON:API detail endpoints. Passed.
- Draft content is excluded from JSON:API collection endpoints. Passed.
- Paragraph content follows parent entity access. Passed.
- No secrets, tokens, credentials, private URLs, or unpublished data are present in committed files or rendered pages.
- `composer audit` reports no advisories across the resolved tree. Passed 2026-06-08.
- Any future write-capable agent/context interface has a separate threat model.

## Accessibility

Spot-check passed 2026-06-07 (WS-F: keyboard walk with skip-link, visible
focus, no traps; WCAG AA contrast on all WS-B CSS color pairs — homepage and
Service page). The full WCAG 2.2 AA release gate remains open:

- Keyboard navigation works for header, cards, links, CTAs, and accordions.
- Heading hierarchy has one primary H1 per page.
- Links and buttons have meaningful accessible names.
- Color contrast meets WCAG 2.2 AA targets.
- Images have useful alt text or are marked decorative when appropriate.
- Form/editor workflows do not block keyboard users.

## Performance And Responsive

Open — not yet release-gated:

- Core pages are responsive at mobile, tablet, and desktop widths (per-bundle WS-B screenshots exist; full-page review remains).
- Layout shifts are checked on pages with cards, media, and accordions.
- Cache behavior is reviewed after Canvas and Paragraph rendering.
- No oversized placeholder images ship in the final screenshot/sample content.

## Retrieval And Schema

- Rendered HTML exposes the sourceable content patterns. Passed.
- JSON:API access proof passed after sections and Canvas content exist (re-proof 2026-06-07, nodes + Canvas pages + paragraphs).
- `/sitemap.xml` indexes the canonical types + Canvas pages, drafts excluded; populates on first cron. Passed 2026-06-07. Internal search deliberately not shipped.
- JSON-LD is emitted only where visible page content supports it — parity probe 23/23; hosted schema.org validator 0 errors/0 warnings on all four node types. Google Rich-Results re-confirmation pending.
- FAQPage schema stays gated unless eligibility and visible content rules are satisfied. Passed (gating verified by the module's test suite).

## Review Gates

- Drupal implementation review.
- Drupal theme/render review.
- Accessibility review (full gate; spot-check done).
- QA/test review.
- Copy/proposal review.
- Security/access review before Marketplace submission.
