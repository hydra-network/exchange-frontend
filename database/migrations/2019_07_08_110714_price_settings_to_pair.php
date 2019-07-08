<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PriceSettingsToPair extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pairs', function (Blueprint $table) {
            $table->bigInteger('min_trade_amount')->nullable();
            $table->bigInteger('limit_from_one_person')->nullable();
            $table->bigInteger('max_price')->nullable();
            $table->bigInteger('min_price')->nullable();
            $table->bigInteger('daily_volume_limit')->nullable();
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->removeColumn('min_trade_amount');
            $table->removeColumn('limit_from_one_person');
            $table->removeColumn('max_price');
            $table->removeColumn('min_price');
            $table->removeColumn('daily_volume_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->bigInteger('min_trade_amount')->nullable();
            $table->bigInteger('limit_from_one_person')->nullable();
            $table->bigInteger('max_price')->nullable();
            $table->bigInteger('min_price')->nullable();
            $table->bigInteger('daily_volume_limit')->nullable();
        });

        Schema::table('pairs', function (Blueprint $table) {
            $table->removeColumn('min_trade_amount');
            $table->removeColumn('limit_from_one_person');
            $table->removeColumn('max_price');
            $table->removeColumn('min_price');
            $table->removeColumn('daily_volume_limit');
        });
    }
}
