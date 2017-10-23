<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDocSrcTypeEnum extends Migration {
	public $mdID = "bf2cbda0554311e786150b8cd04a3bde";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.doc.src.type.enum')->comment('来源类型');
		$md->string('interface')->comment('接口')->default(0);
		$md->string('manual')->comment('手工')->default(1);
		$md->string('allot')->comment('分配')->default(2);
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
