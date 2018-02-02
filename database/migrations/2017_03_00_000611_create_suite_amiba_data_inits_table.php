<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataInitsTable extends Migration {
	public $mdID = "46623d90180e11e7a58db5577f82cba4";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.init')->comment('期间考核数据')->tableName('suite_amiba_data_inits');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->string('doc_no')->nullable()->comment('单据编号');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
		$md->entity('currency', 'suite.cbo.currency')->nullable()->comment('币种');

		$md->timestamps();

		$md->entity('lines', 'suite.amiba.data.init.line')->nullable()->collection()->comment('明细行');

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
