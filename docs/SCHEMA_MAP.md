# Schema And Retrieval Map

Structured data must match visible rendered page content. Do not emit hidden claims or use JSON-LD to make the starter look more complete than the page actually is.

## Rules

- Render visible HTML first.
- Show sources, reviewed dates, direct answers, and next actions in public HTML.
- Add JSON-LD only where visible content supports it.
- Do not claim guaranteed rich results, AI citations, AI rankings, or answer-engine inclusion.
- Use `FAQPage` only when the rendered page and site context are eligible; otherwise prefer visible Q/A content plus `WebPage`.

## Candidate Types

| Content item | Candidate Schema.org type | Notes |
| --- | --- | --- |
| Service page | `WebPage`, `Service`, `BreadcrumbList` | `Service` describes the service; `WebPage` describes the page. |
| Answer page | `WebPage`; `FAQPage` only when eligible | Most alpha answer pages should avoid broad FAQ rich-result claims. |
| Article page | `Article`, `WebPage`, `BreadcrumbList` | Use `NewsArticle` only for time-sensitive news. |
| Evidence Source | `CreativeWork` or citation reference | May not need standalone rich-result targeting. |
| Navigation breadcrumbs | `BreadcrumbList` | Must match visible navigation. |

## Retrieval Surfaces

| Surface | Alpha posture |
| --- | --- |
| Rendered HTML | Required proof. |
| JSON:API | Current proof passed after Paragraph field configuration; rerun when Canvas component content and more Paragraph types exist. |
| Sitemap/search | Required or strongly expected after rendered pages exist. |
| JSON-LD | Deferred until rendered-content parity can be validated. |
| MCP/hypermedia/RDF/agent writes | Research-only, not alpha dependencies. |

## Validation

- JSON:API published/draft access checks.
- Paragraph section render checks on structured nodes.
- Canvas page shell route check.
- Visible source links and reviewed dates.
- Structured data validator only after JSON-LD exists.
- Copy/proposal review before any public GEO or structured-data claims.
