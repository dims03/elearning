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
        $schema = Schema::getConnection()->getSchemaBuilder();

        if (Schema::hasColumn('questions', 'question_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropForeign(['question_id']);
            });
        }

        if ($schema->hasIndex('questions', ['question_id', 'is_correct'])) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropIndex('questions_question_id_is_correct_index');
            });
        }

        Schema::table('questions', function (Blueprint $table) {
            $columnsToDrop = [];

            foreach (['question_id', 'option_text', 'is_correct'] as $column) {
                if (Schema::hasColumn('questions', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (! Schema::hasColumn('questions', 'question_id')) {
                $table->foreignId('question_id')
                    ->nullable()
                    ->after('points')
                    ->constrained('questions')
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('questions', 'option_text')) {
                $table->text('option_text')->nullable()->after('question_id');
            }

            if (! Schema::hasColumn('questions', 'is_correct')) {
                $table->boolean('is_correct')->default(false)->after('option_text');
            }
        });

        $schema = Schema::getConnection()->getSchemaBuilder();

        if (! $schema->hasIndex('questions', ['question_id', 'is_correct'])) {
            Schema::table('questions', function (Blueprint $table) {
                $table->index(['question_id', 'is_correct']);
            });
        }
    }
};
