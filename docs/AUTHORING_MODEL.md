# Authoring Model

AI Visibility Starter uses two first-class authoring lanes with one shared public design vocabulary.

## Canvas Lane

Use Canvas for visual site-building surfaces:

- homepage;
- migration landing page;
- topic or service-area hub;
- campaign page.

Canvas pages should prove the starter can support familiar visual page-building jobs without turning every canonical structured page into a page-builder page.

## Paragraphs Lane

Use Paragraphs for governed reusable sections inside structured nodes:

- Service;
- Answer;
- Article.

Paragraphs enrich structured pages, but node fields remain the source of truth for direct answers, summaries, evidence sources, reviewed dates, topics, audiences, and next actions.

## Not Supported

- Free mixing of Canvas and Paragraphs on the same canonical page.
- Automatic conversion between Canvas pages and Paragraph sections.
- Treating Canvas component props and Paragraph fields as one interchangeable storage model.

## Shared Component Vocabulary

| Pattern | Canvas | Paragraphs / node fields |
| --- | --- | --- |
| Hero / page intro | Canvas component props | Node title, summary, optional `section_hero` |
| Direct answer | Canvas text/source prop | `field_direct_answer` or `section_direct_answer` |
| Evidence/source list | Selected source references | `field_evidence_sources` or `section_evidence_list` |
| Step list | Repeatable step props | `section_step_list` |
| Card grid / related content | Selected cards/references | related fields, taxonomy lists, or `section_card_grid` |
| CTA / next action | URL/text props | `field_next_action` or `section_cta` |
| Alert / callout | Heading/body/severity props | `section_callout` |
| Media/text | Media reference and text props | `body` or `section_media_text` |
| Accordion / FAQ | Disclosure items | `section_accordion` |
| Contact/action panel | Contact/action props | `section_contact_panel` |

## First Rendered Proof Inventory

| ID | Page | Lane | Purpose |
| --- | --- | --- | --- |
| C-01 | Homepage | Canvas | Introduce the starter and primary service areas. |
| C-02 | Migration landing page | Canvas | Show source CMS patterns landing in Drupal structures. |
| C-03 | Service-area hub | Canvas | Group related services and answers. |
| C-04 | Campaign page | Canvas | Demonstrate time-sensitive public messaging. |
| P-01 | Service detail | Paragraphs on Service node | Canonical service content with governed sections. |
| P-02 | Answer detail | Paragraphs on Answer node | Reusable direct answer with sources and related services. |
| P-03 | Article detail | Paragraphs on Article node | Evidence-backed explainer or update. |
| F-01 | Evidence Source detail | Fielded node display | Make sources and citations inspectable. |

## Validation

Both authoring lanes need clean install, editor create/edit, reorder behavior, rendered output, responsive screenshots, keyboard checks, visible provenance, and access checks before public copy can claim "Canvas and Paragraphs authoring support."
