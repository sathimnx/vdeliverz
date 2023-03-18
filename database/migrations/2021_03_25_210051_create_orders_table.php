<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('paid')->default(0);
            $table->tinyInteger('type');
            $table->string('amount');
            $table->string('currency')->default('₹');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cart_id');
            $table->json('address')->nullable();
            $table->string('payment_id')->nullable()->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->dateTime('canceled_at')->nullable();
            $table->dateTime('cancel_reason')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();
        });
        Schema::table('carts', function (Blueprint $table) {
            $table->tinyInteger('type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
