<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaAllotMethodsTable extends Migration {
	private $mdID = "697f2000129e11e787893bfb90343a5f";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.allot.method')->comment('分配方法')->tableName('suite_amiba_allot_methods');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->timestamps();

		$md->entity('lines', 'suite.amiba.allot.method.line')->nullable()->collection()->comment('明细行');

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
