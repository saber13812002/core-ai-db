<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('release_reports', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('release_id')->nullable()->constrained('model_releases')->nullOnDelete();
            $table->string('report_type', 50)->nullable();
            $table->jsonb('content');
            $table->text('storage_path')->nullable();
            $table->timestamp('generated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('release_reports');
    }
};
