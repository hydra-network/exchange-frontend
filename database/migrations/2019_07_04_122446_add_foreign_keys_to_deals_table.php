<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDealsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('deals', function(Blueprint $table)
		{
			$table->foreign('ask_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('bid_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('buyer_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('pair_id')->references('id')->on('pairs')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('seller_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('deals', function(Blueprint $table)
		{
			$table->dropForeign('deals_ask_id_foreign');
			$table->dropForeign('deals_bid_id_foreign');
			$table->dropForeign('deals_buyer_user_id_foreign');
			$table->dropForeign('deals_pair_id_foreign');
			$table->dropForeign('deals_seller_user_id_foreign');
		});
	}

}
