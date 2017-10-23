<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaModelingMatchDirectionEnum extends Migration {
	public $mdID = "d2a7c740961411e7a8dfc513fbdf4cee";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.modeling.match.direction.enum')->comment('匹配方向');
		$md->string('fm')->comment('来源')->default(1);
		$md->string('to')->comment('目标')->default(2);
		$md->build();

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Metadata::dropIfExists($this->mdID);
	}
}
