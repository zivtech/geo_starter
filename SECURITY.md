# Security Policy

## Current Status

GEO Starter is a Community alpha site-template scaffold. Alpha and beta releases are not covered by Drupal security advisories; that is expected for this prerelease phase. The project also does not yet have Marketplace security attestations.

## Reporting A Security Issue

Do not open public issues for suspected vulnerabilities. Contact the project maintainers privately through the repository owner or the support channel named in `SUPPORT.md`.

Do not include secrets, credentials, private data, or unpublished content in reports unless a maintainer has provided a secure channel.

## Alpha Security Checks

Before each alpha release, the maintainers should verify:

- required dependencies are stable Drupal.org projects where applicable;
- no patches or pinned exact versions are required;
- anonymous JSON:API access exposes published public content only;
- unpublished nodes, draft content, and Paragraph revisions are not exposed anonymously;
- generated markup does not include secrets, credentials, or private paths;
- the agent-facing MCP interface follows the threat model below.

## Agent write threat model (MCP)

The recipe ships an agent introspection surface (`drupal/mcp_server` +
`drupal/simple_oauth`, with typed tools in the companion `geo_starter_mcp`
module — see `docs/MCP.md`). Its security boundaries:

- **Authentication:** OAuth 2.1 (`simple_oauth`). The HTTP transport must run
  with auth **required** whenever the write tools are enabled. Never expose
  write tools with auth disabled.
- **Least privilege:** two scopes — `geo:read` (`access content`, published
  only) and `geo:write` (the `geo_agent` role). Issue each client only the
  scopes it needs.
- **No agent publishing:** the `geo_agent` role is **not** granted the
  `publish` (or `archive`) transition of `geo_starter_editorial`. All agent
  writes are forced to `moderation_state: draft` / unpublished in
  `DraftWriter`; publishing is a human (or explicitly privileged) action.
- **Read boundary unchanged:** anonymous JSON:API still exposes published
  public content only; draft/unpublished content and Paragraph revisions are
  not exposed anonymously.
- **Token handling:** treat OAuth client secrets and bearer tokens as secrets;
  never commit them or place them in `content/`.

Live verification of these boundaries is part of release readiness and has not
been run in-repo (no live Drupal here) — see `docs/DEMO_RUNBOOK.md`.

## Stable/Marketplace Security Gates

Before a future stable or Marketplace submission, the maintainers should also verify Drupal security advisory coverage, Marketplace security attestations, and the final support contact path.

## Scope

This policy covers the site-template recipe, included configuration, sample content, helper scripts, and documentation in this repository.
