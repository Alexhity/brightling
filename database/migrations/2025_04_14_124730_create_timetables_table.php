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

            $table->string('title')->nullable();

            // День недели (ENUM, чтобы не было опечаток)
            $table->enum('weekday', [
                'понедельник', 'вторник', 'среда', 'четверг',
                'пятница', 'суббота', 'воскресенье'
            ])->nullable()->comment('День недели для регулярных слотов');
            $table->date('date')->nullable()->comment('Дата для разовых слотов');
            $table->time('start_time');
            $table->integer('duration')->unsigned();
            $table->date('ends_at')->nullable()->comment('Дата окончания регулярного слота');

            // Флаги и типы
            $table->enum('type', ['group', 'individual', 'test'])->default('group');
            $table->boolean('active')->default(true);
            $table->boolean('is_public')->default(false)->comment('Виден ли слот студентам');
            $table->boolean('cancelled')->default(false)->comment('Отменен ли слот');


            // Связи
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('request_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->comment('Основной преподаватель');
            $table->foreignId('override_user_id')->nullable()->constrained('users')->comment('Заменяющий преподаватель');
            $table->foreignId('parent_id')->nullable()->constrained('timetables')->onDelete('cascade')->comment('Родительский слот');

            // Стандартные поля
            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index('date');
            $table->index('weekday');
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
