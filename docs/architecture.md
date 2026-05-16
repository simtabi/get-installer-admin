# Architecture

`get-installer-admin` is a multi-tenant Laravel 13 application
that serves + edits `get-installer` registries. Each tenant has
their own registry JSON; the installer consumes the registry over
HTTPS with bearer-token auth.

```text
┌─────────────────────────────┐         ┌──────────────────────────┐
│  Browser (admin user)       │         │  CI runner / laptop      │
│  Inertia + React SPA        │         │  get-installer CLI       │
└────────┬────────────────────┘         └────────┬─────────────────┘
         │ OAuth (GitHub/GitLab/Entra)           │ Bearer token
         │ + Passport bearer (admin session)     │ from tenant's
         ▼                                        ▼ "machine identity"
┌────────────────────────────────────────────────────────────────────┐
│                      Laravel 13 application                        │
│                                                                    │
│  /admin/*                   (Inertia routes; HTML+JSON via SPA)    │
│  /api/v1/*                  (REST API, JSON only)                  │
│                                                                    │
│  Middleware: TenantScope (every request resolved to a tenant)     │
│  Auth:       Passport tokens, OAuth providers                     │
│  Audit log:  one entry per mutation                               │
└─────┬──────────────────────────────────────────────┬──────────────┘
      │ Eloquent                                     │ Queue
      ▼                                              ▼
┌─────────────┐                              ┌──────────────────┐
│ PostgreSQL  │                              │ Redis (jobs +    │
│ 17          │                              │ cache + locks)   │
└─────────────┘                              └──────────────────┘
```

## Multi-tenancy

Single database, `tenant_id` column on every model. Global scope on
each Eloquent model filters by the authenticated user's tenant.

```php
// In AppServiceProvider::boot()
Tenant::current() === auth()->user()?->tenant;

// On every tenant-scoped model:
protected static function booted(): void
{
    static::addGlobalScope(new TenantScope);
    static::creating(fn ($m) => $m->tenant_id = Tenant::current()->id);
}
```

We considered `stancl/tenancy` and rejected it: the surface here is
narrow (registries + audit logs), so a hand-rolled scope is ~50
lines of code that's easier to audit than a 5kloc package.

## Data model

- **tenants**: id, name, slug, created_at
- **users**: id, tenant_id, email, name, oauth_subject, role
  - role ∈ {owner, admin, editor, viewer}
- **registries**: id, tenant_id, name, schema_version, body (JSONB)
  - body is the full registry JSON; we don't normalise products + versions
    into separate tables because the registry is read 100x more than written.
- **audit_log_entries**: id, tenant_id, user_id, action, target_type,
  target_id, before (JSONB), after (JSONB), created_at
  - one row per mutation; never deleted

## OAuth flow

1. User clicks "Sign in with GitHub" on the admin login page.
2. Laravel Socialite redirects to GitHub OAuth.
3. Callback exchanges code for an access token + user info.
4. We look up or create a `User` keyed by `oauth_subject`.
5. If `User.tenant_id` is null (first sign-in for this org), the
   user picks or creates a tenant.
6. Laravel issues a Passport token tied to the user.
7. Subsequent API requests carry the bearer token; `TenantScope`
   handles isolation.

## Background workers

- **PyPI yank scan** (every 6 hours): walk every tenant's registry,
  for each `python` version block, query PyPI for the yanked status,
  update the registry if it changed, append an audit entry.
- **Audit-log compaction** (daily at 02:00 UTC): older than 90 days
  + low signal (no schema-version change) → archive to cold storage.

Driver: Redis queue (Laravel Horizon ships with a dashboard).

## Signed-URL handoff to the installer

For domain-locked installs (`get-installer` Phase E), the admin
app mints short-lived signed URLs that the installer consumes:

```
https://get.tenant.example.com/install.sh?\
  tenant=acme&\
  product=our-tool&\
  version=1.4.0&\
  exp=1759123456&\
  sig=<hmac-sha256>
```

The HMAC key lives in the tenant's `registries` row + is rotated
quarterly. URL TTL is 5 minutes max. The installer verifies the
signature against the tenant's allowlist.

## Deployment targets

| Target | Pros | Cons | When |
|---|---|---|---|
| Laravel Forge | Managed | Locked to AWS/DO/Linode | Single-tenant prod |
| Laravel Vapor | Serverless, scales | Cold starts; cost ceiling | Bursty traffic |
| Self-host Docker | Full control | You run it | Customer demand |
| Kubernetes | Cloud-native | Complexity overhead | Enterprise |

Default recommendation: Forge until traffic warrants Vapor or
self-host.

## Open questions

(Move these to `docs/adr/` as they're answered.)

- Tenant subdomain routing (`acme.example.com`) vs path (`example.com/acme`).
- Whether to ship a CLI for tenant admins (`get-installer-admin tenant create`).
- How `get-installer` clients authenticate to a self-hosted admin
  (bearer token vs mTLS vs OAuth client credentials).
