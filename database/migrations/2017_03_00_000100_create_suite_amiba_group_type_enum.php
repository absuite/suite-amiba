<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaGroupTypeEnum extends Migration {
	public $mdID = "94504ad010ec11e7b624e5d6241ed2e7";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.group.type.enum')->comment('阿米巴单元性质');
		$md->string('org')->comment('组织')->default(0);
		$md->string('dept')->comment('部门')->default(1);
		$md->string('work')->comment('工作中心')->default(2);
		// $md->string('team')->comment('班组')->default(3);
		// $md->string('item')->comment('料品')->default(4);
		// $md->string('person')->comment('人员')->default(4);
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
