<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChargesToChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->integer('basic_charge')->nullable();
            $table->integer('extra_charge')->nullable();
            $table->float('basic_km')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charges', function (Blueprint $table) {
            //
        });
    }
}
