<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWithdrawalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('withdrawals', function(Blueprint $table)
		{
			$table->foreign('asset_id', 'withdrawals_currency_id_foreign')->references('id')->on('assets')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('withdrawals', function(Blueprint $table)
		{
			$table->dropForeign('withdrawals_currency_id_foreign');
			$table->dropForeign('withdrawals_user_id_foreign');
		});
	}

}
