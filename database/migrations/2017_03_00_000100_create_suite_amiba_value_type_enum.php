<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaValueTypeEnum extends Migration {
	private $mdID = "95dabf1017ef11e79de361065a788d05";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.value.type.enum')->comment('取值类型');
		$md->string('amt')->comment('金额')->default(0);
		$md->string('qty')->comment('数量')->default(1);
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
