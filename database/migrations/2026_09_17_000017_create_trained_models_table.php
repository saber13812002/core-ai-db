<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trained_models', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('training_job_id')->nullable()->constrained('training_jobs')->nullOnDelete();
            $table->string('name', 200);
            $table->string('version', 50);
            $table->string('model_type', 50)->nullable();
            $table->string('base_model_code', 120)->nullable();

            $table->text('storage_path')->nullable();
            $table->text('service_endpoint')->nullable();
            $table->string('status', 50)->default('ready');

            $table->jsonb('metadata')->nullable()->default('{}');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['name', 'version']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trained_models');
    }
};
