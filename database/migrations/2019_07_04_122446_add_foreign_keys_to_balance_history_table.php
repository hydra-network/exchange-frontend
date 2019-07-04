<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBalanceHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('balance_history', function(Blueprint $table)
		{
			$table->foreign('asset_id', 'balance_history_currency_id_foreign')->references('id')->on('assets')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('deal_id')->references('id')->on('deals')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('deposit_id')->references('id')->on('deposits')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('order_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('withdrawal_id')->references('id')->on('withdrawals')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('balance_history', function(Blueprint $table)
		{
			$table->dropForeign('balance_history_currency_id_foreign');
			$table->dropForeign('balance_history_deal_id_foreign');
			$table->dropForeign('balance_history_deposit_id_foreign');
			$table->dropForeign('balance_history_order_id_foreign');
			$table->dropForeign('balance_history_user_id_foreign');
			$table->dropForeign('balance_history_withdrawal_id_foreign');
		});
	}

}
