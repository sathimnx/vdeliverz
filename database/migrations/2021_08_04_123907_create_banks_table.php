<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('active')->default(1);
            $table->string('bank_name');
            $table->string('acc_no');
            $table->string('branch');
            $table->string('city');
            $table->string('ifsc');
            $table->string('icon')->default('default.png');
            $table->foreignId('shop_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->foreignId('bank_id')->nullable()->constrained();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks');
    }
}