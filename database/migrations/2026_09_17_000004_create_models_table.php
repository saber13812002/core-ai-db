<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('models', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 120)->unique();
            $table->string('name_fa', 200)->nullable();
            $table->string('provider', 100)->nullable();
            $table->string('model_type', 50)->nullable();
            $table->string('version', 50)->nullable();
            $table->text('endpoint')->nullable();
            $table->integer('context_window')->nullable();
            $table->boolean('is_active')->default(true);
            $table->jsonb('metadata')->nullable()->default('{}');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};
