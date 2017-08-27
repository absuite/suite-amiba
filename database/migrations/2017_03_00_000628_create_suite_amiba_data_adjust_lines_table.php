<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataAdjustLinesTable extends Migration {
	private $mdID = "90601940203111e7a4e5fdcea11e6924";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.adjust.line')->comment('考核调整明细')->tableName('suite_amiba_data_adjust_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('adjust', 'suite.amiba.data.adjust')->nullable()->comment('考核调整');
		$md->entity('fm_group', 'suite.amiba.group')->nullable()->comment('来源阿米巴');
		$md->entity('to_group', 'suite.amiba.group')->nullable()->comment('目的阿米巴');
		$md->entity('fm_element', 'suite.amiba.element')->nullable()->comment('来源核算要素');
		$md->entity('to_element', 'suite.amiba.element')->nullable()->comment('目的核算要素');
		$md->decimal('money', 30, 2)->default(0)->comment('金额');

		$md->timestamps();

		$md->foreign('adjust_id')->references('id')->on('suite_amiba_data_adjusts')->onDelete('cascade');

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
