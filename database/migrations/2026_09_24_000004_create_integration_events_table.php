<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('service_id')->nullable()->constrained('service_registry')->nullOnDelete();

            // Polymorphic-style reference kept referentially honest:
            // (reference_type, reference_id) always names one row of a main table.
            $table->string('reference_type', 50);   // source_file, automation_job, processed_output, cleaned_output, dataset, trained_model, job_batch
            $table->uuid('reference_id');
            $table->string('external_job_id', 200)->nullable();

            $table->string('event_type', 50);       // status_update, completed, failed, started, ...
            $table->jsonb('payload')->nullable()->default('{}');

            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['reference_type', 'reference_id']);
            $table->index('event_type');
            $table->index('received_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_events');
    }
};
