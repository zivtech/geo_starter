# Schema And Retrieval Map

Structured data must match visible rendered page content. Do not emit hidden claims or use JSON-LD to make the starter look more complete than the page actually is.

## Rules

- Render visible HTML first.
- Show sources, reviewed dates, direct answers, and next actions in public HTML.
- Add JSON-LD only where visible content supports it.
- Do not claim guaranteed rich results, AI citations, AI rankings, or answer-engine inclusion.
- Use `FAQPage` only when the rendered page and site context are eligible; otherwise prefer visible Q/A content plus `WebPage`.

## Emitted Types (1.x)

`drupal/geo_starter_jsonld` emits one parity-correct schema.org `@graph` per
full canonical published page (see the module's README for the authoritative
contract):

| Content item | Schema.org types emitted | Notes |
| --- | --- | --- |
| Service page | `WebPage`, `Service`; provider `ContactPoint` + `PostalAddress` + `hoursAvailable` from a contact panel section; gated `FAQPage` from `section_faq` | `Service` describes the service; `WebPage` describes the page. `FAQPage` requires ≥2 valid rendered Q&A pairs. |
| Answer page | `WebPage`, `Question`/`Answer` | Models the page's single direct answer; broad `FAQPage` claims stay gated to real multi-Q&A sections. |
| Article page | `WebPage`, `Article` | `NewsArticle` is not emitted; reserve it for time-sensitive news if ever added. |
| Evidence Source page | `CreativeWork` at a stable fragment `@id` | Cross-page `citation` `@id`s from citing pages resolve here; unpublished sources are suppressed. |
| Section content | Gated `HowTo` (step list), `ItemList` (card grid) | Emitted only when the rendered section supports them. |
| Navigation breadcrumbs | Not emitted | Candidate only; would have to match visible navigation. |

## Retrieval Surfaces

| Surface | Current posture (1.x) |
| --- | --- |
| Rendered HTML | Shipping: semantic section templates on Mercury (see `docs/LIMITATIONS.md` for the rendering boundary). |
| JSON:API | Proven on a fresh install: published 200 / draft 403 across nodes, Canvas pages, and paragraphs (re-proof 2026-06-07). |
| Sitemap | Shipping: `simple_sitemap` indexes the four canonical node types + Canvas pages; populates on first cron. Internal site search is deliberately not shipped. |
| JSON-LD | Shipping for all four node types + gated section emission; validated by PHPUnit suites in CI, the 23/23 acceptance probe, an offline domain-correctness check, and a hosted schema.org validator pass (0 errors/warnings). Google Rich-Results re-confirmation pending — no eligibility claimed. |
| MCP/hypermedia/RDF/agent writes | Research-only; not 1.x dependencies. |

## Validation

- JSON:API published/draft access checks on every release proof.
- Section render checks on structured nodes (WS-B assertions).
- Canvas page render checks (front page + C-01..C-04).
- Visible source links and reviewed dates.
- JSON-LD parity probe (23/23) plus external validator evidence — see `docs/VALIDATION.md`.
- Copy/proposal review before any public GEO or structured-data claims.
