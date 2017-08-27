<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaElementScopeEnum extends Migration {
	private $mdID = "efeedfe07e9b11e7945217843c2046a0";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.element.scope.enum')->comment('要素范围');
		$md->string('inside')->comment('内部')->default(0);
		$md->string('outside')->comment('外部')->default(1);
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
