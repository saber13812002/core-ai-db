<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vector_collection_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('vector_collection_id')->constrained('vector_collections')->cascadeOnDelete();
            $table->uuid('source_file_id')->constrained('source_files')->cascadeOnDelete();
            $table->uuid('processed_output_id')->nullable()->constrained('processed_outputs')->nullOnDelete();
            $table->uuid('cleaned_output_id')->nullable()->constrained('cleaned_outputs')->nullOnDelete();
            $table->string('external_vector_id', 200)->nullable();
            $table->jsonb('chunk_ref')->nullable();
            $table->jsonb('metadata')->nullable()->default('{}');
            $table->timestamp('created_at')->useCurrent();

            $table->index('vector_collection_id');
            $table->index('source_file_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vector_collection_items');
    }
};
