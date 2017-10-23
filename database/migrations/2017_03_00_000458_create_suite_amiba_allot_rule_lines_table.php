<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaAllotRuleLinesTable extends Migration {
	public $mdID = "697f2660129e11e7a375370ed28a51f4";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.allot.rule.line')->comment('分配标准明细')->tableName('suite_amiba_allot_rule_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('rule', 'suite.amiba.allot.rule')->nullable()->comment('分配标准');
		$md->entity('element', 'suite.amiba.element')->nullable()->comment('目的核算要素');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('目的阿米巴');
		$md->decimal('rate', 30, 2)->comment('比例');
		$md->timestamps();

		$md->foreign('rule_id')->references('id')->on('suite_amiba_allot_rules')->onDelete('cascade');

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
