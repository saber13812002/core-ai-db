<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();   // audio, video, pdf, docx, xlsx, pptx, image, text
            $table->string('label_fa', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_types');
    }
};
