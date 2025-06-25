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
        Schema::table('lessons', function (Blueprint $table) {
            // Делаем course_id nullable
            $table->dropForeign(['course_id']);
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();

            // Добавляем поле type
            $table->enum('type', ['group', 'individual', 'test'])->default('group')->after('status');

            // Добавляем связь на родительский урок
            $table->unsignedBigInteger('parent_lesson_id')->nullable()->after('type');
            $table->foreign('parent_lesson_id')->references('id')->on('lessons')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['parent_lesson_id']);
            $table->dropColumn('parent_lesson_id');

            $table->dropColumn('type');

            $table->dropForeign(['course_id']);
            $table->unsignedBigInteger('course_id')->change(); // возвращаем NOT NULL
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });
    }
};
