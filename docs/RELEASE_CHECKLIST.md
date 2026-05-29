# Release Checklist

## Before Any Public Alpha Release

Latest clean install evidence is recorded in `docs/VALIDATION.md`. Rerun the install/apply proof if dependencies, recipe config, or imported content change before tagging.

- [x] Confirm Drupal.org project exists: `https://www.drupal.org/project/geo_starter`.
- [x] Confirm DrupalCode repository exists: `https://git.drupalcode.org/project/geo_starter`.
- [x] Confirm `composer validate --strict` passes.
- [x] Confirm `git diff --check` passes.
- [x] Confirm no secrets or credentials are present.
- [x] Rerun clean Drupal CMS install/apply after dependency, recipe config, content, or package-name changes. Last passed on 2026-05-29 for `1.0.0-alpha1` readiness after the GEO rename, Olivero public theme addition, Paragraph proof, and Canvas shell additions.
- [x] Replace placeholder `screenshot.webp` with a representative alpha screenshot.
- [x] Confirm project page copy does not claim Marketplace readiness.
- [x] Confirm project page copy does not claim guaranteed AI placement.
- [x] Confirm limitations are linked from README/project page.
- [x] Confirm support and security policies are present.
- [x] Confirm dependency summary is current.
- [x] Confirm content and asset license summary is current.
- [x] Push current `main` to DrupalCode.
- [x] Tag `1.0.0-alpha1`.
- [x] Push the alpha tag to DrupalCode.
- [x] Tag `1.0.0-alpha2` (corrected taxonomy + repaired depends graph; supersedes the non-installing alpha1) and push to both remotes.
- [ ] Create the `1.0.0-alpha2` release node on Drupal.org from the tag. Paste-ready source is in `docs/DRUPAL_ORG_RELEASE_NOTES_1.0.0-alpha2.md`.
- [ ] Mark `1.0.0-alpha1` unsupported on Drupal.org (it fails to install).

## Before Marketplace Submission

- [ ] Decide applicant path and support owner.
- [ ] Confirm all required dependencies are stable and patch-free.
- [ ] Complete theme/design-system proof.
- [ ] Complete Canvas authoring proof. Canvas shell proof exists; component-composed page proof remains.
- [ ] Complete Paragraphs authoring proof. Runtime save/render proof exists; manual editor UI/reorder proof remains.
- [ ] Complete rendered HTML proof.
- [ ] Complete JSON:API/access proof after Paragraphs exist.
- [ ] Complete sitemap/search proof.
- [ ] Add JSON-LD only where rendered content supports it.
- [ ] Complete WCAG 2.2 AA review.
- [ ] Complete performance review.
- [ ] Complete responsive screenshot review.
- [ ] Complete privacy/security attestations.
- [ ] Create preview/demo URL.
- [ ] Finalize screenshot and marketing images.
- [ ] Finalize content and asset license summary.
- [ ] Finalize project/listing copy.
- [ ] Run proposal/copy review.
- [ ] Submit complete repositories, marketing materials, draft content, and images.
