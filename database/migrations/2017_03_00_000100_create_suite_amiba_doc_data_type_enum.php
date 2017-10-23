<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDocDataTypeEnum extends Migration {
	public $mdID = "6470db0054f611e7a1edbf9cc21cdbc0";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.doc.data.type.enum')->comment('数据类型');
		$md->string('amt')->comment('金额')->default(0);
		$md->string('qty')->comment('数量')->default(1);
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
