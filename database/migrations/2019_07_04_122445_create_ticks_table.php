<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pair_id')->unsigned()->nullable()->index('ticks_pair_id_foreign');
			$table->bigInteger('open');
			$table->bigInteger('close');
			$table->bigInteger('min')->nullable();
			$table->bigInteger('max')->nullable();
			$table->bigInteger('avg')->nullable();
			$table->integer('time');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ticks');
	}

}
