<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->tinyInteger('active')->default(1);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade');
            $table->longText('description')->nullable();
            $table->string('currency')->nullable()->default('₹');
            $table->string('image')->default('default.png');
            $table->integer('price');
            $table->integer('available');
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
        Schema::dropIfExists('products');
    }
}
