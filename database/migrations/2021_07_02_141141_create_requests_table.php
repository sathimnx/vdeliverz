<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->integer('pay_status')->default(0);
            $table->unsignedBigInteger('accepted_by')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->longText('accept_ins')->nullable();
            $table->longText('complete_ins')->nullable();
            $table->longText('request_ins')->nullable();
            $table->string('total');
            $table->string('com');
            $table->string('charge');
            $table->longText('order_ids');
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('accepted_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('pay_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
