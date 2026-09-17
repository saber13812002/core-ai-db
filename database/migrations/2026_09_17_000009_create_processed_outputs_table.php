<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processed_outputs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('job_id')->constrained('automation_jobs')->cascadeOnDelete();
            $table->uuid('source_file_id')->constrained('source_files')->cascadeOnDelete();
            $table->unsignedInteger('output_type_id')->constrained('output_types')->restrictOnDelete();
            $table->unsignedInteger('action_id')->constrained('automation_actions')->restrictOnDelete();
            $table->unsignedInteger('model_id')->nullable()->constrained('models')->nullOnDelete();
            $table->uuid('prompt_id')->nullable()->constrained('prompts')->nullOnDelete();

            $table->text('content_text')->nullable();
            $table->jsonb('content_json')->nullable();
            $table->text('storage_path')->nullable();
            $table->string('content_hash', 64)->nullable();

            $table->jsonb('chunk_refs')->nullable()->default('[]');
            $table->integer('token_count')->nullable();
            $table->integer('char_count')->nullable();

            $table->decimal('quality_score', 5, 2)->nullable();
            $table->boolean('is_human_approved')->default(false);
            $table->uuid('human_approved_by')->nullable();
            $table->timestamp('human_approved_at')->nullable();
            $table->text('human_notes')->nullable();

            $table->integer('version_number')->default(1);
            $table->uuid('superseded_by_id')->nullable();
            $table->boolean('is_latest')->default(true);

            $table->jsonb('processing_metadata')->nullable()->default('{}');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->index('source_file_id');
            $table->index('output_type_id');
            $table->index('action_id');
            $table->index('is_latest');
            $table->index('is_human_approved');
        });

        // Self-referencing FK must be added after the primary key exists.
        Schema::table('processed_outputs', function (Blueprint $table): void {
            $table->foreign('superseded_by_id', 'fk_processed_outputs_superseded_by')
                ->references('id')->on('processed_outputs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processed_outputs');
    }
};
