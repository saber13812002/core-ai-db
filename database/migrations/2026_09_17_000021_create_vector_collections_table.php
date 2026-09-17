<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vector_collections', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 300);
            $table->text('description')->nullable();
            $table->string('vector_db', 50)->default('chromadb');
            $table->string('external_collection_id', 200)->nullable();
            $table->string('search_mode', 50)->default('hybrid');
            $table->jsonb('filter_criteria')->nullable()->default('{}');
            $table->string('status', 50)->default('building');
            $table->integer('total_items')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('status');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vector_collections');
    }
};
