<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataDocLinesTable extends Migration {
	private $mdID = "46624480180e11e7bca81736a380ee38";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.doc.line')->comment('单据行')->tableName('suite_amiba_data_doc_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('doc', 'suite.amiba.data.doc')->nullable()->comment('单据');
		// $md->enum('biz_type', 'suite.cbo.biz.type.enum')->nullable()->comment('业务类型');
		// $md->enum('data_type', 'suite.amiba.doc.data.type.enum')->nullable()->comment('数据类型');
		$md->entity('trader', 'suite.cbo.trader')->nullable()->comment('客商');
		$md->entity('item_category', 'suite.cbo.item.category')->nullable()->comment('料品分类');
		$md->entity('item', 'suite.cbo.item')->nullable()->comment('料品');
		$md->entity('mfc', 'suite.cbo.mfc')->nullable()->comment('厂牌');
		$md->entity('project', 'suite.cbo.project')->nullable()->comment('项目');
		$md->string('expense_code')->nullable()->comment('费用项目');
		$md->string('account_code')->nullable()->comment('科目');
		$md->entity('unit', 'suite.cbo.unit')->nullable()->comment('计量单位');
		$md->decimal('qty', 30, 2)->default(0)->comment('数量');
		$md->decimal('price', 30, 2)->default(0)->comment('单价');
		$md->decimal('money', 30, 2)->default(0)->comment('金额');
		$md->text('memo')->nullable()->comment('描述');

		$md->timestamps();

		$md->foreign('doc_id')->references('id')->on('suite_amiba_data_docs')->onDelete('cascade');

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
