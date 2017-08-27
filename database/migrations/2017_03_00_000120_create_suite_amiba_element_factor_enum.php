<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaElementFactorEnum extends Migration {
	private $mdID = "99f8849017e911e7b3267bed03c5ec4e";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.element.factor.enum')->comment('要素性质');
		$md->string('change')->comment('变动')->default(0);
		$md->string('fixed')->comment('固定')->default(1);
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
