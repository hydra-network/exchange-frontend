<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->string('type', 191);
			$table->text('description', 65535);
			$table->string('code', 191);
			$table->string('icon', 191)->nullable();
			$table->integer('status')->nullable();
			$table->string('address_example', 191);
			$table->integer('min_confirmations');
			$table->decimal('min_withdrawal_amount', 65, 8)->nullable();
			$table->bigInteger('min_trade_amount')->nullable();
			$table->bigInteger('withdrawal_fees')->nullable();
			$table->bigInteger('exchange_fees')->nullable();
			$table->bigInteger('limit_from_one_person')->nullable();
			$table->bigInteger('max_price')->nullable();
			$table->bigInteger('min_price')->nullable();
			$table->bigInteger('daily_volume_limit')->nullable();
			$table->integer('subunits')->nullable();
			$table->integer('round')->nullable();
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
		Schema::drop('assets');
	}

}
