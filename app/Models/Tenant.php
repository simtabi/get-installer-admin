<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One Tenant per organisation. Holds Users, Registries, and the
 * AuditLogEntries that record every mutation in that org's scope.
 *
 * Resolved per-request by the TenantScope middleware:
 * `auth()->user()?->tenant`. Models filtered by global scope (see
 * Registry::booted + AuditLogEntry::booted).
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 */
class Tenant extends Model
{
    use HasUuids;

    protected $fillable = ['slug', 'name'];

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<Registry, $this> */
    public function registries(): HasMany
    {
        return $this->hasMany(Registry::class);
    }

    /** @return HasMany<AuditLogEntry, $this> */
    public function auditLog(): HasMany
    {
        return $this->hasMany(AuditLogEntry::class);
    }
}
