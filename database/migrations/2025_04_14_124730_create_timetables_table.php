<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();

            // День недели (ENUM, чтобы не было опечаток)
            $table->enum('weekday', [
                'понедельник', 'вторник', 'среда', 'четверг',
                'пятница', 'суббота', 'воскресенье'
            ]);

            // Поле date: для одноразовых слотов
            $table->date('date')->nullable();

            // Время начала занятия
            $table->time('start_time');

            // Длительность занятия (в минутах)
            $table->integer('duration')->unsigned();

            // Тип занятия: групповой, индивидуальный или бесплатный слот
            $table->enum('type', ['group', 'individual', 'free'])
                ->default('group');

            // Активен ли этот слот (если Нужно выключить временно)
            $table->boolean('active')->default(true);

            $table->dateTime('enrollment_status')->nullable(); // удалить

            // Если это расписание для конкретного курса – ссылка на courses.id
            $table->foreignId('course_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            // Какому пользователю (преподавателю) принадлежит слот
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('request_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
