<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaElementTypeEnum extends Migration {
	private $mdID = "fb332b9010eb11e7aeb3af5f01aeb33c";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.element.type.enum')->comment('要素类型');
		$md->string('rcv')->comment('收入')->default(0);
		$md->string('cost')->comment('成本')->default(1);
		$md->string('free')->comment('费用')->default(2);
		$md->string('tax')->comment('税金')->default(3);
		$md->string('memo')->comment('备注')->default(4);
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
