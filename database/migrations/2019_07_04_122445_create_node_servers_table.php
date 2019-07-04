<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNodeServersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('node_servers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('currency_id')->unsigned()->index('node_servers_currency_id_foreign');
			$table->string('base_url', 191);
			$table->string('status', 191)->nullable();
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
		Schema::drop('node_servers');
	}

}
