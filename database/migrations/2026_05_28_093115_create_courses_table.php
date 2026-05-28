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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])
                ->default('beginner');
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft');
            $table->boolean('is_free')->default(true);
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['teacher_id', 'status']);
            $table->index(['category_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
