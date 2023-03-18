<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('verified')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('type_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->integer('price')->nullable();
            $table->integer('rating_count')->default(0);
            $table->float('rating_avg')->default(0.0);
            $table->string('currency')->nullable()->default('₹');
            $table->string('street')->nullable();
            $table->string('area')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('image')->default('default.png');
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('shops');
    }
}
