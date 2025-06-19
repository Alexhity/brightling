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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // отправитель
            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // получатель
            $table->foreignId('recipient_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('question_text');
            $table->timestamp('question_sent_at')
                ->useCurrent();
            $table->text('answer_text')->nullable();
            $table->timestamp('answer_sent_at')->nullable();

            $table->enum('status', ['pending', 'answered'])
                ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
