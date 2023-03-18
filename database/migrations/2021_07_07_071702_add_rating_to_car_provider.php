<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingToCarProvider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_provider', function (Blueprint $table) {
            $table->integer('rating_count')->default(0);
            $table->float('rating_avg')->default(0.0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_provider', function (Blueprint $table) {
            //
        });
    }
}
