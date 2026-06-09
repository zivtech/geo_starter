# Authoring Model

GEO Starter uses two first-class authoring lanes with one shared public design vocabulary.

## Canvas Lane

Use Canvas for visual site-building surfaces:

- homepage;
- migration landing page;
- topic or service-area hub;
- campaign page.

Canvas pages should prove the starter can support familiar visual page-building jobs without turning every canonical structured page into a page-builder page.

The recipe imports the Canvas front-page shell at `/geo-starter` plus four component-composed Canvas sample pages (C-01 homepage, C-02 migration landing, C-03 topic hub, C-04 campaign), built from stock Mercury components. The recipe ships `canvas.component.*` config for every component the trees use, and the accepted Canvas/Mercury minor range is capped to the validated one (see `docs/LIMITATIONS.md`).

## Paragraphs Lane

Use Paragraphs for governed reusable sections inside structured nodes:

- Service;
- Answer;
- Article.

Paragraphs enrich structured pages, but node fields remain the source of truth for direct answers, summaries, evidence sources, reviewed dates, topics, audiences, and next actions.

The 1.x line ships a ten-bundle section library, attached to Service, Answer, and Article nodes through `field_sections`:

- `geo_starter_section` — general heading/body section;
- `section_faq` + `section_faq_item` — FAQ with nested question/answer items (the gated JSON-LD `FAQPage` source);
- `section_step_list` + `section_step_item` — ordered steps (the JSON-LD `HowTo` source);
- `section_card_grid` — referenced-content cards (the JSON-LD `ItemList` source);
- `section_contact_panel` — structured contact details with `office_hours` (the JSON-LD `ContactPoint`/`hoursAvailable` source);
- `section_cta`, `section_alert`, `section_media_text` — visual page sections (JSON-LD-silent).

Specialized bundles stay limited to patterns with a concrete retrieval and authoring payoff. All ten render through the `geo_starter_jsonld_markup` semantic templates (see `docs/LIMITATIONS.md` for the rendering boundary).

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
| Step list | Repeatable step props | `section_step_list` + `section_step_item` |
| Card grid / related content | Selected cards/references | related fields, taxonomy lists, or `section_card_grid` |
| CTA / next action | URL/text props | `field_next_action` or `section_cta` |
| Alert / callout | Heading/body/severity props | `section_alert` |
| Media/text | Media reference and text props | `section_media_text` (or `body`/`field_summary`) |
| Accordion / FAQ | Disclosure items | `section_faq` via `field_sections` |
| Contact/action panel | Contact/action props | `section_contact_panel` (structured `office_hours`) |

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

Proven on a fresh install (see `docs/VALIDATION.md` for the runs):

- All four component-composed Canvas pages (C-01..C-04) and the front-page shell import and render (validated on Canvas 1.5.0/1.5.1 + Mercury 1.0.5).
- All ten section bundles import with their fields, render through the semantic templates (WS-B: 21/21 markup/label/scoping assertions, desktop + mobile screenshots per bundle), and the JSON-LD parity probe passes 23/23.
- `field_sections` installs on Service, Answer, and Article; a Paragraph section can be created, attached, saved, and rendered.
- Keyboard and AA-contrast spot-check passed on the homepage and Service page (WS-F).

Still not proven:

- Manual editor UI create/edit/reorder screenshots for Paragraph sections.
- Full accessibility and responsive release gates for the authoring output.
