<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountToSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->integer('count')->nullable()->default(0);
            $table->integer('available')->nullable()->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->dropColumn(['count', 'available']);
        });
       
    }
}
