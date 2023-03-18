<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableForToppings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('toppings', function (Blueprint $table) {
            $table->unsignedBigInteger('title_id');
            $table->integer('available');
            $table->string('title_name');
            $table->string('variety');
            $table->foreign('title_id')->references('id')->on('titles')->onDelete('cascade');
        });
        Schema::table('cart_products', function (Blueprint $table) {
            $table->json('toppings')->nullable();
        });
        Schema::table('shops', function (Blueprint $table) {
            $table->tinyInteger('assign')->default(1);
        });
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('size')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
