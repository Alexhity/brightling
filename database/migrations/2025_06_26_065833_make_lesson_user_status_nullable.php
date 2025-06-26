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
        Schema::table('lesson_user', function (Blueprint $table) {
            // Делаем поле status_nullable
            $table->enum('status', ['present','absent'])
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_user', function (Blueprint $table) {
            // Возвращаем прежнее состояние: не nullable, default = 'present'
            $table->enum('status', ['present','absent'])
                ->nullable(false)
                ->default('present')
                ->change();
        });
    }
};
