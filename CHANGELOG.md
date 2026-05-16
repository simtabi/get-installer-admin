# Changelog

All notable changes to this project will be documented in this file.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Initial repo scaffolding: README + LICENSE + CHANGELOG +
  CONTRIBUTING + SECURITY + CODE_OF_CONDUCT + `.editorconfig` +
  `.gitignore` (Laravel-aware) per Simtabi conventions.
- `.github/workflows/{ci,release}.yml` matrices for PHP 8.3 + 8.4
  on Linux + macOS, with Node 22 for asset builds.
- `.github/dependabot.yml` weekly on Monday 06:00 America/New_York.
- `.github/ISSUE_TEMPLATE/{bug_report,feature_request,config}.yml`
  + `PULL_REQUEST_TEMPLATE.md`.
- `docs/architecture.md`, `docs/api/v1.yaml` (OpenAPI 3.1 skeleton),
  `docs/decisions.md` (ADR index).
- `INITIALIZE.md` documents the `composer create-project laravel/laravel`
  bootstrap + Passport + Inertia + React + tenancy steps in order.

### Not yet

- The Laravel install itself (`INITIALIZE.md` is the runbook).
- The multi-tenant data model migrations.
- OAuth provider integrations.
- The first real controller / route.

When the bootstrap lands, that becomes the first `[0.1.0]` cut.
