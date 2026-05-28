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
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('selected_option_id')
                ->nullable()
                ->constrained('question_options')
                ->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->unsignedInteger('score_given')->default(0);
            $table->text('teacher_feedback')->nullable();
            $table->timestamps();

            $table->unique(['exam_session_id', 'question_id']);
            $table->index(['exam_session_id', 'is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
