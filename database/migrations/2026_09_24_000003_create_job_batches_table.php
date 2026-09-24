<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_batches', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 300)->nullable();
            $table->uuid('master_prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->timestamp('scheduled_for')->nullable();

            $table->unsignedBigInteger('estimated_total_tokens')->nullable();
            $table->integer('estimated_duration_seconds')->nullable();
            $table->unsignedBigInteger('actual_total_tokens')->nullable();
            $table->integer('actual_duration_seconds')->nullable();

            $table->string('status', 50)->default('queued');
            $table->string('triggered_by', 100)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_batches');
    }
};
