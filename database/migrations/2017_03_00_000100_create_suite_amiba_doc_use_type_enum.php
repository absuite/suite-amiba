<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDocUseTypeEnum extends Migration {
	public $mdID = "6470e18054f611e7b63193d90b8e12e6";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.doc.use.type.enum')->comment('数据用途');
		$md->string('direct')->comment('直接数据')->default(10);
		$md->string('indirect')->comment('待分配')->default(20);
		$md->string('allocated')->comment('已分配')->default(30);
		$md->string('memo')->comment('备注')->default(50);
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
