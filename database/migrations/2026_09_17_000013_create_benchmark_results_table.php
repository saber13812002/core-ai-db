<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benchmark_results', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('benchmark_session_id')->constrained('benchmark_sessions')->cascadeOnDelete();

            $table->uuid('candidate_output_id')->nullable()->constrained('processed_outputs')->nullOnDelete();
            $table->uuid('candidate_cleaned_id')->nullable()->constrained('cleaned_outputs')->nullOnDelete();
            $table->uuid('baseline_output_id')->nullable()->constrained('processed_outputs')->nullOnDelete();
            $table->uuid('baseline_cleaned_id')->nullable()->constrained('cleaned_outputs')->nullOnDelete();
            $table->uuid('ground_truth_id')->nullable()->constrained('human_ground_truth')->nullOnDelete();

            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('quality_rate', 5, 2)->nullable();
            $table->jsonb('metrics')->nullable()->default('{}');

            $table->jsonb('comparison_details')->nullable()->default('{}');
            $table->boolean('is_candidate_better')->nullable();
            $table->decimal('improvement_percent', 6, 2)->nullable();

            $table->unsignedInteger('judge_model_id')->nullable()->constrained('models')->nullOnDelete();
            $table->uuid('judge_prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->jsonb('judge_raw_response')->nullable();

            $table->boolean('human_validated')->default(false);
            $table->uuid('human_validated_by')->nullable();
            $table->timestamp('human_validated_at')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('benchmark_session_id');
            $table->index('candidate_output_id');
            $table->index('baseline_output_id');
            $table->index('is_candidate_better');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benchmark_results');
    }
};
