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
        Schema::create('free_lesson_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->enum('status', ['new', 'in_processing', 'approved', 'rejected'])->default('new');
            $table->enum('requested_role', ['student', 'teacher', 'admin'])->default('student');
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time')->nullable();
            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_lesson_requests');
    }
};
