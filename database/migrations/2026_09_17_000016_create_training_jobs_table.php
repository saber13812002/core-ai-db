<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_jobs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('dataset_id')->constrained('datasets')->cascadeOnDelete();
            $table->unsignedInteger('service_id')->nullable()->constrained('service_registry')->nullOnDelete();
            $table->string('external_job_id', 200)->nullable();

            $table->string('base_model_code', 120)->nullable();
            $table->jsonb('training_config')->nullable()->default('{}');
            $table->string('status', 50)->default('queued');
            $table->integer('progress_percent')->default(0);

            $table->decimal('estimated_cost_usd', 10, 4)->nullable();
            $table->decimal('actual_cost_usd', 10, 4)->nullable();
            $table->text('error_message')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_jobs');
    }
};
