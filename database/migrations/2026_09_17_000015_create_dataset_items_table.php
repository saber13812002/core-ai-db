<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dataset_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('dataset_id')->constrained('datasets')->cascadeOnDelete();
            $table->uuid('source_file_id')->constrained('source_files')->cascadeOnDelete();

            $table->uuid('input_output_id')->nullable()->constrained('processed_outputs')->nullOnDelete();
            $table->uuid('input_cleaned_id')->nullable()->constrained('cleaned_outputs')->nullOnDelete();
            $table->uuid('ground_truth_id')->nullable()->constrained('human_ground_truth')->nullOnDelete();

            $table->string('split', 20);
            $table->integer('sequence_order')->nullable();
            $table->jsonb('metadata')->nullable()->default('{}');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['dataset_id', 'split']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dataset_items');
    }
};
