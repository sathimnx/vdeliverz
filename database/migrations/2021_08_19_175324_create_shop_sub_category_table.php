<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopSubCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_sub_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->integer('order')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade');
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
        Schema::dropIfExists('shop_sub_category');
    }
}