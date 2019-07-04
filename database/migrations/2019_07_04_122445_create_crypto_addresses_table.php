<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCryptoAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crypto_addresses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('asset_id')->unsigned()->index('addresses_currency_id_foreign');
			$table->string('label', 191)->nullable();
			$table->string('private_key', 191);
			$table->string('address');
			$table->integer('user_id')->unsigned()->index('addresses_user_id_foreign');
			$table->integer('last_check_depo')->nullable();
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
		Schema::drop('crypto_addresses');
	}

}
