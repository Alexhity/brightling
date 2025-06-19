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
        Schema::table('timetables', function (Blueprint $table) {
            $table->foreign('request_id')
                ->references('id')
                ->on('free_lesson_requests')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['request_id']);
        });
    }
};
