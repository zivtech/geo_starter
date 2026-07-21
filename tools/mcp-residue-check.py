#!/usr/bin/env python3
"""MCP-residue gate over the shipped 1.x Composer artifact.

The base recipe ships zero MCP capability: no MCP packages, no OAuth
wiring, no typed GEO tool surface. Prose may say "MCP is an optional,
experimental opt-in — see docs/OPTIONAL_MCP.md" (the M1 re-architecture),
but nothing shipped may *advertise or wire* the capability. Two rules,
checked over the files `git archive HEAD` actually ships (export-ignore
honored), reading working-tree contents so uncommitted edits are gated:

1. Capability signatures hard-fail in EVERY shipped file:
   simple_oauth, geo_agent, geo.(describe|list_nodes|get_node|
   validate_node|create_node|update_node).
2. The broad tokens `mcp` / `oauth` hard-fail in MACHINE surfaces —
   recipe.yml, composer.json, config/, content/, tools/, docs/api/ except
   its README — because an agent-consumed surface that names MCP steers
   agents to tools that do not exist (the B1 defect).

Shipped prose (.md) may mention mcp/oauth (deferral statements, the
CHANGELOG's own record of the decision, this gate's definition in
RELEASE_CHECKLIST.md); those mentions are printed as an INFO count for
review, not failed. docs/OPTIONAL_MCP.md and this script are exempt.

Exit 0 = clean; exit 1 = violations printed.
"""
import io
import re
import subprocess
import sys
import tarfile

SIGNATURES = re.compile(
    r'simple_oauth|geo_agent'
    r'|geo\.(describe|list_nodes|get_node|validate_node|create_node|update_node)',
    re.IGNORECASE)
BROAD = re.compile(r'mcp|oauth', re.IGNORECASE)

# OPTIONAL_MCP.md is the sanctioned opt-in doc; this script and the release
# checklist define the gate itself and necessarily quote its patterns.
EXEMPT = {'docs/OPTIONAL_MCP.md', 'tools/mcp-residue-check.py',
          'docs/RELEASE_CHECKLIST.md'}


def is_machine(path):
    if path in ('recipe.yml', 'composer.json'):
        return True
    if path.startswith(('config/', 'content/', 'tools/')):
        return True
    if path.startswith('docs/api/') and not path.endswith('README.md'):
        return True
    return False


tar_bytes = subprocess.run(
    ['git', 'archive', 'HEAD', '--format=tar'],
    capture_output=True, check=True).stdout
shipped = [m.name for m in tarfile.open(fileobj=io.BytesIO(tar_bytes))
           if m.isfile()]

problems = []
prose_mentions = []
for path in sorted(shipped):
    if path in EXEMPT:
        continue
    try:
        text = open(path, encoding='utf-8', errors='ignore').read()
    except OSError:
        problems.append(f'UNREADABLE: {path} (shipped by git archive, '
                        'missing from the working tree)')
        continue
    for i, line in enumerate(text.splitlines(), 1):
        if SIGNATURES.search(line):
            problems.append(f'SIGNATURE: {path}:{i}: {line.strip()[:100]}')
        elif BROAD.search(line):
            if is_machine(path):
                problems.append(
                    f'MACHINE-SURFACE: {path}:{i}: {line.strip()[:100]}')
            else:
                prose_mentions.append(f'{path}:{i}')

if problems:
    print(f'mcp-residue-check: {len(problems)} violation(s)')
    for p in problems:
        print('  ' + p)
    sys.exit(1)

print(f'mcp-residue-check: OK — {len(shipped)} shipped files; no capability '
      f'signatures, machine surfaces clean; {len(prose_mentions)} sanctioned '
      'prose mention(s): ' + ', '.join(prose_mentions))
