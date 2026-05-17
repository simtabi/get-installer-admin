<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->string('name', 200);
            $table->unsignedSmallInteger('schema_version')->default(1);
            // The full registry.json payload. SQLite stores as TEXT;
            // Postgres uses JSONB. Validated against the get-installer
            // registry.schema.json before write at the controller layer.
            $table->json('body');
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registries');
    }
};
