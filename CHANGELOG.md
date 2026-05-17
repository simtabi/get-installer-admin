# Changelog

All notable changes to this project will be documented in this file.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Laravel 12 bootstrap landed** (`edfc845`, `059a60d`):
  `composer create-project laravel/laravel . "^12"` ran; the
  framework skeleton, PHPUnit, Vite, and Tailwind asset pipeline
  are committed. `composer.json` carries Simtabi metadata
  (`name`, `authors`, `support`). `config.platform.php = "8.2.0"`
  pins lockfile resolution so the maintainer's PHP 8.5 local and
  CI's PHP 8.3 / 8.4 runners generate compatible lockfiles.
  `package-lock.json` committed so `npm ci` works in CI.
- Initial repo scaffolding: README + LICENSE + CHANGELOG +
  CONTRIBUTING + SECURITY + CODE_OF_CONDUCT + `.editorconfig` +
  `.gitignore` (Laravel-aware) per Simtabi conventions.
- `.github/workflows/ci.yml` matrix for PHP 8.3 + 8.4 on Linux +
  macOS, with Node 22 for asset builds. Bootstrap precheck makes
  CI a no-op when `composer.json` / `package.json` are absent.
- `.github/dependabot.yml` weekly on Monday 06:00 America/New_York
  for composer + npm + github-actions.
- `.github/ISSUE_TEMPLATE/{bug_report,feature_request,config}.yml`
  + `PULL_REQUEST_TEMPLATE.md`.
- `docs/architecture.md` (multi-tenant data model, OAuth flow,
  background workers, signed-URL handoff),
  `docs/api/v1.yaml` (OpenAPI 3.1 skeleton for `/tenants/me`,
  `/registries`, `/audit`), `docs/decisions.md` (ADR index).
- `INITIALIZE.md` documents the `composer create-project laravel/laravel`
  bootstrap + Passport + Inertia + React + tenancy steps in order.

### Not yet

- Passport (OAuth) install — INITIALIZE.md step 2.
- Inertia + React + Tailwind asset pipeline — step 3.
- Multi-tenant data model + migrations — step 4.
- OpenAPI validator wired + controllers conforming to v1.yaml — step 5.
- Pest swap from PHPUnit — step 6.
- The first real `/api/v1/*` controller.

When Passport + the first migration land, that becomes the `[0.1.0]`
cut.
