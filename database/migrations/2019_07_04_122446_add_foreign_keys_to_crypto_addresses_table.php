<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCryptoAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('crypto_addresses', function(Blueprint $table)
		{
			$table->foreign('asset_id', 'addresses_currency_id_foreign')->references('id')->on('assets')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('user_id', 'addresses_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('crypto_addresses', function(Blueprint $table)
		{
			$table->dropForeign('addresses_currency_id_foreign');
			$table->dropForeign('addresses_user_id_foreign');
		});
	}

}
