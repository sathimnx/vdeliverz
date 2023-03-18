<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeekdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekdays', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->time('opens_at');
            $table->time('closes_at');
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
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
        Schema::dropIfExists('weekdays');
    }
}
