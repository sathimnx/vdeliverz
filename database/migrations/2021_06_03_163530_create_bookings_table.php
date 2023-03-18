<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('device_id')->nullable();
            $table->unsignedBigInteger('provider_id');
            // 1 for successfully booked
            // 2 for booking accepted
            // 3 for booking assigned
            // 4 for booking rejected by vendor
            // 5 for booking canceled by customer
            // 6 for booking Completed
            $table->integer('status')->default(0);
            $table->date('booked_at');
            $table->unsignedBigInteger('slot_id');
            $table->time('from');
            $table->time('to');
            $table->tinyInteger('payment')->default(0);
            $table->tinyInteger('paid')->default(0);
            $table->string('total_amount');
            $table->string('payable_amount');
            $table->string('amount_paid')->nullable();
            $table->string('taxes')->nullable();
            $table->string('travel_charge')->nullable();
            $table->string('coupon_amount')->nullable();
            $table->string('currency')->default('₹');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('charge');
            $table->tinyInteger('type');
            $table->json('provider_details');
            $table->json('address_details')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->longText('instructions')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
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
        Schema::dropIfExists('bookings');
    }
}
