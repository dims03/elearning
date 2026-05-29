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
        Schema::table('questions', function (Blueprint $table) {
            if (! Schema::hasColumn('questions', 'exam_id')) {
                $table->foreignId('exam_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('exams')
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('questions', 'question_text')) {
                $table->text('question_text')->nullable()->after('exam_id');
            }

            if (! Schema::hasColumn('questions', 'type')) {
                $table->enum('type', ['multiple_choice', 'true_false', 'essay'])
                    ->default('multiple_choice')
                    ->after('question_text');
            }

            if (! Schema::hasColumn('questions', 'points')) {
                $table->unsignedInteger('points')->default(1)->after('type');
            }

            if (! Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('order');
            }

            if (! Schema::hasColumn('questions', 'image')) {
                $table->string('image')->nullable()->after('explanation');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            $sm = Schema::getConnection()->getSchemaBuilder();

            if (! $sm->hasIndex('questions', ['exam_id', 'type'])) {
                $table->index(['exam_id', 'type']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'image')) {
                $table->dropColumn('image');
            }

            if (Schema::hasColumn('questions', 'explanation')) {
                $table->dropColumn('explanation');
            }

            if (Schema::hasColumn('questions', 'points')) {
                $table->dropColumn('points');
            }

            if (Schema::hasColumn('questions', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('questions', 'question_text')) {
                $table->dropColumn('question_text');
            }

            if (Schema::hasColumn('questions', 'exam_id')) {
                $table->dropConstrainedForeignId('exam_id');
            }
        });
    }
};
