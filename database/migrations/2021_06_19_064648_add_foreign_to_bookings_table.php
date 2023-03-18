<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('car_provider_id')->nullable();
            $table->unsignedBigInteger('provider_sub_service_id')->nullable();
            $table->foreign('car_provider_id')->references('id')->on('car_provider')->onDelete('set null');
            $table->foreign('provider_sub_service_id')->references('id')->on('provider_sub_service')->onDelete('set null');
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
            //
        });
    }
}
