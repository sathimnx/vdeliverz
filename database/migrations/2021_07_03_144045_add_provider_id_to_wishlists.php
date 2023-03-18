<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderIdToWishlists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->change();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
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