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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedTinyInteger('pass_score')->default(70);
            $table->unsignedTinyInteger('max_attempts')->default(1);
            $table->boolean('is_randomized')->default(false);
            $table->boolean('show_result_immediately')->default(true);
            $table->enum('status', ['draft', 'published', 'closed'])
                ->default('draft');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
