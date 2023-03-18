<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('verified')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('type_id');
            $table->tinyInteger('opened')->default(1);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('price')->nullable();
            $table->string('hour_price')->nullable();
            $table->string('job_price')->nullable();
            $table->string('min_amount')->nullable();
            $table->string('radius')->nullable();
            $table->integer('rating_count')->default(0);
            $table->string('email')->nullable();
            $table->string('rating_avg')->default(0.0);
            $table->string('currency')->nullable()->default('₹');
            $table->string('street')->nullable();
            $table->string('area')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('image')->default('default.png');
            $table->longText('description')->nullable();
            $table->bigInteger('points')->nullable()->default(0);
            $table->string('delivery_boy_charge')->nullable()->default(1);
            $table->string('delivery_charge')->nullable()->default(1);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->text('weekdays')->nullable();
            $table->string('comission')->default(1);
            $table->tinyInteger('assign')->default(1);
            $table->string('banner_image')->nullable()->default('default.png');
            $table->softDeletes();
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
        Schema::dropIfExists('providers');
    }
}
