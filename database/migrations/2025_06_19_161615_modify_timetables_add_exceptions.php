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
        Schema::table('timetables', function (Blueprint $t) {
            // Удаляем старое не нужное поле
            if (Schema::hasColumn('timetables', 'enrollment_status')) {
                $t->dropColumn('enrollment_status');
            }

            // Добавляем parent_id
            $t->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('timetables')
                ->nullOnDelete();

            // Добавляем date окончания серии
            $t->date('ends_at')
                ->nullable()
                ->after('weekday');

            // Поля переопределения для исключений
            $t->time('override_start_time')
                ->nullable()
                ->after('duration');
            $t->integer('override_duration')
                ->unsigned()
                ->nullable()
                ->after('override_start_time');
            $t->foreignId('override_user_id')
                ->nullable()
                ->after('override_duration')
                ->constrained('users')
                ->nullOnDelete();

            // Флаг отмены конкретного занятия
            $t->boolean('cancelled')
                ->default(false)
                ->after('override_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $t) {
            // Удаляем старое не нужное поле
            if (Schema::hasColumn('timetables', 'enrollment_status')) {
                $t->dropColumn('enrollment_status');
            }

            // Добавляем parent_id
            $t->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('timetables')
                ->nullOnDelete();

            // Добавляем date окончания серии
            $t->date('ends_at')
                ->nullable()
                ->after('weekday');

            // Поля переопределения для исключений
            $t->time('override_start_time')
                ->nullable()
                ->after('duration');
            $t->integer('override_duration')
                ->unsigned()
                ->nullable()
                ->after('override_start_time');
            $t->foreignId('override_user_id')
                ->nullable()
                ->after('override_duration')
                ->constrained('users')
                ->nullOnDelete();

            // Флаг отмены конкретного занятия
            $t->boolean('cancelled')
                ->default(false)
                ->after('override_user_id');
        });
    }
};
