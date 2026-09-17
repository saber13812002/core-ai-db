<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_call_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('service_id')->nullable()->constrained('service_registry')->nullOnDelete();
            $table->uuid('source_file_id')->nullable()->constrained('source_files')->nullOnDelete();
            $table->uuid('job_id')->nullable()->constrained('automation_jobs')->nullOnDelete();
            $table->string('endpoint', 500)->nullable();
            $table->string('http_method', 10)->nullable();
            $table->jsonb('request_payload')->nullable();
            $table->jsonb('response_payload')->nullable();
            $table->integer('http_status')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('source_file_id');
            $table->index('created_at');
            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_call_logs');
    }
};
