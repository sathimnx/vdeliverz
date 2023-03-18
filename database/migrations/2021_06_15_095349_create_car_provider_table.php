<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_provider', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('car_id');
            $table->string('image')->default('default.png');
            $table->string('hour');
            $table->string('week');
            $table->string('month');
            $table->json('about')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
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
        Schema::dropIfExists('car_provider');
    }
}
