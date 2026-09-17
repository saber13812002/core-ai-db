<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_actions', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('name_fa', 200);
            $table->text('description')->nullable();
            $table->string('action_category', 50)->nullable();
            $table->jsonb('input_file_types');
            $table->foreignId('output_type_id')->nullable()->constrained('output_types')->nullOnDelete();
            $table->boolean('requires_prompt')->default(false);
            $table->boolean('is_batchable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_actions');
    }
};
