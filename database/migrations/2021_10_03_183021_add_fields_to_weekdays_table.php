<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToWeekdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekdays', function (Blueprint $table) {
            $table->dropForeign('weekdays_provider_id_foreign');
            $table->dropForeign('weekdays_shop_id_foreign');
            $table->dropColumn('shop_id');
            $table->dropColumn('day');
            $table->dropColumn('opens_at');
            $table->dropColumn('closes_at');
            $table->dropColumn('provider_id');
            $table->string('name')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weekdays', function (Blueprint $table) {
            //
        });
    }
}
