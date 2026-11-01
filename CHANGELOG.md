# Changelog

All Zana packages share one version number and are released together. A release that did
not change this package is not listed.

## 2.10.0 — 2026-11-01

### Added

- `ValidationBehavior`: with mj-zana/mediator, validates every command and query before its handler
  runs; opt-in through `mediator.behaviors` (FE-53, ADR-025).
- `ValidationExceptionMapper`: with mj-zana/http, a `ValidationException` is a 422 with `errors` per
  field.
- Both are registered by `ValidationServiceProvider` only when their package is installed; the library
  still requires only PHP.

## 2.4.0 — 2026-10-26

### Changed

- Local development pins the sibling packages at 2.4.0. No change to the package itself.

## 2.3.0 — 2026-10-26

### Changed

- Tests and tooling (`tests/`, `phpunit.xml.dist`, `phpstan.neon.dist`) are no longer in Composer
  archives, so they stay out of production `vendor/` folders (FE-17).

## 2.1.0 — 2026-10-26

### Added

- `Violation::$template` and `$parameters`, and an optional `MessageTranslatorInterface` for the
  `Validator` (FE-02).

## 2.0.0 — 2026-10-26

The final 2.0 release. No changes since 2.0.0-rc.1.

## 2.0.0-beta.1 — 2026-10-25

### Added

- `ValidationServiceProvider`.

## 2.0.0-alpha.3 — 2026-10-24

First release of the 2.0 line. Attribute-based validation of objects, typically input DTOs. No dependencies. Requires PHP 8.5 or later.

Documentation: [the validation guide](https://github.com/mjnable/zana-docs/blob/main/guides/validation.md).
