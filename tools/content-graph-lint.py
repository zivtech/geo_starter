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
3. Component-config coverage — every `component_id` placed by canvas_page
   content or a canvas.page_region.* config must ship a matching
   config/canvas.component.<id>.yml. Canvas's component-config
   auto-generation is install-stack-dependent and, on current Drupal CMS
   stacks, runs after recipe content import (it also skips some SDCs
   entirely — canvas #3591658), so a missing shipped config fails
   canvas_page import validation with "config does not exist"
   (1.0.0-beta1 defect #2, found 2026-06-07).

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


def iter_component_ids(value):
    """Yield every `component_id` placed inside a Canvas component tree."""
    if isinstance(value, dict):
        cid = value.get('component_id')
        if isinstance(cid, str):
            yield cid
        for v in value.values():
            yield from iter_component_ids(v)
    elif isinstance(value, list):
        for v in value:
            yield from iter_component_ids(v)


repo = os.path.dirname(root)
component_refs = {}  # component_id -> set of repo-relative referencing files

for path in sorted(glob.glob(root + '/**/*.yml', recursive=True)):
    data = yaml.safe_load(open(path))
    if not isinstance(data, dict) or '_meta' not in data:
        continue
    uuid = data['_meta']['uuid']
    depends = (data['_meta'].get('depends') or {})
    graph[uuid] = set(depends)
    labels[uuid] = os.path.relpath(path, root)
    for ref in iter_entity_refs(data.get('default', {})):
        if ref not in depends:
            problems.append(
                f"MISSING DEPENDS: {labels[uuid]} references {ref} "
                f"in a field value but does not declare it in _meta.depends")
    if data['_meta'].get('entity_type') == 'canvas_page':
        for cid in iter_component_ids(data.get('default', {})):
            component_refs.setdefault(cid, set()).add(
                os.path.relpath(path, repo))

# Component-config coverage (invariant 3).
config_dir = os.path.join(repo, 'config')
if os.path.isdir(config_dir):
    for path in sorted(glob.glob(config_dir + '/canvas.page_region.*.yml')):
        for cid in iter_component_ids(yaml.safe_load(open(path))):
            component_refs.setdefault(cid, set()).add(
                os.path.relpath(path, repo))
    for cid in sorted(component_refs):
        if not os.path.isfile(
                os.path.join(config_dir, f'canvas.component.{cid}.yml')):
            for src in sorted(component_refs[cid]):
                problems.append(
                    f"MISSING COMPONENT CONFIG: {src} places component "
                    f"'{cid}' but config/canvas.component.{cid}.yml is not "
                    f"shipped — canvas_page import will fail validation "
                    f"(auto-generation runs after content import)")
else:
    print(f"content-graph-lint: NOTE — no config/ dir next to {root}; "
          "component-config coverage not checked.")

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
      "all field entity-refs declared, "
      f"{len(component_refs)} placed components all have shipped configs.")
