<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataTargetTypeEnum extends Migration {
	public $mdID = "df15a720203311e7b034d5f3c0c8e0c1";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.data.target.type.enum')->comment('目标指标类型');
		$md->string('grossRate')->comment('毛利率目标')->default(0);
		$md->string('netRate')->comment('净利率目标')->default(1);
		$md->string('growth')->comment('增长率目标')->default(2);
		$md->string('income')->comment('收入目标')->default(2);
		$md->string('expend')->comment('支出目标')->default(2);
		$md->string('grossProfit')->comment('毛利润目标')->default(2);
		$md->string('netProfit')->comment('净利润目标')->default(2);
		$md->string('additional')->comment('单位附加值目标')->default(2);
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
