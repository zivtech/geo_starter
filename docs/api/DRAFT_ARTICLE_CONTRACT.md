# Draft Article handoff

`draft-article.schema.json` defines a local review artifact for one new GEO
Starter Article. It is not a JSON:API payload or a publishing API: its only
permitted moderation value is `draft`, and it rejects `status`, `publish`,
`operation`, and every other unknown key.

The contract requires an Article UUID, visible publication and reviewed dates,
at least one Evidence Source UUID, and one or more absolute HTTP(S) review
links. Its JSON input is capped at 256 KiB; title/author strings, rich text,
reference lists, and links are bounded; duplicate UUIDs are rejected. Rich text
must use the recipe's `content_format`. `reviewLinks` is handoff metadata for
reviewers; it is deliberately not written into Drupal. The remaining field names
map to the Article content model.

UUID collections and `reviewLinks` must be JSON arrays, never objects keyed by
labels. Review URLs use a conservative ASCII HTTP(S) subset: no embedded
credentials, ports are canonical decimal values from 1 through 65535, and each
percent escape must contain two hexadecimal digits.

Paragraph sections are intentionally excluded. They need their own nested
artifact contract because a bare Paragraph UUID does not prove revision ownership
or safely express component composition.

The shipped valid example is a contract fixture, not content to import as-is.
Replace its demo values, generate a fresh lowercase RFC 4122 `entityUuid`, and
resolve all reference UUIDs against the installed site before dry-run.

Validate an artifact without changing anything:

```bash
php tools/validate-draft-article.php docs/api/examples/draft-article.valid.json
```

Run the local fixture gate:

```bash
bash tests/draft-article-contract.sh
```

On a Drupal site installed from this recipe, the companion Drush script also
revalidates the artifact, resolves all supplied UUID references to their allowed
bundles, and defaults to a no-mutation dry run:

```bash
drush php:script /path/to/geo_starter/tools/import-draft-article.php -- --artifact=/absolute/path/article.json --actor-uid=<editor-uid>
drush php:script /path/to/geo_starter/tools/import-draft-article.php -- --artifact=/absolute/path/article.json --actor-uid=<editor-uid> --apply
```

Both modes require `--actor-uid` to identify an active Drupal editor with
`create article content`; dry-run uses the same create/reference-access checks
as apply. The UID is explicit attribution, not CLI authentication: whoever runs
this script already has trusted Drush/server access and can act as Drupal. It
creates exactly one new unpublished Article revision in `draft`, owned by that
editor; it refuses an existing UUID and never updates, publishes, archives, or
deletes content. It resolves every supplied reference, rejects wrong bundles,
and rejects references the selected editor cannot view. The revision log stores
the artifact UUID and a SHA-256 fingerprint of the exact bytes parsed, never
raw prompt/content. The command returns that hash with the node ID and UUID.
Preserve the imported artifact unchanged and record the review decision in a
separate ticket/log. A successful import still needs a human editorial review
and an explicit Drupal workflow transition before publication.

The separate generated `content-model.schema.json` is version `1.1.0` and uses
the next-recipe `1.2.0` artifact URL intentionally: Article publication date is
an additive contract change absent from the released `1.1.0` recipe artifact.
