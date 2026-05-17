<?php

namespace Tests\Feature\Api\V1;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TenantMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->getJson('/api/v1/tenants/me')->assertStatus(401);
    }

    public function test_returns_the_authenticated_user_tenant(): void
    {
        $tenant = Tenant::create(['slug' => 'acme', 'name' => 'Acme Inc.']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'email' => 'admin@acme.example',
            'password' => 'hashed-not-real',
            'role' => 'owner',
        ]);

        Passport::actingAs($user);

        $this->getJson('/api/v1/tenants/me')
            ->assertOk()
            ->assertJson([
                'id' => $tenant->id,
                'slug' => 'acme',
                'name' => 'Acme Inc.',
            ])
            ->assertJsonStructure(['id', 'slug', 'name', 'created_at']);
    }
}
