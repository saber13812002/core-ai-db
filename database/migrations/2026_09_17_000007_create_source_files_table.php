<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_files', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('external_ref', 100)->nullable()->unique();

            $table->string('file_type', 50);
            $table->string('original_filename', 500)->nullable();
            $table->text('storage_path');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->string('checksum_sha256', 64)->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('page_count')->nullable();
            $table->string('language', 10)->default('fa');

            $table->jsonb('metadata')->nullable()->default('{}');

            $table->boolean('human_approved')->default(false);
            $table->uuid('human_approved_by')->nullable();
            $table->timestamp('human_approved_at')->nullable();
            $table->text('human_approval_note')->nullable();

            $table->string('processing_status', 50)->default('pending');

            $table->integer('version_number')->default(1);
            $table->uuid('superseded_by_id')->nullable();
            $table->boolean('is_latest')->default(true);
            $table->timestamp('deleted_at')->nullable();

            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('file_type');
            $table->index('processing_status');
            $table->index('human_approved');
            $table->index('is_latest');
        });

        // Self-referencing FK must be added after the primary key exists.
        Schema::table('source_files', function (Blueprint $table): void {
            $table->foreign('superseded_by_id', 'fk_source_files_superseded_by')
                ->references('id')->on('source_files')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_files');
    }
};
