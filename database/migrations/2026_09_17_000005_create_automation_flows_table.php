<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_flows', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 300);
            $table->string('platform', 50);
            $table->string('platform_flow_id', 200)->nullable();
            $table->text('description')->nullable();
            $table->jsonb('input_file_types')->nullable();
            $table->foreignId('output_type_id')->nullable()->constrained('output_types')->nullOnDelete();
            $table->unsignedInteger('action_id')->nullable()->constrained('automation_actions')->nullOnDelete();
            $table->jsonb('config')->nullable()->default('{}');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_flows');
    }
};
