# Contributing to get-installer-admin

## Before you start

1. Read [`README.md`](README.md) and the design doc at
   [`simtabi/get-installer/REPO-PROPOSAL-admin.md`](https://github.com/simtabi/get-installer/blob/main/REPO-PROPOSAL-admin.md).
2. If the bootstrap hasn't run yet (no `composer.json` at the repo
   root), follow [`INITIALIZE.md`](INITIALIZE.md) end-to-end first.

## Code conventions

- **PHP**: PSR-12. Pint (`./vendor/bin/pint`) enforces. PHPStan
  level 8 (`./vendor/bin/phpstan analyse`).
- **TypeScript**: ESLint + Prettier. `pnpm lint && pnpm format`.
- **Tests**: Pest (`./vendor/bin/pest`). Integration tests live
  under `tests/Feature/`, unit tests under `tests/Unit/`.
- **API**: every change to `app/Http/Controllers/Api/V1/` must also
  update `docs/api/v1.yaml` (OpenAPI 3.1 spec). CI verifies the
  controllers conform.

## Commit messages

- Imperative subject ≤ 72 chars.
- Body explains the **why**, not the **what**.
- No emoji unless we ask.
- No `Co-Authored-By:` trailers unless we ask.
- No AI-generated tells (`leverage`, `powerful`, `robust`,
  `comprehensive`, `seamless`).

## PR workflow

- Branch from `main`.
- Run the full quality gate before pushing:
  ```bash
  ./vendor/bin/pint --test
  ./vendor/bin/phpstan analyse
  ./vendor/bin/pest
  pnpm lint && pnpm typecheck && pnpm test
  ```
- Open a PR. Use the template prompts (what/why/how + checklist).
- CI must pass before merge. Don't `--no-verify`.

## Reporting bugs

[`SECURITY.md`](SECURITY.md) for security issues — private channel.
Everything else: open a Github Issue with the bug-report template.

## License

Contributions are MIT-licensed per [`LICENSE`](LICENSE). Open a
PR; we'll squash + merge.
