<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_registry', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 200);
            $table->string('service_type', 50);
            $table->text('base_url')->nullable();
            $table->string('api_key_ref', 200)->nullable();
            $table->jsonb('endpoints')->nullable()->default('{}');
            $table->boolean('is_active')->default(true);
            $table->string('health_status', 50)->default('unknown');
            $table->timestamp('last_health_check')->nullable();
            $table->jsonb('metadata')->nullable()->default('{}');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_registry');
    }
};
