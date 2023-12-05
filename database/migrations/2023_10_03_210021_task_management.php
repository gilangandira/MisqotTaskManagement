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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('status_id')->default(1);
            $table->unsignedBigInteger('assets_id');
            $table->unsignedBigInteger('sla_id')->default(1);
            $table->unsignedBigInteger('timetracker_id')->default(1);
            $table->string('name');
           
            $table->text('description');
            $table->dateTime('dates')->default(now());
            $table->string('location');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('status_id')->references('id')->on('status')->onDelete('cascade');
            $table->foreign('assets_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('sla_id')->references('id')->on('sla')->onDelete('cascade');
            $table->foreign('timetracker_id')->references('id')->on('timetracker')->onDelete('cascade');    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};