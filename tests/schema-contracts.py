#!/usr/bin/env python3
"""Exercise the shipped JSON Schemas as strict, executable contracts."""

from __future__ import annotations

import copy
import json
from pathlib import Path
import subprocess
import tempfile

from jsonschema import Draft202012Validator, FormatChecker


ROOT = Path(__file__).resolve().parents[1]
UUID = "40000000-0000-4000-8000-000000000001"
PARITY_FIXTURES = {
    "draft-article.parity-multibyte-255.json": True,
    "draft-article.parity-multibyte-256.json": False,
    "draft-article.parity-review-links-reordered-duplicate.json": False,
    "draft-article.parity-whitespace-title.json": False,
    "draft-article.parity-url-edge-valid.json": True,
    "draft-article.parity-url-edge-invalid.json": False,
}


def load(path: str) -> dict:
    return json.loads((ROOT / path).read_text(encoding="utf-8"))


def errors(validator: Draft202012Validator, payload: object) -> list[str]:
    return [error.message for error in validator.iter_errors(payload)]


def assert_valid(validator: Draft202012Validator, payload: object, label: str) -> None:
    found = errors(validator, payload)
    if found:
        raise AssertionError(f"{label} should validate: {'; '.join(found)}")


def assert_invalid(validator: Draft202012Validator, payload: object, label: str) -> None:
    if not errors(validator, payload):
        raise AssertionError(f"{label} should be rejected")


def assert_draft_parity(
    validator: Draft202012Validator,
    payload: object,
    expected_valid: bool,
    label: str,
) -> None:
    schema_valid = not errors(validator, payload)
    with tempfile.NamedTemporaryFile("w", suffix=".json", encoding="utf-8") as fixture:
        json.dump(payload, fixture, ensure_ascii=False)
        fixture.flush()
        php_result = subprocess.run(
            ["php", str(ROOT / "tools/validate-draft-article.php"), fixture.name],
            capture_output=True,
            text=True,
            check=False,
        )
    php_valid = php_result.returncode == 0
    if schema_valid != expected_valid or php_valid != expected_valid:
        raise AssertionError(
            f"{label}: expected {expected_valid}; JSON Schema={schema_valid}, "
            f"PHP={php_valid}; PHP output: {php_result.stderr.strip()}"
        )


def with_field(valid: dict, field: str, value: object) -> dict:
    payload = copy.deepcopy(valid)
    payload[field] = value
    return payload


def with_review_url(valid: dict, uri: str) -> dict:
    payload = copy.deepcopy(valid)
    payload["reviewLinks"][0]["uri"] = uri
    return payload


def draft_dynamic_cases(valid: dict) -> list[tuple[str, dict, bool]]:
    link = {"label": "Guidance", "uri": "https://example.org/guidance"}
    return [
        ("associative UUID object", with_field(valid, "field_evidence_sources", {"primary": UUID}), False),
        ("numeric-key UUID object", with_field(valid, "field_evidence_sources", {"0": UUID}), False),
        ("associative reviewLinks object", with_field(valid, "reviewLinks", {"primary": link}), False),
        ("numeric-key reviewLinks object", with_field(valid, "reviewLinks", {"0": link}), False),
        ("year-zero date", with_field(valid, "field_publication_date", "0000-01-01"), False),
        ("valid leap date", with_field(valid, "field_publication_date", "2000-02-29"), True),
        ("invalid century leap date", with_field(valid, "field_publication_date", "1900-02-29"), False),
        ("out-of-range URL port", with_review_url(valid, "https://example.org:99999/path"), False),
        ("zero URL port", with_review_url(valid, "https://example.org:0/path"), False),
        ("noncanonical URL port", with_review_url(valid, "https://example.org:080/path"), False),
        ("invalid URL percent escape", with_review_url(valid, "https://example.org/%ZZ"), False),
        ("terminal URL line feed", with_review_url(valid, "https://example.org/path\n"), False),
        ("non-ASCII URL port", with_review_url(valid, "https://example.org:١/path"), False),
        ("embedded URL credentials", with_review_url(valid, "https://user@example.org/path"), False),
        ("maximum valid URL port", with_review_url(valid, "https://example.org:65535/path?q=a%20b#review"), True),
        ("minimal absolute URL", with_review_url(valid, "http://localhost"), True),
    ]


