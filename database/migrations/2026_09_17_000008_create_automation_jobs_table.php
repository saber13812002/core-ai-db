<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_jobs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('batch_id')->nullable();

            $table->uuid('source_file_id')->constrained('source_files')->cascadeOnDelete();
            $table->unsignedInteger('action_id')->constrained('automation_actions')->restrictOnDelete();
            $table->uuid('flow_id')->nullable()->constrained('automation_flows')->nullOnDelete();
            $table->unsignedInteger('model_id')->nullable()->constrained('models')->nullOnDelete();
            $table->uuid('prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->uuid('cleaning_prompt_id')->nullable()->constrained('prompts')->nullOnDelete();

            $table->uuid('parent_job_id')->nullable();
            $table->string('rerun_reason', 50)->nullable();
            $table->uuid('rerun_of_job_id')->nullable();

            $table->string('status', 50)->default('queued');
            $table->integer('priority')->default(5);
            $table->integer('progress_percent')->default(0);

            $table->decimal('estimated_cost_usd', 10, 4)->nullable();
            $table->integer('estimated_duration_sec')->nullable();
            $table->decimal('actual_cost_usd', 10, 4)->nullable();
            $table->integer('actual_duration_sec')->nullable();
            $table->integer('token_count')->nullable();

            $table->unsignedInteger('service_id')->nullable()->constrained('service_registry')->nullOnDelete();
            $table->string('external_job_id', 200)->nullable();
            $table->jsonb('request_payload')->nullable();
            $table->jsonb('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);

            $table->timestamp('queued_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->uuid('created_by')->nullable();

            $table->index('source_file_id');
            $table->index('status');
            $table->index('batch_id');
            $table->index('action_id');
            $table->index('prompt_id');
            $table->index('parent_job_id');
        });

        // Self-referencing FKs must be added after the primary key exists.
        Schema::table('automation_jobs', function (Blueprint $table): void {
            $table->foreign('parent_job_id', 'fk_automation_jobs_parent')
                ->references('id')->on('automation_jobs')->nullOnDelete();
            $table->foreign('rerun_of_job_id', 'fk_automation_jobs_rerun_of')
                ->references('id')->on('automation_jobs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_jobs');
    }
};
