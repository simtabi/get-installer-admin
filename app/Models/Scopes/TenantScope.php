<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope that filters every query on a tenant-scoped model by
 * the authenticated user's tenant_id.
 *
 * Models opt in via `static::addGlobalScope(new TenantScope)` in
 * their `booted()` method. Currently applied on Registry and
 * AuditLogEntry; Tenant + User are NOT scoped because:
 *
 * - Tenant: each user only sees their own, but lookup happens via
 *   `auth()->user()->tenant`, not a query filter.
 * - User: a tenant admin should see every user in their tenant, but
 *   the auth flow (Passport bearer) already resolves to one user.
 *
 * When no user is authenticated (web sign-in pages, queue workers
 * with explicit `withoutGlobalScope`), the scope is a no-op so we
 * don't accidentally return zero rows for legitimate unscoped reads.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        if ($user === null) {
            return;
        }
        $tenantId = $user->tenant_id ?? null;
        if ($tenantId === null) {
            return;
        }
        $builder->where(
            $model->qualifyColumn('tenant_id'),
            $tenantId,
        );
    }
}
