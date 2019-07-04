<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTicksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ticks', function(Blueprint $table)
		{
			$table->foreign('pair_id')->references('id')->on('pairs')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ticks', function(Blueprint $table)
		{
			$table->dropForeign('ticks_pair_id_foreign');
		});
	}

}
