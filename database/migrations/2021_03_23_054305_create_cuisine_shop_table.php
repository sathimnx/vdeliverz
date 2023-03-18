<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuisineShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuisine_shop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cusine_id');
            $table->unsignedBigInteger('shop_id');
            $table->foreign('cusine_id')->references('id')->on('cuisines')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
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
        Schema::dropIfExists('cuisine_shop');
    }
}
