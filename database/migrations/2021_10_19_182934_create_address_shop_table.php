<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_shop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('address_id');
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->float('kms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address_shop');
    }
}