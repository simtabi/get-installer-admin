# get-installer-admin

Web admin for [`get-installer`](https://github.com/simtabi/get-installer)
registries. Laravel 12 + Inertia + React + REST API + OAuth.

> **Status: Laravel bootstrap landed.** PHP skeleton + PHPUnit +
> Vite are installed and committed; CI is green on PHP 8.3 + 8.4 ×
> Linux + macOS. Remaining steps in [`INITIALIZE.md`](INITIALIZE.md)
> (Passport, Inertia + React, multi-tenancy, OpenAPI validator
> wiring, Pest swap) run from this baseline.

## What this repo is for

`get-installer` reads a JSON registry of products + versions and
installs them onto user machines. For solo use, the registry sits
in a git repo. For teams or fleets, it needs a backend:

- Multi-tenant registries (one per organisation).
- OAuth (GitHub / GitLab / Microsoft Entra) for admin access.
- REST API at versioned `/api/v1/` for programmatic edits.
- Inertia + React UI for human admins.
- Background workers: PyPI yank scans, audit-log compaction.
- Audit log per tenant: every registry edit recorded.
- Phase E unlocked: signed-URL handoff to the installer for
  domain-locked installs.

The design rationale + alternatives considered live in the
upstream sibling repo:
[`simtabi/get-installer/REPO-PROPOSAL-admin.md`](https://github.com/simtabi/get-installer/blob/main/REPO-PROPOSAL-admin.md).

## Bootstrap

1. Clone this repo.
2. Follow [`INITIALIZE.md`](INITIALIZE.md) end-to-end. It runs
   `composer create-project laravel/laravel` in this directory,
   then installs Passport, Inertia, React, and the test stack.
3. Read `docs/architecture.md` for the multi-tenant data model.
4. Read `docs/api/v1.yaml` (OpenAPI 3.1 spec) for the contract
   surface; controllers conform to it.

## Status

| | |
|---|---|
| Bootstrap | ✓ Laravel 12 + Vite skeleton landed (`059a60d`) |
| Tests | PHPUnit (Laravel 12 default); Pest swap is INITIALIZE.md step 6 |
| Passport / OAuth | pending (INITIALIZE.md step 2) |
| Inertia + React | pending (INITIALIZE.md step 3) |
| Multi-tenancy | pending (INITIALIZE.md step 4) |
| API spec | placeholder at `docs/api/v1.yaml` (no controllers yet) |
| CI | ✓ PHP 8.3 + 8.4 × Linux + macOS, Node 22 |
| Trusted publishers | N/A (Laravel app, not a package) |

## License

MIT — see [`LICENSE`](LICENSE).

## Built by

[Simtabi LLC](https://simtabi.com) · contributions welcome once the
bootstrap has run.
