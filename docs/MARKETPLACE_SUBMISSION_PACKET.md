# Marketplace Submission Packet

**Status:** Not ready for submission
**Last reviewed:** 2026-07-21 (for `1.2.0`)

## Submission Position

GEO Starter `1.2.0` is published stable as a Drupal.org Community project. Its
exact public-tag/default-quickstart gate, release node, packaged archives,
release-history feed, and live project-page update are verified. Marketplace
readiness is a separate next track: the remaining gates are design/theme
proof, accessibility/performance/responsive evidence, a preview URL, and
named ownership/support commitments.

Do not submit until every blocker below is resolved.

## Applicant And Ownership

| Item | Status | Notes |
| --- | --- | --- |
| Applicant path | Open | Decide Zivtech/Drupal Certified Partner, individual Ripple Maker, or collaborator path. |
| Support owner | Open | Must name a real support owner before submission. |
| Maintenance owner | Open | Must name release and security maintenance owner. |
| Pricing model | Open | Decide free, paid, or free with commercial support. |
| Repository access | Partial | DrupalCode project repo exists at `https://git.drupalcode.org/project/geo_starter`; Marketplace review may require all repositories/materials. |

## Required Materials

| Material | Status | File/location |
| --- | --- | --- |
| Package repo | Ready | This repository and the published stable [1.2.0 release](https://www.drupal.org/project/geo_starter/releases/1.2.0). |
| Listing copy | Draft | `docs/PROJECT_PAGE_DRAFT.md` mirrors the verified live Community project page. A Marketplace adaptation still needs final proposal review. |
| Install docs | Ready | `docs/INSTALL.md` (verified released-artifact path). |
| Limitations | Ready | `docs/LIMITATIONS.md`. |
| Authoring model | Partial | `docs/AUTHORING_MODEL.md`; Canvas composed-page and section-library render proofs exist; manual editor UI/reorder proof remains. |
| Migration map | Draft | `docs/MIGRATION_MAP.md`; importer automation remains out of scope. |
| Schema/retrieval map | Ready | `docs/SCHEMA_MAP.md`; JSON-LD shipping and externally validated. The Rich Results Test is not an eligibility/display guarantee. |
| Validation evidence | Partial | `docs/VALIDATION.md`; install, JSON:API, sections, Canvas pages, JSON-LD, sitemap, and a11y spot-check proofs exist. Needs full WCAG/performance/responsive evidence. |
| Support policy | Draft placeholder | `SUPPORT.md`; needs named owner/SLA. |
| Security policy | Partial | `SECURITY.md`; Security Team advisory coverage granted for stable releases, including 1.2.0 (verified 2026-07-21); still needs a named maintainer contact path. |
| Changelog | Ready | `CHANGELOG.md`. |
| Screenshot | Partial | Representative `1.0.0` homepage `screenshot.webp` exists; final Marketplace imagery still needed. |
| Preview/demo URL | Partial | `https://joyus.ai` — production geo_starter install (Zivtech-owned), designated demo 2026-07-17. It runs real product content, not the recipe's sample content; decide before submission whether a disposable sample-content demo is also wanted. |
| Accessibility evidence | Blocked | Spot-check passed (see `docs/VALIDATION.md` WS-F); needs full WCAG 2.2 AA review evidence. |
| Performance evidence | Blocked | Needs rendered site performance proof. |
| Responsive evidence | Partial | Per-bundle desktop/mobile screenshots exist (WS-B); needs full responsive review. |
| Privacy/security attestations | Blocked | Needs final behavior and support owner. |
| Dependency list | Ready | `composer.json` and `docs/DEPENDENCIES.md` (verified 2026-07-21); re-verify immediately before submission. |
| License/content rights summary | Ready | `LICENSE.md` and `docs/CONTENT_LICENSES.md`; re-run the asset review if content changes. |

## Marketplace Quality Gates

- Built on Drupal CMS.
- Specific use case and coherent demo content.
- Canvas-compatible visual authoring proof.
- Near-feature-complete starting site.
- Stable required dependencies.
- No dependency patches.
- No exact dependency pins.
- Security review evidence.
- WCAG 2.2 AA accessibility evidence.
- Performance evidence.
- Responsive design evidence.
- Documentation, support, and maintenance commitments.
- Complete repositories and marketing materials.

## Blocking Work

1. Complete the theme/design-system proof (node field-stack rendering and a visual design pass beyond the semantic section templates).
2. Manual editor UI create/edit/reorder proof for Paragraph sections.
3. Replace the representative screenshot with final Marketplace imagery.
4. Confirm a durable preview/demo URL.
5. Run full accessibility, performance, responsive, security/access, and QA reviews.
6. Finalize applicant/support/pricing decisions.
7. Finalize listing copy with Zivtech writing-style and proposal/copy review.
8. Rerun the released-artifact install proof after any later dependency, recipe config, content, or package metadata change.

## Do Not Claim Yet

- Marketplace-ready.
- WCAG 2.2 AA complete.
- Production support commitments.
- Final privacy/security attestation.
- Guaranteed AI citations, rankings, or answer-engine placement.
- Turnkey source-CMS migration automation.
