<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pair_id')->unsigned()->index('orders_pair_id_foreign');
			$table->integer('user_id')->unsigned()->index('orders_user_id_foreign');
			$table->string('type', 191);
			$table->string('owner_type', 191)->nullable();
			$table->string('status', 191);
			$table->bigInteger('quantity');
			$table->bigInteger('quantity_remain');
			$table->bigInteger('price');
			$table->bigInteger('cost');
			$table->bigInteger('cost_remain');
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
		Schema::drop('orders');
	}

}
