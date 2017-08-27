<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDtiModelingsTable extends Migration {
	private $mdID = "30f4d4c036d211e7bfa6f52f5d85dd72";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.dti.modeling')->comment('接口数据建模')->tableName('suite_amiba_dti_modelings');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
		$md->text('memo')->nullable()->comment('备注');
		$md->boolean('revoked')->default(0)->comment('撤销');
		$md->boolean('succeed')->default(0)->comment('成功的');
		$md->dateTime('start_time')->nullable()->comment('开始时间');
		$md->dateTime('end_time')->nullable()->comment('结束时间');
		$md->text('msg')->nullable()->comment('结果消息');
		$md->timestamps();

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
