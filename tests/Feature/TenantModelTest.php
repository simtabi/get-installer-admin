<?php

namespace Tests\Feature;

use App\Models\AuditLogEntry;
use App\Models\Registry;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_uuid_is_assigned_on_create(): void
    {
        $tenant = Tenant::create(['slug' => 'acme', 'name' => 'Acme Inc.']);

        $this->assertNotNull($tenant->id);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $tenant->id,
        );
    }

    public function test_tenant_owns_registries_and_users(): void
    {
        $tenant = Tenant::create(['slug' => 'acme', 'name' => 'Acme Inc.']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'email' => 'admin@acme.example',
            'password' => 'hashed-not-real',
            'role' => 'owner',
        ]);
        $registry = Registry::create([
            'tenant_id' => $tenant->id,
            'name' => 'default',
            'schema_version' => 1,
            'body' => ['products' => []],
        ]);

        $this->assertCount(1, $tenant->users);
        $this->assertCount(1, $tenant->registries);
        $this->assertEquals($user->id, $tenant->users->first()->id);
        $this->assertEquals($registry->id, $tenant->registries->first()->id);
    }

    public function test_registry_body_round_trips_as_array(): void
    {
        $tenant = Tenant::create(['slug' => 'acme', 'name' => 'Acme']);
        $body = [
            'schema_version' => 1,
            'products' => [
                ['name' => 'tool', 'versions' => ['1.0.0']],
            ],
        ];
        $registry = Registry::create([
            'tenant_id' => $tenant->id,
            'name' => 'default',
            'body' => $body,
        ]);
        $registry->refresh();

        $this->assertSame($body, $registry->body);
    }

    public function test_audit_log_entry_persists_before_after(): void
    {
        $tenant = Tenant::create(['slug' => 'acme', 'name' => 'Acme']);
        $entry = AuditLogEntry::create([
            'tenant_id' => $tenant->id,
            'user_id' => null,
            'action' => 'update',
            'target_type' => Registry::class,
            'target_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'before' => ['v' => 1],
            'after' => ['v' => 2],
        ]);
        $entry->refresh();

        $this->assertSame(['v' => 1], $entry->before);
        $this->assertSame(['v' => 2], $entry->after);
        $this->assertNotNull($entry->created_at);
        $this->assertNull($entry->updated_at);  // append-only
    }

    public function test_audit_log_entry_belongs_to_tenant(): void
    {
        $tenant = Tenant::create(['slug' => 'acme', 'name' => 'Acme']);
        $entry = AuditLogEntry::create([
            'tenant_id' => $tenant->id,
            'action' => 'create',
            'target_type' => Registry::class,
            'target_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
        ]);

        $this->assertSame($tenant->id, $entry->tenant->id);
    }
}
