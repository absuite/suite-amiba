<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataTargetsTable extends Migration {
	public $mdID = "df15a430203311e7bd7a452f2195e34a";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.target')->comment('考核目标')->tableName('suite_amiba_data_targets');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->entity('fm_period', 'suite.cbo.period.account')->nullable()->comment('开始期间');
		$md->entity('to_period', 'suite.cbo.period.account')->nullable()->comment('结束期间');
		$md->text('memo')->nullable()->comment('备注');

		$md->timestamps();
		$md->entity('lines', 'suite.amiba.data.target.line')->nullable()->collection()->comment('明细行');

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
