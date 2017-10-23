<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaElementDirectionEnum extends Migration {
	public $mdID = "fb3329a010eb11e79ba937544629339d";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.element.direction.enum')->comment('要素方向');
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
