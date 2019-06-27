<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Init extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->string('label')->nullable();
            $table->string('private_key');
            $table->string('addres');
            $table->integer('user_id')->unsigned();
            $table->integer('last_check_depo')->nullable();
            $table->timestamps();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->text('description');
            $table->string('code');
            $table->string('icon')->nullable();
            $table->integer('status')->nullable();
            $table->string('address_example');
            $table->integer('min_confirmations');
            $table->decimal('min_withdrawal_amount', 65, 8)->nullable();
            $table->decimal('min_trade_amount', 65, 8)->nullable();
            $table->decimal('withdrawal_fees', 65, 8)->nullable();
            $table->decimal('exchange_fees', 65, 8)->nullable();
            $table->timestamps();
        });

        Schema::create('node_servers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->string('base_url');
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('pairs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code');
            $table->string('status');
            $table->integer('primary_asset_id')->unsigned();
            $table->integer('secondary_asset_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('deposits', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('address_id')->unsigned();
            $table->string('tx_id');
            $table->integer('status')->nullable();
            $table->integer('confirmations')->nullable();
            $table->decimal('quantity', 65, 8);
            $table->timestamps();
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('address');
            $table->string('tx_id');
            $table->string('ip');
            $table->integer('status')->nullable();
            $table->integer('confirmations')->nullable();
            $table->decimal('quantity', 65, 8);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('pair_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('type');
            $table->string('owner_type');
            $table->string('status');
            $table->decimal('quantity', 65, 8);
            $table->decimal('quantity_remain', 65, 8);
            $table->decimal('price', 65, 8);
            $table->decimal('cost', 65, 8);
            $table->decimal('cost_remain', 65, 8);
            $table->timestamps();
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('pair_id')->unsigned();
            $table->integer('seller_user_id')->unsigned();
            $table->integer('buyer_user_id')->unsigned();
            $table->integer('bid_id')->unsigned();
            $table->integer('ask_id')->unsigned();
            $table->decimal('quantity', 65, 8);
            $table->decimal('price', 65, 8);
            $table->decimal('cost', 65, 8);
            $table->timestamps();
        });

        Schema::create('balance_history', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type');
            $table->integer('asset_id')->unsigned();
            $table->decimal('quantity', 65, 8);
            $table->integer('user_id')->unsigned();
            $table->integer('order_id')->nullable()->unsigned();
            $table->integer('deal_id')->nullable()->unsigned();
            $table->integer('deposit_id')->nullable()->unsigned();
            $table->integer('withdrawal_id')->nullable()->unsigned();
            $table->decimal('balance', 65, 8);
            $table->timestamps();
        });

        Schema::create('ticks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('pair_id')->nullable()->unsigned();
            $table->decimal('min', 65, 8)->nullable();
            $table->decimal('max', 65, 8)->nullable();
            $table->decimal('avg', 65, 8)->nullable();
            $table->timestamps();
        });
        
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->foreign('asset_id')->references('id')->on('currencies');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->foreign('asset_id')->references('id')->on('currencies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('address_id')->references('id')->on('addresses');
        });

        Schema::table('pairs', function (Blueprint $table) {
            $table->foreign('primary_asset_id')->references('id')->on('currencies');
            $table->foreign('secondary_asset_id')->references('id')->on('currencies');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('asset_id')->references('id')->on('currencies');
        });

        Schema::table('node_servers', function (Blueprint $table) {
            $table->foreign('asset_id')->references('id')->on('currencies');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('pair_id')->references('id')->on('pairs');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('ticks', function (Blueprint $table) {
            $table->foreign('pair_id')->references('id')->on('pairs');
        });
        
        Schema::table('deals', function (Blueprint $table) {
            $table->foreign('bid_id')->references('id')->on('orders');
            $table->foreign('ask_id')->references('id')->on('orders');
            $table->foreign('pair_id')->references('id')->on('pairs');
            $table->foreign('seller_user_id')->references('id')->on('users');
            $table->foreign('buyer_user_id')->references('id')->on('users');
        });

        Schema::table('balance_history', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('deal_id')->references('id')->on('deals');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('deposit_id')->references('id')->on('deposits');
            $table->foreign('withdrawal_id')->references('id')->on('withdrawals');
            $table->foreign('asset_id')->references('id')->on('currencies');
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
        Schema::dropIfExists('balance_history');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('node_servers');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('pairs');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('ticks');
        Schema::dropIfExists('balance_history');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
