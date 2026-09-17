<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 300);
            $table->text('description')->nullable();
            $table->string('purpose', 50);
            $table->string('target_model_type', 50)->nullable();
            $table->unsignedInteger('target_output_type_id')->nullable()->constrained('output_types')->nullOnDelete();

            $table->jsonb('filter_criteria')->nullable()->default('{}');

            $table->string('status', 50)->default('building');

            $table->integer('total_items')->default(0);
            $table->integer('train_count')->default(0);
            $table->integer('validation_count')->default(0);
            $table->integer('test_count')->default(0);

            $table->text('storage_path')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};
