<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponIdToCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->json('coupon_details')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
        });

        Schema::table('cart_products', function (Blueprint $table) {
            $table->json('product_details')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            //
        });
    }
}
