<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // job_batches now exists, so the previously-dangling batch_id column
        // can reference it (nullOnDelete: batches are reference data, jobs
        // remain usable if a batch is removed).
        Schema::table('automation_jobs', function (Blueprint $table): void {
            $table->foreign('batch_id', 'fk_automation_jobs_batch')
                ->references('id')->on('job_batches')->nullOnDelete();
        });

        Schema::table('source_files', function (Blueprint $table): void {
            $table->uuid('project_id')->nullable()->after('external_ref');
            $table->unsignedInteger('source_type_id')->nullable()->after('file_type');
            $table->index('project_id');
            $table->index('source_type_id');
        });

        Schema::table('source_files', function (Blueprint $table): void {
            $table->foreign('project_id', 'fk_source_files_project')
                ->references('id')->on('projects')->nullOnDelete();
            $table->foreign('source_type_id', 'fk_source_files_source_type')
                ->references('id')->on('source_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('source_files', function (Blueprint $table): void {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['source_type_id']);
            $table->dropIndex(['project_id']);
            $table->dropIndex(['source_type_id']);
            $table->dropColumn(['project_id', 'source_type_id']);
        });

        Schema::table('automation_jobs', function (Blueprint $table): void {
            $table->dropForeign(['batch_id']);
        });
    }
};
