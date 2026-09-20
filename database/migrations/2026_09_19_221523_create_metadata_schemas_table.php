<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metadata_schemas', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 200)->unique();
            $table->string('scope', 50)->default('global');
            $table->jsonb('schema')->nullable()->default('{}');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('scope');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metadata_schemas');
    }
};
