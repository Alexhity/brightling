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
        Schema::table('timetables', function (Blueprint $table) {
            // Убираем текущее ограничение FK (если оно было)
            $table->dropForeign(['user_id']);
            // Делаем столбец user_id unsignedBigInteger nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
            // Снова вешаем внешний ключ, но уже с nullOnDelete()
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Удаляем FK
            $table->dropForeign(['user_id']);
            // Делаем столбец user_id снова not nullable
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            // Вешаем FK с cascadeOnDelete (раньше было)
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
