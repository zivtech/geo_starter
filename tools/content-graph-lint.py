#!/usr/bin/env python3
"""Lint the recipe's content/ dependency graph.

Three invariants, enforced at authoring time (run from the repo root or pass
the content dir as the first argument):

1. Completeness — every `entity: <uuid>` reference inside a file's field
   values must be declared in that file's `_meta.depends`. A missing edge
   imports in arbitrary filesystem order and may resolve to NULL.
2. Acyclicity — the depends digraph must have no cycles. Core's
   DefaultContent importer has no cycle detection: with a cycle, at least
   one reference silently imports as NULL in *every* order
   (core Importer::setFieldValues() passes the unresolved NULL straight to
   setValue()), and when the broken edge is an entity_reference_revisions
   field the import crashes outright (ERR onChange() calls getRevisionId()
   on NULL — entity_reference_revisions 1.14, line 230). Found 2026-06-07:
   service -> section_card_grid paragraph -> answer -> service. Which edge
   breaks depends on directory iteration order, so green installs do not
   prove the graph is sound — this lint does.
3. Resolvability — every depends key must be a shipped entity or an
   allowlisted external type (e.g., `user`). A depends entry with no
   corresponding shipped file is a phantom: the dependency graph is false,
   and any tool that reads `depends:` as authoritative hits an unresolvable
   UUID.

Dev-only tool: not executed by `drush recipe` / `site:install`.
Exit 0 = clean; exit 1 = violations printed.
"""
import glob
import os
import sys

import yaml

root = sys.argv[1] if len(sys.argv) > 1 else os.path.join(
    os.path.dirname(__file__), '..', 'content')
root = os.path.abspath(root)

graph = {}
labels = {}
raw_depends = {}
problems = []


def iter_entity_refs(value):
    """Yield every uuid used as an `entity:` reference inside field values."""
    if isinstance(value, dict):
        ref = value.get('entity')
        if isinstance(ref, str) and len(ref) == 36:
            yield ref
        for v in value.values():
            yield from iter_entity_refs(v)
    elif isinstance(value, list):
        for v in value:
            yield from iter_entity_refs(v)


for path in sorted(glob.glob(root + '/**/*.yml', recursive=True)):
    data = yaml.safe_load(open(path))
    if not isinstance(data, dict) or '_meta' not in data:
        continue
    uuid = data['_meta']['uuid']
    depends = (data['_meta'].get('depends') or {})
    graph[uuid] = set(depends)
    raw_depends[uuid] = dict(depends)
    labels[uuid] = os.path.relpath(path, root)
    for ref in iter_entity_refs(data.get('default', {})):
        if ref not in depends:
            problems.append(
                f"MISSING DEPENDS: {labels[uuid]} references {ref} "
                f"in a field value but does not declare it in _meta.depends")

# Resolvability check — every depends key must resolve to a shipped file
# or be an allowlisted external type (e.g., user 1 which the importer handles).
EXTERNAL_TYPES = {'user'}
for owner, deps in raw_depends.items():
    for dep_uuid, dep_type in deps.items():
        if dep_type in EXTERNAL_TYPES:
            continue
        if dep_uuid not in graph:
            problems.append(
                f"PHANTOM DEPENDS: {labels[owner]} declares depends on "
                f"{dep_uuid} ({dep_type}) which has no shipped content file")

# Cycle detection (iterative DFS, three-color).
WHITE, GRAY, BLACK = 0, 1, 2
color = {u: WHITE for u in graph}
for start in graph:
    if color[start] != WHITE:
        continue
    stack = [(start, iter(sorted(graph[start])))]
    color[start] = GRAY
    trail = [start]
    while stack:
        node, children = stack[-1]
        advanced = False
        for child in children:
            if child not in graph:
                continue  # external dep (e.g., user 1); importer handles it
            if color[child] == GRAY:
                cycle = trail[trail.index(child):] + [child]
                problems.append(
                    'CYCLE: ' + ' -> '.join(labels[u] for u in cycle))
            elif color[child] == WHITE:
                color[child] = GRAY
                stack.append((child, iter(sorted(graph[child]))))
                trail.append(child)
                advanced = True
                break
        if not advanced:
            color[node] = BLACK
            stack.pop()
            trail.pop()

if problems:
    print(f"content-graph-lint: {len(problems)} problem(s) in {root}")
    for p in problems:
        print('  ' + p)
    sys.exit(1)

print(f"content-graph-lint: OK — {len(graph)} entities, "
      f"{sum(len(v) for v in graph.values())} depends edges, no cycles, "
      "all field entity-refs declared.")
