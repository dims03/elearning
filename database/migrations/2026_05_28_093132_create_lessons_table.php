<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->enum('type', ['video', 'pdf', 'text', 'quiz'])
                ->default('text');
            $table->string('video_url')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();

            $table->index(['chapter_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
