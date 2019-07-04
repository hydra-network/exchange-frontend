<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePairsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pairs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191);
			$table->string('status', 191);
			$table->integer('primary_asset_id')->unsigned()->index('pairs_currency1_id_foreign');
			$table->integer('secondary_asset_id')->unsigned()->index('pairs_currency2_id_foreign');
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
		Schema::drop('pairs');
	}

}
