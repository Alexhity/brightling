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
        Schema::create('language_user', function (Blueprint $table) {
            $table->id();
            $table->enum('level', [
                'beginner','A1','A2','B1','B2','C1','C2'
            ])->nullable();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
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
        Schema::table('language_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['language_id']);
        });
        Schema::dropIfExists('user_languages');
    }
};
