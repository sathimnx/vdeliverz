<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEarnedInDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('amount_earned')->nullable();
            $table->tinyInteger('delivery_type')->nullable();
            $table->string('points_earned')->nullable();
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('amount_earned')->nullable();
            $table->tinyInteger('delivery_type')->nullable();
            $table->string('points_earned')->nullable();
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->string('delivery_boy_charge')->nullable()->default(1);
            $table->string('delivery_charge')->nullable()->default(1);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('delivery_type')->nullable();
            $table->string('amount_earned')->nullable();
            $table->string('points_earned')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            //
        });
    }
}
