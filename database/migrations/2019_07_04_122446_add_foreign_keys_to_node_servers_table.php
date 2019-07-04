<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToNodeServersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('node_servers', function(Blueprint $table)
		{
			$table->foreign('currency_id')->references('id')->on('assets')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('node_servers', function(Blueprint $table)
		{
			$table->dropForeign('node_servers_currency_id_foreign');
		});
	}

}
