# Changelog

## Unreleased

- **Breaking (taxonomy correction).** Fixed an inverted taxonomy: the `topic` vocabulary
  previously held page-aspect terms (Eligibility, Costs, Deadlines, …) while the actual
  subject domains lived in a separate `service_area` vocabulary — so a retrieval query for
  "topics" returned page sections, not subjects. The subject terms (Benefits and assistance,
  Permits and records, Community programs, Housing and utilities) now live in `topic`; the
  page-aspect terms, the `service_area` vocabulary, `field_service_area`, and its field
  storage are removed. `field_topic` is now the single subject axis — required on Service
  and Answer, optional on Article (editorial/cross-cutting pieces) and Evidence Source.
  Taxonomy reference widgets no longer allow inline term creation.
- This **supersedes and corrects** the earlier note "Restored Service area configuration
  consistency," which described the pre-correction (still-inverted) model and was inaccurate.
- Sample content, the `tools/` sample-content re-seed, and the JSON:API access-probe script
  were updated to the corrected model.
- Existing `1.0.0-alpha1` installs need a manual taxonomy migration; no automated upgrade
  path ships. The corrected model is for fresh installs.
- **Re-validation pending.** A fresh Drupal CMS install/apply has NOT yet been run against
  the corrected taxonomy; the evidence in `docs/VALIDATION.md` reflects the pre-correction
  model.
- Updated Drupal.org project-page copy around the narrative "Drupal is the CMS for an age of agents."

## 1.0.0-alpha1 - 2026-05-29

- Initial Community alpha for `drupal/geo_starter`.
- Added Drupal CMS site-template recipe shape with `type: Site`.
- Added MVP Service, Answer, Article, Evidence Source, Audience, Topic, and Service area content model configuration.
- Added sample public-service content for install and JSON:API access proof.
- Added Canvas as the visual-page authoring lane with a Canvas Page shell.
- Added Paragraphs and Entity Reference Revisions with `geo_starter_section` attached to Service, Answer, and Article nodes.
- Added Olivero as the basic public frontend theme for alpha rendering.
- Added clean install, rendered-page, YAML parsing, and JSON:API published/draft access validation evidence.
- Added alpha documentation for installation, limitations, dependencies, content licenses, migration mapping, schema boundaries, support, and security reporting.

## Earlier Alpha Scaffold Work

- Reframed the starter as a governed, GEO-friendly migration foundation for teams moving from headless/composable or legacy page/post CMS stacks.
- Added required package constraints for Canvas, Paragraphs, and Entity Reference Revisions.
- Updated the recipe install list for the required dual-authoring dependency posture.
- Clarified validation gaps after the dual-authoring dependency expansion.
