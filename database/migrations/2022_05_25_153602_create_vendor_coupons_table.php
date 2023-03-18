<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_code')->unique();
            $table->dateTime('expired_on');
            $table->integer('max_order_amount');
            $table->string('coupon_percentage')->nullable();
            $table->string('symbol')->nullable()->default('%');
            $table->longText('coupon_description')->nullable();
            $table->integer('Discount_use_amt')->nullable();
            $table->integer('min_order_amt')->nullable();
            $table->integer('shop_id')->nullable();
            $table->longText('sub_category_id')->nullable();
            $table->longText('product_id')->nullable();
            $table->longText('sub_category_name')->nullable();
            $table->longText('product_name')->nullable();
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('vendor_coupons');
    }
}
