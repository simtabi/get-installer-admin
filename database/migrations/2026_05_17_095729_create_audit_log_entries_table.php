<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('action', ['create', 'update', 'delete']);
            $table->string('target_type', 80);   // e.g. "App\Models\Registry"
            $table->uuid('target_id');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->timestamps();

            // Cheap tenant-scoped lookups + chronological order.
            $table->index(['tenant_id', 'created_at']);
            $table->index(['tenant_id', 'target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log_entries');
    }
};
