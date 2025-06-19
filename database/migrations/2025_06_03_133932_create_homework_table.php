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
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->dateTime('deadline');
            $table->string('file')->nullable();
            $table->string('link')->nullable();
            $table->string('status')->default('pending');
            $table->integer('mark')->unsigned()->nullable();
            // По желанию можно сделать enum('status', ['pending','submitted','graded','rejected']), но string тоже ок.
            $table->foreignId('lesson_id')
                ->constrained('lessons')
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
        Schema::table('homeworks', function (Blueprint $table) {
            $table->dropForeign(['lesson_id']);
        });
        Schema::dropIfExists('homeworks');
    }
};
