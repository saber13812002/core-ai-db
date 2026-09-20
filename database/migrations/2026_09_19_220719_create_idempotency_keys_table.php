<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idempotency_keys', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->uuid('api_key_id')->nullable()->index();
            $table->string('key', 128);
            $table->string('route_path', 100);
            $table->uuid('reference_id');
            $table->smallInteger('response_status');
            $table->timestamp('expires_at');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['api_key_id', 'key', 'route_path']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
