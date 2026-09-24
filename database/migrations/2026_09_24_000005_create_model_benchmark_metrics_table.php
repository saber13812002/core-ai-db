<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_benchmark_metrics', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('model_evaluation_id')->constrained('model_evaluations')->cascadeOnDelete();

            // One row per metric: the score is produced by the judge prompt
            // dedicated to that metric (metric_prompt_id), as per the design.
            $table->string('metric_name', 100);
            $table->uuid('metric_prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->unsignedInteger('judge_model_id')->nullable()->constrained('models')->nullOnDelete();
            $table->jsonb('details')->nullable()->default('{}');

            $table->timestamp('evaluated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->index('model_evaluation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_benchmark_metrics');
    }
};
