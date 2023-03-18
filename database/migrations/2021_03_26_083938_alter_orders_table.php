<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('delivered_at')->nullable();
            $table->string('prefix')->nullable()->default('058454');

        });

        Schema::table('carts', function (Blueprint $table) {
            $table->string('tax')->nullable()->default('0');
            $table->string('coupon_amount')->nullable()->default('0')->change();
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
