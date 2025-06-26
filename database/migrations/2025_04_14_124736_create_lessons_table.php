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
            $table->string('zoom_link')->nullable();;
            $table->string('material_path')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('status');
            $table->integer('mark')->unsigned()->nullable();
            $table->foreignId('course_id')
                ->nullable()
                ->constrained('courses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
            $table->foreignId('timetable_id')
                ->nullable()
                ->constrained('timetables')
                ->nullOnDelete();
            $table->foreignId('teacher_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('SET NULL');
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
