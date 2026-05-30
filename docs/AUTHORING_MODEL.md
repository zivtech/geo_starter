# Authoring Model

GEO Starter uses two first-class authoring lanes with one shared public design vocabulary.

## Canvas Lane

Use Canvas for visual site-building surfaces:

- homepage;
- migration landing page;
- topic or service-area hub;
- campaign page.

Canvas pages should prove the starter can support familiar visual page-building jobs without turning every canonical structured page into a page-builder page.

The current alpha imports one Canvas page shell at `/geo-starter`. It proves the recipe can install Canvas and create a Canvas Page entity, but it does not yet prove a component-composed Canvas landing page.

## Paragraphs Lane

Use Paragraphs for governed reusable sections inside structured nodes:

- Service;
- Answer;
- Article.

Paragraphs enrich structured pages, but node fields remain the source of truth for direct answers, summaries, evidence sources, reviewed dates, topics, audiences, and next actions.

The current alpha ships one broad proof Paragraph type, `geo_starter_section`, and one specialized Service-only FAQ slice:

- `section_faq` — an optional FAQ section attached through Service `field_sections`;
- `section_faq_item` — nested question/answer items used only inside `section_faq`.

This proves the governed section lane while keeping specialized bundles limited to patterns that have a concrete retrieval and authoring payoff.

## Not Supported

- Free mixing of Canvas and Paragraphs on the same canonical page.
- Automatic conversion between Canvas pages and Paragraph sections.
- Treating Canvas component props and Paragraph fields as one interchangeable storage model.

## Shared Component Vocabulary

| Pattern | Canvas | Paragraphs / node fields |
| --- | --- | --- |
| Hero / page intro | Canvas component props | Node title, summary, optional `field_sections` item |
| Direct answer | Canvas text/source prop | `field_direct_answer` plus optional `field_sections` context |
| Evidence/source list | Selected source references | `field_evidence_sources` plus optional `field_sections` context |
| Step list | Repeatable step props | Future specialized Paragraph type |
| Card grid / related content | Selected cards/references | related fields, taxonomy lists, or `section_card_grid` |
| CTA / next action | URL/text props | `field_next_action` or `section_cta` |
| Alert / callout | Heading/body/severity props | `field_sections` item in the alpha; future specialized type if needed |
| Media/text | Media reference and text props | `body`, `field_summary`, or future specialized Paragraph type |
| Accordion / FAQ | Disclosure items | `section_faq` on Service nodes |
| Contact/action panel | Contact/action props | Future specialized Paragraph type |

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

Current proof completed on 2026-05-29:

- Canvas Page entity imports and `/geo-starter` returns `200`.
- `geo_starter_section` Paragraph type installs.
- `section_faq` and nested `section_faq_item` Paragraph types install for Service FAQ content.
- `field_sections` installs on Service, Answer, and Article.
- A Paragraph section can be created, attached to a Service node, saved, and rendered in HTML.

Still not proven:

- Component-composed Canvas pages.
- Manual editor UI create/edit/reorder screenshots.
- `section_faq` enablement on Answer and Article nodes.
- Accessibility and responsive review of the authoring output.
