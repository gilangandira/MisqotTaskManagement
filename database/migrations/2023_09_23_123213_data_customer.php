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
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('customers_name');
            $table->string('ppoe_username');
            $table->string('ppoe_password');
            $table->string('image')->default("customer-image/1077114.png");
            $table->string('ip_client');
            $table->string('ap_ssid');
            $table->integer('channel_frequensy');
            $table->integer('bandwith');
            $table->integer('subscription_fee');
            $table->string('location');
            $table->date('start_dates');
            $table->softDeletes();
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
        Schema::dropIfExists('customer');
    }
};