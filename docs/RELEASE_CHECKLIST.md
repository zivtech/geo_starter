# Release Checklist

## Before Any Public Alpha Release

Latest clean install evidence is recorded in `docs/VALIDATION.md`. Rerun the install/apply proof if dependencies, recipe config, or imported content change before tagging.

- [ ] Push current `main` to the canonical remote.
- [ ] Confirm `composer validate --strict` passes.
- [ ] Confirm `git diff --check` passes.
- [ ] Confirm no secrets or credentials are present.
- [ ] Rerun clean Drupal CMS install/apply after dependency changes.
- [ ] Replace placeholder `screenshot.webp`.
- [ ] Confirm project page copy does not claim Marketplace readiness.
- [ ] Confirm project page copy does not claim guaranteed AI placement.
- [ ] Confirm limitations are linked from README/project page.
- [ ] Confirm support and security policies are present.
- [ ] Confirm dependency summary is current.
- [ ] Confirm content and asset license summary is current.
- [ ] Tag alpha release.
- [ ] Publish release notes from `CHANGELOG.md`.

## Before Marketplace Submission

- [ ] Decide applicant path and support owner.
- [ ] Confirm all required dependencies are stable and patch-free.
- [ ] Complete theme/design-system proof.
- [ ] Complete Canvas authoring proof.
- [ ] Complete Paragraphs authoring proof.
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
