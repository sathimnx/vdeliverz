<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarProviderToWishlists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->unsignedBigInteger('car_provider_id')->nullable();
            $table->foreign('car_provider_id')->references('id')->on('car_provider')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wishlists', function (Blueprint $table) {
            //
        });
    }
}
