<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBalanceHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('balance_history', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type', 191);
			$table->integer('asset_id')->unsigned()->index('balance_history_currency_id_foreign');
			$table->bigInteger('quantity')->nullable();
			$table->integer('user_id')->unsigned()->index('balance_history_user_id_foreign');
			$table->integer('order_id')->unsigned()->nullable()->index('balance_history_order_id_foreign');
			$table->integer('deal_id')->unsigned()->nullable()->index('balance_history_deal_id_foreign');
			$table->integer('deposit_id')->unsigned()->nullable()->index('balance_history_deposit_id_foreign');
			$table->integer('withdrawal_id')->unsigned()->nullable()->index('balance_history_withdrawal_id_foreign');
			$table->bigInteger('balance');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('balance_history');
	}

}
