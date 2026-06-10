# Security Policy

## Current Status

GEO Starter is a stable (1.x) Drupal CMS site-template recipe maintained as a
Community project. It is **not yet covered by Drupal Security Team
advisories**: stable 1.0.0 is the prerequisite to apply for opt-in coverage,
and that application is a tracked post-1.0 follow-up. Do not assume advisory
coverage until the project page shows it. The project also does not have
Marketplace security attestations.

## Reporting A Security Issue

Do not open public issues for suspected vulnerabilities. Contact the project maintainers privately through the repository owner or the support channel named in `SUPPORT.md`.

Do not include secrets, credentials, private data, or unpublished content in reports unless a maintainer has provided a secure channel.

## Release Security Checks

Before each release, the maintainers should verify:

- required dependencies are stable Drupal.org projects where applicable;
- no patches or pinned exact versions are required;
- `composer audit` reports no advisories across the resolved tree;
- anonymous JSON:API access exposes published public content only;
- unpublished nodes, draft content, and Paragraph revisions are not exposed anonymously;
- generated markup does not include secrets, credentials, or private paths;
- future agent-facing or write-capable interfaces have a separate threat model.

## Security-Team And Marketplace Gates

Follow-ups now that 1.0.0 is stable:

- Apply for Drupal Security Team opt-in coverage (tracked separately; do not
  imply coverage in project copy until granted).
- Before any Marketplace submission, verify advisory coverage status,
  Marketplace security attestations, and the final support contact path.

## Scope

This policy covers the site-template recipe, included configuration, sample content, helper scripts, and documentation in this repository.
