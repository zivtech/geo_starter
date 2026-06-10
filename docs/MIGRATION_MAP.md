# Migration Destination Map

GEO Starter is a migration-ready destination model, not a turnkey importer.

This map shows how common source CMS patterns can land in Drupal structures without naming specific source competitors in public copy.

## Source-To-Drupal Map

| Source pattern | Drupal destination | Authoring lane | Notes |
| --- | --- | --- | --- |
| Homepage | Canvas page | Canvas | Visual composition and starter-kit first impression. |
| Landing page | Canvas page | Canvas | Preserve visual page-building patterns. |
| Campaign page | Canvas page | Canvas | Handles seasonal or urgent messaging. |
| Basic visual page | Canvas page or later Basic Page | Canvas today | Add Basic Page only if examples prove it is needed. |
| Blog/news post | Article node | Structured node plus Paragraphs | Preserve title, summary, author, body, tags/topics, reviewed dates, and optional `geo_starter_section` items. |
| Service/offering page | Service node | Structured node plus Paragraphs | Preserve direct answer, audience, topic, next action, eligibility, evidence, and optional `geo_starter_section` items. |
| FAQ item/reusable answer | Answer node or Accordion Paragraph | Structured node when reusable/source-backed | Use local accordion only for page-specific disclosure. |
| Evidence/source object | Evidence Source node | Fielded node | Required for visible citations and provenance. |
| Reusable block/component | Canvas component or Paragraph bundle | Both lanes | Map by source intent, not by storage mechanics. |
| Media asset | Drupal media | Shared | Preserve alt text, captions, and rights notes where available. |
| Category/tag | Topic (subject), Audience | Structured fields | Normalize inherited taxonomy instead of copying clutter. |
| Menu/navigation | Drupal menus/views/hubs | Shared | Use migration as a chance to simplify navigation. |
| SEO metadata | Drupal CMS SEO defaults or later Metatag path | Deferred | Choose implementation after rendered metadata proof. |
| Redirects/slugs | Redirect plan and path aliases | Launch planning | Required for real migrations; doc-only in this starter. |

## Migrated-Like Sample Scenarios

- Page/post front page to Canvas homepage.
- Page-builder landing/campaign page to Canvas campaign page.
- Headless structured service entry to Service node with optional `geo_starter_section` Paragraph sections.
- FAQ/reusable answer block to Answer node.
- Blog/news post to Article node.
- Citation/source entry to Evidence Source node.
- Inherited tags/categories to normalized Drupal vocabularies.
- Reusable block/component to equivalent Canvas component and Paragraph bundle where appropriate.

## Explicit Non-Goals

- Full legacy CMS export/import.
- Full headless CMS API import.
- Automated rich-text conversion for every embedded widget.
- Automatic page-builder-to-Canvas conversion.
- Automatic block-to-Paragraph conversion.
- Localization, commerce, membership, comments, or complex form migrations.
