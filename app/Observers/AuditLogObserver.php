<?php

namespace App\Observers;

use App\Models\AuditLogEntry;
use Illuminate\Database\Eloquent\Model;

/**
 * Records every create / update / delete on the models it's attached
 * to into the audit_log_entries table. The actor (user_id) comes
 * from the authenticated request; null when an unauthenticated path
 * mutates (e.g., seeders, queue workers).
 *
 * Registered in AppServiceProvider::boot():
 *
 *   Registry::observe(AuditLogObserver::class);
 *   Tenant::observe(AuditLogObserver::class);   // optional
 */
class AuditLogObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'create', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $before = $model->getOriginal();
        $after = $model->getAttributes();
        $this->record($model, 'update', $before, $after);
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'delete', $model->getOriginal(), null);
    }

    /**
     * @param  array<string,mixed>|null  $before
     * @param  array<string,mixed>|null  $after
     */
    private function record(Model $model, string $action, ?array $before, ?array $after): void
    {
        $tenantId = $model->getAttribute('tenant_id');
        if (! is_string($tenantId)) {
            // Models without a tenant_id (Tenant itself when first
            // created) need a different audit path; skip silently
            // for now and surface in v0.2 when we audit Tenant ops.
            return;
        }

        AuditLogEntry::create([
            'tenant_id' => $tenantId,
            'user_id' => auth()->id(),
            'action' => $action,
            'target_type' => $model::class,
            'target_id' => (string) $model->getKey(),
            'before' => $before,
            'after' => $after,
        ]);
    }
}
