<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('source_files', function (Blueprint $table): void {
            $table->uuid('metadata_schema_id')->nullable()->after('metadata');
            $table->index('metadata_schema_id');
        });

        Schema::table('source_files', function (Blueprint $table): void {
            $table->foreign('metadata_schema_id', 'fk_source_files_metadata_schema')
                ->references('id')->on('metadata_schemas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('source_files', function (Blueprint $table): void {
            $table->dropForeign(['metadata_schema_id']);
            $table->dropColumn('metadata_schema_id');
            $table->dropIndex(['metadata_schema_id']);
        });
    }
};
