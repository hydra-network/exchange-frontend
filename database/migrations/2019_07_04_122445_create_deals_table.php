<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDealsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pair_id')->unsigned()->index('deals_pair_id_foreign');
			$table->integer('seller_user_id')->unsigned()->index('deals_seller_user_id_foreign');
			$table->integer('buyer_user_id')->unsigned()->index('deals_buyer_user_id_foreign');
			$table->integer('bid_id')->unsigned()->index('deals_bid_id_foreign');
			$table->integer('ask_id')->unsigned()->index('deals_ask_id_foreign');
			$table->bigInteger('quantity');
			$table->integer('price');
			$table->bigInteger('cost');
			$table->boolean('type');
			$table->timestamps();
			$table->dateTime('notify_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('deals');
	}

}
