<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetracker', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_time')->default(now());
            $table->timestamp('end_time')->default(now());
            $table->dateTime('due_dates');
            $table->integer('time_track')->default(0);
            $table->integer('timer')->default(0);
            $table->boolean('runing_time?')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetracker');
    }
};
