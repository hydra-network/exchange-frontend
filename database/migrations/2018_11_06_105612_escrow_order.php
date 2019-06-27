<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EscrowOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('escrow_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hash');
            $table->integer('order_id')->unsigned();
            $table->string('ip');
            $table->string('status');
            $table->decimal('quantity', 65, 8);
            $table->decimal('cost', 65, 8);
            $table->integer('pair_id')->unsigned();
            $table->string('user_address');
            $table->integer('deposit_address_id')->unsigned();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('escrow_deposits', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->integer('escrow_id')->unsigned();
            $table->integer('address_id')->unsigned();
            $table->string('tx_id');
            $table->integer('confirmations')->nullable();
            $table->decimal('quantity', 65, 8);
            $table->timestamps();
        });

        Schema::table('escrow_order', function (Blueprint $table) {
            $table->foreign('pair_id')->references('id')->on('pairs');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('deposit_address_id')->references('id')->on('addresses');
        });

        Schema::table('escrow_deposits', function (Blueprint $table) {
            $table->foreign('asset_id')->references('id')->on('currencies');
            $table->foreign('escrow_id')->references('id')->on('escrow_order');
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('escrow_order');
        Schema::dropIfExists('escrow_deposits');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
