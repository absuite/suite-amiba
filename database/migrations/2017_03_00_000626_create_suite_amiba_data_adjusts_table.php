<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataAdjustsTable extends Migration {
	public $mdID = "90601760203111e79db05daf7dfa78e5";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.adjust')->comment('考核调整')->tableName('suite_amiba_data_adjusts');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->string('doc_no')->nullable()->comment('单据编号');
		$md->dateTime('doc_date')->nullable()->comment('单据日期');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
		$md->text('memo')->nullable()->comment('备注');

		$md->timestamps();
		$md->entity('lines', 'suite.amiba.data.adjust.line')->nullable()->collection()->comment('明细行');

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
