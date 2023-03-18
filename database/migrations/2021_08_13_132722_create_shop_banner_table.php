<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_banners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->string('image')->default('default.png');
            $table->string('slogan')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->integer('order')->nullable()->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('banner')->default('default.png');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_banner');
    }
}
