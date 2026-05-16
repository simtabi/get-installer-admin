# Security Policy

## Supported versions

`get-installer-admin` is pre-1.0. The latest tag is the only
supported version; backports happen on a case-by-case basis for
demonstrated impact.

## Reporting a vulnerability

Email `opensource@simtabi.com`. Include:

- A description of the issue + reproduction steps.
- The version (commit SHA if pre-tag) you observed it on.
- Your preferred disclosure timeline (we default to 90 days).

For multi-tenant data leakage, container-escape, or auth bypass:
mark the subject `[SECURITY URGENT]` and we'll respond within 24
hours.

## What's in scope

- Cross-tenant data leakage (Tenant A seeing Tenant B's registry
  or audit log).
- OAuth flow bypasses.
- API authorization bypass (`/api/v1/...` endpoints).
- Stored XSS / CSRF in the Inertia + React UI.
- Registry tampering (a logged-in tenant editing another tenant's
  registry).
- Audit-log gaps (mutations that don't get recorded).

## What's out of scope

- Self-hosted deployments that don't follow the documented
  hardening (`docs/deployment.md` once it lands).
- Issues that require physical access to the server.
- Issues in dependencies we don't ship (Laravel, Passport, etc.) —
  report those upstream.

## Threat model

See `docs/security.md` for the full threat model (after bootstrap).
Headline guarantees once shipped:

- OAuth-only admin login; no password auth in production.
- All API endpoints require a Passport bearer token AND a
  matching tenant scope.
- Audit log records every mutation with the actor's user id +
  tenant id + timestamp.
- Signed-URL handoff to `get-installer` uses short-lived HMAC
  tokens (max 5-minute TTL).

## Disclosure timeline

For confirmed issues:

1. Day 0: report received, acknowledged.
2. Day 1–7: triage, fix in private branch.
3. Day 8–14: patch released, advisory drafted.
4. Day 15+: public disclosure (GitHub Security Advisory).

Aggressive timelines compress on demonstrated active exploitation.
