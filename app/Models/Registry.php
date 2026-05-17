<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A get-installer registry owned by one Tenant.
 *
 * `body` holds the full registry.json payload; the controller layer
 * validates it against the get-installer registry.schema.json before
 * persisting.
 *
 * @property string $id
 * @property string $tenant_id
 * @property string $name
 * @property int $schema_version
 * @property array<string,mixed> $body
 */
class Registry extends Model
{
    use HasUuids;

    protected $fillable = ['tenant_id', 'name', 'schema_version', 'body'];

    /** @var array<string,string> */
    protected $casts = [
        'body' => 'array',
        'schema_version' => 'integer',
    ];

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
