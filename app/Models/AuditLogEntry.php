<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per tenant-scoped mutation. Append-only; never deleted.
 *
 * Created by the AuditLogObserver when a Registry / Tenant is
 * created / updated / deleted (observer lives in
 * App\Observers\AuditLogObserver, registered in AppServiceProvider).
 *
 * @property string $id
 * @property string $tenant_id
 * @property int|null $user_id
 * @property string $action create|update|delete
 * @property string $target_type
 * @property string $target_id
 * @property array<string,mixed>|null $before
 * @property array<string,mixed>|null $after
 */
class AuditLogEntry extends Model
{
    use HasUuids;

    /**
     * Audit log is append-only: it has no `updated_at` semantic; the
     * record is immutable from creation. We still let Laravel manage
     * `created_at` via timestamps but never expose an update path.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id', 'user_id', 'action',
        'target_type', 'target_id',
        'before', 'after',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
