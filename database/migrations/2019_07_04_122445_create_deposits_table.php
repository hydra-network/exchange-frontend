<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepositsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deposits', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('asset_id')->unsigned()->index('deposits_currency_id_foreign');
			$table->integer('user_id')->unsigned()->index('deposits_user_id_foreign');
			$table->integer('address_id')->unsigned()->index('deposits_address_id_foreign');
			$table->string('tx_id', 191);
			$table->integer('status')->nullable();
			$table->integer('confirmations')->nullable();
			$table->bigInteger('quantity');
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
		Schema::drop('deposits');
	}

}
