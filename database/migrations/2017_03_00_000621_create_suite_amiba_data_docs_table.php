<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataDocsTable extends Migration {
	private $mdID = "466243a0180e11e7a3535f186d2b3403";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.doc')->comment('单据')->tableName('suite_amiba_data_docs');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->string('created_by')->nullable()->comment('制单人');
		$md->string('doc_no')->nullable()->comment('单据编号');
		$md->dateTime('doc_date')->nullable()->comment('单据日期');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
		//direct,indirect,allocated,memo
		$md->enum('use_type', 'suite.amiba.doc.use.type.enum')->nullable()->comment('数据用途');
		$md->entity('fm_group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->entity('to_group', 'suite.amiba.group')->nullable()->comment('对方阿米巴');

		$md->entity('element', 'suite.amiba.element')->nullable()->comment('核算要素');
		$md->entity('currency', 'suite.cbo.currency')->nullable()->comment('币种');

		$md->enum('src_type', 'suite.amiba.doc.src.type.enum')->nullable()->comment('来源类型');
		$md->string('src_id')->nullable()->comment('来源ID');
		$md->string('src_no')->nullable()->comment('来源单据号');
		$md->decimal('money', 30, 2)->default(0)->comment('金额');
		$md->text('memo')->nullable()->comment('备注');
		$md->enum('state', 'suite.amiba.doc.state.enum')->nullable()->comment('单据状态');

		$md->timestamps();

		$md->entity('lines', 'suite.amiba.data.doc.line')->nullable()->collection()->comment('明细行');

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
