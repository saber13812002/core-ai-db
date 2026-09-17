<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benchmark_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 300);
            $table->text('description')->nullable();
            $table->string('benchmark_type', 50);
            $table->uuid('source_file_id')->nullable()->constrained('source_files')->nullOnDelete();
            $table->unsignedInteger('output_type_id')->nullable()->constrained('output_types')->nullOnDelete();
            $table->unsignedInteger('action_id')->nullable()->constrained('automation_actions')->nullOnDelete();
            $table->unsignedInteger('judge_model_id')->nullable()->constrained('models')->nullOnDelete();
            $table->uuid('judge_prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->string('status', 50)->default('pending');
            $table->integer('total_items')->nullable();
            $table->integer('processed_items')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->jsonb('metadata')->nullable()->default('{}');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benchmark_sessions');
    }
};
