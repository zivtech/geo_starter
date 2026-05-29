# Session Handoff — Taxonomy Inversion Fix (2026-05-29)

Internal working note for resuming the GEO Starter taxonomy work. Not part of the
recipe's public documentation set.

## State

`main` is at the merged taxonomy-inversion fix (merge commit on top of `dae4e73`),
pushed to **both** remotes (`origin` = GitHub, `drupalcode` = drupal.org). The fix is
live on the drupal.org dev branch with **honest "re-validation pending" docs** — it has
**not** been validated on a real install.

## What was fixed

A multi-critic review found a CRITICAL taxonomy inversion that a prior Codex commit
(`e72b881`) did not resolve (it added descriptions + evidence_source tagging but kept the
inverted model and documented it as intended).

- **Before:** `topic` vocabulary held page-aspect terms (Eligibility, Costs, Deadlines…);
  subject domains lived in a separate `service_area` vocabulary. A retrieval query for
  "topics" returned page sections, not subjects.
- **After:** subjects (Benefits and assistance, Permits and records, Community programs,
  Housing and utilities) live in `topic` (UUIDs preserved). Page-aspect terms, the
  `service_area` vocabulary, `field_service_area`, and its storage are removed.
  `field_topic` is the single subject axis — required on Service + Answer, optional on
  Article (editorial/cross-cutting) and Evidence Source. Taxonomy widgets no longer allow
  inline term creation. Sample content + `tools/` scripts re-pointed to the corrected model.

## Static verification (passing)

- `0` occurrences of `service_area` / `field_service_area` / `autocomplete_tags` in
  config/content/tools
- 13 nodes carry `field_topic`; term bundles `topic=4 + audience=5`
- all config + content YAML parses valid (`yaml.safe_load`, 0 invalid)

## THE GATE — what must happen before a release tag

`drush recipe apply` has **not** been run. Re-validate on a throwaway Drupal CMS DDEV install:

1. Apply this recipe into a fresh Drupal CMS site.
2. Run `tools/create-alpha-sample-content.php` and `tools/create-jsonapi-access-probes.php`.
3. Confirm: import succeeds with `field_topic` required on Service/Answer (every demo node
   carries a subject term, so it should satisfy — this is the one thing reasoned but not
   yet observed); a vocabulary query for `topic` returns subjects; JSON:API returns 200
   published / 403 draft.

When green:
- Update `CHANGELOG.md` (replace the "re-validation pending" note with the passing run).
- Remove the stale banner in `docs/VALIDATION.md` and record the real evidence.
- Rewrite `docs/DRUPAL_ORG_RELEASE_NOTES_1.0.0-alpha1.md` (currently banner-marked
  "do not publish") to the corrected model.
- Only then cut a release tag. **No tag has been created this session.**

## Open follow-ups (optional)

- Tag the corrected release once validated.
- TraceAIO per-model GEO measurement of the sample content (Jakub Suchy's open-source tool).
- `docs/AUTHORING_MODEL.md` could gain an explicit paragraph on the required/optional
  asymmetry (currently only in field descriptions + CHANGELOG).
