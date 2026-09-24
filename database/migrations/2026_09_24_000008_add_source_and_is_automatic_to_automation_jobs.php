<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks where a job was sent from and whether it was automatic or manual —
 * a standing requirement of the domain design (acceptance-delivery-plan §4).
 * delivery plan §4: automation_jobs: + source (20) default api, + is_automatic bool default false.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_jobs', function (Blueprint $table): void {
            $table->string('source', 20)->default('api')->after('created_at');
            $table->boolean('is_automatic')->default(false)->after('source');

            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::table('automation_jobs', function (Blueprint $table): void {
            $table->dropIndex(['source']);
            $table->dropColumn(['source', 'is_automatic']);
        });
    }
};
