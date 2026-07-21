#!/usr/bin/env python3
"""Fail when a Markdown link in the shipped archive targets a missing file."""

from __future__ import annotations

import io
import posixpath
import re
import subprocess
import sys
import tarfile

LINK = re.compile(r"!?(?:\[[^]]*\])\(([^)]+)\)")
EXTERNAL = re.compile(r"(?:[a-z][a-z0-9+.-]*:|//)", re.I)


def archive_files() -> dict[str, str]:
    archive = subprocess.run(
        ["git", "archive", "--format=tar", "HEAD"], check=True,
        stdout=subprocess.PIPE, stderr=subprocess.PIPE,
    ).stdout
    files: dict[str, str] = {}
    with tarfile.open(fileobj=io.BytesIO(archive)) as tar:
        for member in tar.getmembers():
            if member.isfile() and member.name.endswith((".md", ".mdx")):
                extracted = tar.extractfile(member)
                assert extracted is not None
                files[member.name] = extracted.read().decode("utf-8")
    return files


def target_path(source: str, raw_target: str) -> str | None:
    target = raw_target.strip().strip("<>").split("#", 1)[0].split("?", 1)[0]
    if not target or EXTERNAL.match(target):
        return None
    # Repository-root references are common in this doc set (for example,
    # docs/INSTALL.md from another file in docs/).
    base = "" if target.startswith(("README", "docs/", "SUPPORT", "SECURITY")) else posixpath.dirname(source)
    return posixpath.normpath(posixpath.join(base, target)).lstrip("./")


def main() -> int:
    files = archive_files()
    problems: list[str] = []
    for source, text in sorted(files.items()):
        for match in LINK.finditer(text):
            target = target_path(source, match.group(1))
            if target and target not in files and target not in archive_names:
                problems.append(f"{source}: exported link target is absent: {match.group(1)}")
    if problems:
        print(f"exported-doc-link-check: {len(problems)} broken exported link(s)")
        print("\n".join(f"  {problem}" for problem in problems))
        return 1
    print(f"exported-doc-link-check: OK — {len(files)} exported Markdown file(s), all local links resolve")
    return 0


if __name__ == "__main__":
    # Keep non-Markdown link targets valid too (JSON, YAML, images, etc.).
    archive = subprocess.run(
        ["git", "archive", "--format=tar", "HEAD"], check=True,
        stdout=subprocess.PIPE, stderr=subprocess.PIPE,
    ).stdout
    with tarfile.open(fileobj=io.BytesIO(archive)) as tar:
        archive_names = {member.name for member in tar.getmembers() if member.isfile()}
    sys.exit(main())
