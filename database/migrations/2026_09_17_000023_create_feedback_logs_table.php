<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_logs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('source_file_id')->nullable()->constrained('source_files')->nullOnDelete();
            $table->uuid('processed_output_id')->nullable()->constrained('processed_outputs')->nullOnDelete();
            $table->uuid('cleaned_output_id')->nullable()->constrained('cleaned_outputs')->nullOnDelete();
            $table->uuid('trained_model_id')->nullable()->constrained('trained_models')->nullOnDelete();
            $table->uuid('prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->unsignedInteger('model_id')->nullable()->constrained('models')->nullOnDelete();

            $table->string('feedback_type', 20);
            $table->string('user_id', 200)->nullable();
            $table->string('session_id', 200)->nullable();
            $table->text('comment')->nullable();
            $table->jsonb('context')->nullable()->default('{}');

            $table->timestamp('created_at')->useCurrent();

            $table->index('feedback_type');
            $table->index('processed_output_id');
            $table->index('source_file_id');
            $table->index('prompt_id');
            $table->index('model_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_logs');
    }
};
