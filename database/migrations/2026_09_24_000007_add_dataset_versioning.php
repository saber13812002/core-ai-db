<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Git-like dataset versioning (design doc: previous_dataset_id chain
        // so "SFT-dataset-v3" points back to v2).
        Schema::table('datasets', function (Blueprint $table): void {
            $table->integer('version_number')->default(1);
            $table->uuid('previous_dataset_id')->nullable();
            $table->index('version_number');
        });

        Schema::table('datasets', function (Blueprint $table): void {
            $table->foreign('previous_dataset_id', 'fk_datasets_previous')
                ->references('id')->on('datasets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table): void {
            $table->dropForeign(['previous_dataset_id']);
            $table->dropIndex(['version_number']);
            $table->dropColumn(['version_number', 'previous_dataset_id']);
        });
    }
};
