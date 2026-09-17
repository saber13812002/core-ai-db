<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_evaluations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('trained_model_id')->constrained('trained_models')->cascadeOnDelete();
            $table->uuid('benchmark_session_id')->nullable()->constrained('benchmark_sessions')->nullOnDelete();

            $table->string('evaluation_type', 50)->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->jsonb('metrics')->nullable()->default('{}');

            $table->uuid('baseline_model_id')->nullable()->constrained('trained_models')->nullOnDelete();
            $table->decimal('improvement_percent', 6, 2)->nullable();
            $table->boolean('is_better_than_baseline')->nullable();

            $table->unsignedInteger('judge_model_id')->nullable()->constrained('models')->nullOnDelete();
            $table->uuid('judge_prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->jsonb('details')->nullable()->default('{}');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('trained_model_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_evaluations');
    }
};
