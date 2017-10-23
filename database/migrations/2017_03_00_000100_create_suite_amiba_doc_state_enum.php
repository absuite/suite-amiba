<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDocStateEnum extends Migration {
	public $mdID = "c3622ab0554311e7adbcc5d0695c8257";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.doc.state.enum')->comment('单据状态');
		$md->string('opened')->comment('开立')->default(0);
		$md->string('approve')->comment('核准中')->default(1);
		$md->string('approved')->comment('已核准')->default(2);
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
