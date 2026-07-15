# Security Policy

## Current Status

GEO Starter is a stable (1.x) Drupal CMS site-template recipe. Its stable
releases are **covered by Drupal's security advisory policy** — verified
2026-07-15 against both the project-page shield ("Stable releases for this
project are covered by the security advisory policy") and the release-history
feed (`security covered="1"` for 1.0.0, 1.0.1, and 1.1.0). Coverage was still
absent at the 1.0.1 publish (2026-06-10) and was observed granted by
2026-07-15.

What coverage does **not** mean: it is Drupal Security Team advisory handling
for vulnerabilities reported against stable releases — not a security audit,
review, or guarantee. Alpha and beta releases remain uncovered (standard
Drupal.org policy), and the project still has no Marketplace security
attestations.

## Reporting A Security Issue

Because stable releases are covered by the security advisory policy, report
suspected vulnerabilities **confidentially to the Drupal Security Team**: use
the "Report a security vulnerability" link in the sidebar of
<https://www.drupal.org/project/geo_starter>, which opens the Security Team's
private issue tracker (email `security@drupal.org` only if you cannot use the
tracker). Full process:
<https://www.drupal.org/docs/develop/issues/issue-procedures-and-etiquette/reporting-a-security-issue>

Do not open public issues for suspected vulnerabilities, and do not disclose
them before an advisory is issued. For security questions that are not
suspected vulnerabilities, use the support channel named in `SUPPORT.md`.

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
  The recipe ships none; the only documented agent-write path is the optional,
  experimental MCP opt-in (`docs/OPTIONAL_MCP.md`), which is unsupported and will
  receive its own security review (auth, OAuth scopes, no agent publish) before
  the typed GEO tools are ever packaged as a dependency.

## Security-Team And Marketplace Gates

- Drupal Security Team advisory coverage: **granted** for stable releases
  (verified 2026-07-15). Project copy may state advisory coverage — never
  audit, attestation, or "secure" claims.
- Before any Marketplace submission, the outstanding security gates are the
  Marketplace privacy/security attestations and the final support contact
  path.

## Scope

This policy covers the site-template recipe, included configuration, sample content, helper scripts, and documentation in this repository.
