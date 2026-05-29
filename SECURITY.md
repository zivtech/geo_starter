# Security Policy

## Current Status

AI Visibility Starter is a Community alpha site-template scaffold. It does not yet have Marketplace security attestations.

## Reporting A Security Issue

Do not open public issues for suspected vulnerabilities. Contact the project maintainers privately through the repository owner or the support channel named in `SUPPORT.md`.

Do not include secrets, credentials, private data, or unpublished content in reports unless a maintainer has provided a secure channel.

## Release Security Gates

Before public release, the maintainers should verify:

- required dependencies are stable and security-covered where applicable;
- no patches or pinned exact versions are required;
- anonymous JSON:API access exposes published public content only;
- unpublished nodes, draft content, and Paragraph revisions are not exposed anonymously;
- generated markup does not include secrets, credentials, or private paths;
- future agent-facing or write-capable interfaces have a separate threat model.

## Scope

This policy covers the site-template recipe, included configuration, sample content, helper scripts, and documentation in this repository.
