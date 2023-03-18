<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarIdToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('car_id')->nullable();
            $table->unsignedBigInteger('slot_id')->nullable()->change();
            $table->unsignedBigInteger('sub_service_id')->nullable()->change();
            $table->string('service_name')->nullable()->change();
            $table->string('car_name')->nullable();
            $table->json('car_details')->nullable();
            $table->dateTime('booked_at')->nullable()->change();
            $table->time('from')->nullable()->change();
            $table->time('to')->nullable()->change();
            $table->dateTime('pick_up')->nullable();
            $table->dateTime('drop_off')->nullable();
            $table->tinyInteger('pick_type')->nullable();
            $table->string('address_proof')->nullable();
            $table->string('id_proof')->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->integer('order')->unique()->nullable();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('order')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // $table->dropColumn('column');
        });
    }
}
