<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('output_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('name_fa', 200);
            $table->text('description')->nullable();
            $table->string('output_format', 50)->nullable();
            $table->jsonb('json_schema')->nullable();
            $table->boolean('supports_chunk')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('output_types');
    }
};
