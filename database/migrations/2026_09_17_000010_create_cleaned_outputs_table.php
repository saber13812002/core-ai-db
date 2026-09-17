<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaned_outputs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('processed_output_id')->constrained('processed_outputs')->cascadeOnDelete();
            $table->uuid('source_file_id')->constrained('source_files')->cascadeOnDelete();
            $table->uuid('cleaning_prompt_id')->constrained('prompts')->restrictOnDelete();
            $table->unsignedInteger('model_id')->nullable()->constrained('models')->nullOnDelete();

            $table->text('content_text')->nullable();
            $table->jsonb('content_json')->nullable();
            $table->text('storage_path')->nullable();
            $table->string('content_hash', 64)->nullable();

            $table->string('cleaning_type', 50)->nullable();

            $table->decimal('quality_score', 5, 2)->nullable();
            $table->boolean('is_human_approved')->default(false);
            $table->uuid('human_approved_by')->nullable();
            $table->timestamp('human_approved_at')->nullable();

            $table->integer('version_number')->default(1);
            $table->uuid('superseded_by_id')->nullable();
            $table->boolean('is_latest')->default(true);

            $table->jsonb('processing_metadata')->nullable()->default('{}');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->index('processed_output_id');
            $table->index('source_file_id');
            $table->index('cleaning_prompt_id');
            $table->index('is_latest');
        });

        // Self-referencing FK must be added after the primary key exists.
        Schema::table('cleaned_outputs', function (Blueprint $table): void {
            $table->foreign('superseded_by_id', 'fk_cleaned_outputs_superseded_by')
                ->references('id')->on('cleaned_outputs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaned_outputs');
    }
};
