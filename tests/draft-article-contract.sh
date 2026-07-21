#!/usr/bin/env bash
set -euo pipefail

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
validator="$root/tools/validate-draft-article.php"
examples="$root/docs/api/examples"

php "$validator" "$examples/draft-article.valid.json"
php "$root/tests/draft-article-importer-contract.php"

for fixture in "$examples"/draft-article.invalid-*.json; do
  if php "$validator" "$fixture"; then
    echo "Expected invalid fixture to fail: $fixture" >&2
    exit 1
  fi
done
