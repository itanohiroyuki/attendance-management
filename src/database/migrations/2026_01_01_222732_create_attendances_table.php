<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('work_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->time('break1_start_time')->nullable();
            $table->time('break1_end_time')->nullable();
            $table->time('break2_start_time')->nullable();
            $table->time('break2_end_time')->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('break_minutes')->default(0);
            $table->unsignedInteger('work_minutes')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
