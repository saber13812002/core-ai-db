<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_releases', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('trained_model_id')->constrained('trained_models')->cascadeOnDelete();
            $table->string('version', 50);

            $table->string('status', 50)->default('draft');

            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->text('release_notes')->nullable();
            $table->jsonb('performance_summary')->nullable()->default('{}');

            $table->timestamp('deployed_at')->nullable();
            $table->timestamp('retired_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_releases');
    }
};
