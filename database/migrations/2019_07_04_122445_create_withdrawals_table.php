<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWithdrawalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('withdrawals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('asset_id')->unsigned()->index('withdrawals_currency_id_foreign');
			$table->integer('user_id')->unsigned()->index('withdrawals_user_id_foreign');
			$table->string('address', 191);
			$table->string('tx_id', 191);
			$table->string('ip', 191);
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
		Schema::drop('withdrawals');
	}

}
