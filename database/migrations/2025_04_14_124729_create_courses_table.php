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
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('format', ['individual','group']);
            $table->string('age_group')->nullable();
            // Убедились, что имя поля совпадает: lessons_count, а не lesson_count
            $table->integer('lessons_count')->nullable();
            $table->enum('level', [
                'beginner',
                'A1','A2',
                'B1','B2',
                'C1','C2'
            ])
                ->default('beginner');

            $table->date('duration')->nullable();
            $table->enum('status', ['recruiting', 'not_recruiting', 'completed'])->default('recruiting');

            // Внешний ключ к таблице languages
            $table->foreignId('language_id')->nullable()
                ->constrained('languages')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Внешний ключ к таблице prices (переименовали pricing_id → price_id)
            $table->foreignId('price_id')->nullable()
                ->constrained('prices')
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
        Schema::dropIfExists('courses');
    }
};
