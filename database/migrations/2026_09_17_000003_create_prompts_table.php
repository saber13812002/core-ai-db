<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('family_id')->index();
            $table->string('name', 300);
            $table->string('version', 50);
            $table->text('content');
            $table->string('content_hash', 64);
            $table->string('prompt_type', 50);
            $table->text('purpose')->nullable();
            $table->foreignId('target_output_type_id')->nullable()->constrained('output_types')->nullOnDelete();
            $table->unsignedInteger('target_action_id')->nullable()->constrained('automation_actions')->nullOnDelete();
            $table->uuid('parent_prompt_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->jsonb('tags')->nullable();
            $table->jsonb('metadata')->nullable()->default('{}');
            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['family_id', 'version']);
            $table->index('prompt_type');
        });

        // Self-referencing FK must be added after the primary key exists.
        Schema::table('prompts', function (Blueprint $table): void {
            $table->foreign('parent_prompt_id', 'fk_prompts_parent')->references('id')->on('prompts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
