<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaModelingLinesTable extends Migration {
	private $mdID = "95dabdc017ef11e7837695952246f98f";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.modeling.line')->comment('经营模型明细')->tableName('suite_amiba_modeling_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('modeling', 'suite.amiba.modeling')->nullable()->comment('经营模型');
		$md->entity('element', 'suite.amiba.element')->nullable()->comment('核算要素');
		$md->enum('biz_type', 'suite.cbo.biz.type.enum')->nullable()->comment('业务类型');
		$md->entity('doc_type', 'suite.cbo.doc.type')->nullable()->comment('单据类型');
		$md->entity('item_category', 'suite.cbo.item.category')->nullable()->comment('料品分类');
		$md->string('project_code')->nullable()->comment('费用项目');
		$md->string('account_code')->nullable()->comment('会计科目');
		$md->entity('trader', 'suite.cbo.trader')->nullable()->comment('客商');
		$md->entity('item', 'suite.cbo.item')->nullable()->comment('料品');
		$md->string('factor1')->nullable()->comment('因素1');
		$md->string('factor2')->nullable()->comment('因素2');
		$md->string('factor3')->nullable()->comment('因素3');
		$md->string('factor4')->nullable()->comment('因素4');
		$md->string('factor5')->nullable()->comment('因素5');

		$md->enum('value_type', 'suite.amiba.value.type.enum')->nullable()->comment('取值类型');
		$md->string('adjust')->nullable()->comment('取值比率');
		$md->timestamps();

		$md->foreign('modeling_id')->references('id')->on('suite_amiba_modelings')->onDelete('cascade');

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
