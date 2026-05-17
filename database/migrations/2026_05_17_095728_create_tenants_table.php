<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 80)->unique();
            $table->string('name', 200);
            $table->timestamps();
        });

        // Every existing User belongs to a tenant. We add the column,
        // backfill on the next deploy via a data migration, then make
        // it not-null. For the bootstrap commit, the column is
        // nullable so the seeded admin can be created before the
        // first tenant exists.
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained('tenants')
                ->nullOnDelete();
            $table->string('oauth_subject', 200)
                ->nullable()
                ->after('email')
                ->index();
            $table->enum('role', ['owner', 'admin', 'editor', 'viewer'])
                ->default('viewer')
                ->after('oauth_subject');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'oauth_subject', 'role']);
        });
        Schema::dropIfExists('tenants');
    }
};
