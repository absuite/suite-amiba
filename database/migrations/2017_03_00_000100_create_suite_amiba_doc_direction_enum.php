<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDocDirectionEnum extends Migration {
	public $mdID = "824508d0209511e7ae18d1ec9260a813";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.doc.direction.enum')->comment('方向');
		$md->string('plus')->comment('增项')->default(0);
		$md->string('decrease')->comment('减项')->default(1);
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