def content_model_contract() -> None:
    schema = load("docs/api/content-model.schema.json")
    Draft202012Validator.check_schema(schema)
    validator = Draft202012Validator(schema, format_checker=FormatChecker())

    service = {
        "title": "Emergency assistance",
        "field_direct_answer": {"value": "Apply through the governed intake."},
        "field_summary": {"value": "Help with an urgent household need."},
        "field_next_action": {"uri": "internal:/apply", "title": "Apply"},
        "field_reviewed_date": "2026-07-20",
        "field_topic": [UUID],
    }
    answer = {
        "title": "How do I apply?",
        "field_direct_answer": {"value": "Use the application form."},
        "field_reviewed_date": "2026-07-20",
        "field_topic": [UUID],
    }
    examples = ROOT / "docs/api/examples"
    article = json.loads(
        (examples / "content-model.valid-article.json").read_text(encoding="utf-8")
    )
    evidence = {
        "title": "Program guidance",
        "field_publisher": "Example agency",
        "field_source_type": "guideline",
        "field_source_url": {"uri": "https://example.org/guidance"},
    }

    for label, payload in {
        "Service": service,
        "Answer": answer,
        "Article": article,
        "Evidence Source": evidence,
    }.items():
        assert_valid(validator, payload, label)

    assert_invalid(validator, {"unrelated": True}, "unrelated root object")

    unknown = copy.deepcopy(service)
    unknown["invented_field"] = "not in Drupal config"
    assert_invalid(validator, unknown, "unknown Service field")

    malformed_uuid = copy.deepcopy(service)
    malformed_uuid["field_topic"] = ["not-a-uuid"]
    assert_invalid(validator, malformed_uuid, "malformed reference UUID")

    impossible_date = copy.deepcopy(article)
    impossible_date["field_reviewed_date"] = "2026-02-30"
    assert_invalid(validator, impossible_date, "impossible calendar date")

    assert_invalid(
        validator,
        json.loads(
            (examples / "content-model.invalid-empty-required.json").read_text(
                encoding="utf-8"
            )
        ),
        "empty required Article values",
    )
    assert_invalid(
        validator,
        json.loads(
            (examples / "content-model.invalid-empty-required-service.json").read_text(
                encoding="utf-8"
            )
        ),
        "empty required Service values",
    )


def draft_article_contract() -> None:
    schema = load("docs/api/draft-article.schema.json")
    Draft202012Validator.check_schema(schema)
    validator = Draft202012Validator(schema, format_checker=FormatChecker())
    examples = ROOT / "docs/api/examples"

    assert_valid(
        validator,
        json.loads((examples / "draft-article.valid.json").read_text(encoding="utf-8")),
        "valid draft Article fixture",
    )
    for fixture in sorted(examples.glob("draft-article.invalid-*.json")):
        assert_invalid(
            validator,
            json.loads(fixture.read_text(encoding="utf-8")),
            fixture.name,
        )

    # These edge fixtures must produce the same outcome from the JSON Schema
    # and the dependency-free PHP validator used by the local importer.
    for name, expected_valid in PARITY_FIXTURES.items():
        fixture = examples / name
        payload = json.loads(fixture.read_text(encoding="utf-8"))
        assert_draft_parity(validator, payload, expected_valid, name)

    valid = json.loads((examples / "draft-article.valid.json").read_text(encoding="utf-8"))
    for label, payload, expected_valid in draft_dynamic_cases(valid):
        assert_draft_parity(validator, payload, expected_valid, label)


def main() -> None:
    content_model_contract()
    draft_article_contract()
    print("schema-contracts: content model and draft Article contracts passed")


if __name__ == "__main__":
    main()
