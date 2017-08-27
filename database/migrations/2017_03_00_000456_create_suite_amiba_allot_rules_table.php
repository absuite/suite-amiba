<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaAllotRulesTable extends Migration {
	private $mdID = "697f2600129e11e784114bd8efe4079f";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.allot.rule')->comment('分配标准')->tableName('suite_amiba_allot_rules');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('method', 'suite.amiba.allot.method')->nullable()->comment('分配方法');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('来源阿米巴');
		$md->entity('element', 'suite.amiba.element')->nullable()->comment('来源核算要素');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->timestamps();

		$md->entity('lines', 'suite.amiba.allot.rule.line')->nullable()->collection()->comment('明细行');

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
