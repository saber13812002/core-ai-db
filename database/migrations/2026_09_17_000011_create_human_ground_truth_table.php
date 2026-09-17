<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('human_ground_truth', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('source_file_id')->constrained('source_files')->cascadeOnDelete();
            $table->unsignedInteger('output_type_id')->constrained('output_types')->restrictOnDelete();
            $table->unsignedInteger('action_id')->nullable()->constrained('automation_actions')->nullOnDelete();

            $table->text('content_text')->nullable();
            $table->jsonb('content_json')->nullable();
            $table->text('storage_path')->nullable();

            $table->uuid('approved_by');
            $table->timestamp('approved_at')->useCurrent();
            $table->text('approval_notes')->nullable();
            $table->string('confidence_level', 20)->nullable();

            $table->uuid('based_on_output_id')->nullable()->constrained('processed_outputs')->nullOnDelete();
            $table->uuid('based_on_cleaned_id')->nullable()->constrained('cleaned_outputs')->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('source_file_id');
            $table->index('output_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('human_ground_truth');
    }
};
