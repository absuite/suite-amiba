<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataTypeEnum extends Migration {
	private $mdID = "5d85dc60186011e7beeb2f9e419adee8";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.data.type.enum')->comment('业务类型');
		$md->string('direct')->comment('归集')->default(0);
		$md->string('indirect')->comment('分配')->default(1);
		$md->string('adjust')->comment('调整')->default(2);
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
