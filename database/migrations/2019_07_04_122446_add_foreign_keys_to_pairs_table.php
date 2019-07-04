<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPairsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pairs', function(Blueprint $table)
		{
			$table->foreign('primary_asset_id', 'pairs_currency1_id_foreign')->references('id')->on('assets')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('secondary_asset_id', 'pairs_currency2_id_foreign')->references('id')->on('assets')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pairs', function(Blueprint $table)
		{
			$table->dropForeign('pairs_currency1_id_foreign');
			$table->dropForeign('pairs_currency2_id_foreign');
		});
	}

}
