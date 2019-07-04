<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDepositsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('deposits', function(Blueprint $table)
		{
			$table->foreign('address_id')->references('id')->on('crypto_addresses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('asset_id', 'deposits_currency_id_foreign')->references('id')->on('assets')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('deposits', function(Blueprint $table)
		{
			$table->dropForeign('deposits_address_id_foreign');
			$table->dropForeign('deposits_currency_id_foreign');
			$table->dropForeign('deposits_user_id_foreign');
		});
	}

}
