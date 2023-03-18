<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableToSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->change();
            $table->unsignedBigInteger('provider_id')->nullable();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->change();
            $table->unsignedBigInteger('provider_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slots', function (Blueprint $table) {
            //
        });
    }
}
