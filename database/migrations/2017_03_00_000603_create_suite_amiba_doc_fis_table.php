<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDocFisTable extends Migration {
	public $mdID = "be47d740209511e7a62eb5ea591210cd";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.doc.fi')->comment('原始财务数据')->tableName('suite_amiba_doc_fis');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->string('doc_no')->nullable()->comment('单据编号');
		$md->dateTime('doc_date')->nullable()->comment('单据日期');
		$md->text('memo')->nullable()->comment('备注');

		$md->string('src_doc_id')->nullable()->comment('来源单据ID');
		$md->string('src_doc_type')->nullable()->comment('来源单据类型');
		$md->string('src_key_id')->nullable()->comment('来源主键ID');
		$md->string('src_key_type')->nullable()->comment('来源主键类型');

		$md->string('biz_type')->nullable()->comment('业务类型');
		$md->string('doc_type')->nullable()->comment('单据类型');
		$md->string('org')->nullable()->comment('组织');
		$md->string('person')->nullable()->comment('业务人员');

		$md->string('fm_org')->nullable()->comment('来源组织');
		$md->string('fm_dept')->nullable()->comment('来源部门');
		$md->string('fm_work')->nullable()->comment('来源工作中心');
		$md->string('fm_team')->nullable()->comment('来源班组');
		$md->string('fm_wh')->nullable()->comment('来源存储地点');
		$md->string('fm_person')->nullable()->comment('来源业务人员');

		$md->string('trader')->nullable()->comment('客商');
		$md->string('project')->nullable()->comment('项目');

		$md->string('account')->nullable()->comment('科目');
		$md->string('currency')->nullable()->comment('币种');

		$md->decimal('debit_money', 30, 2)->default(0)->comment('借方金额');
		$md->decimal('credit_money', 30, 2)->default(0)->comment('贷方金额');

		$md->string('factor1')->nullable()->comment('因素1');
		$md->string('factor2')->nullable()->comment('因素2');
		$md->string('factor3')->nullable()->comment('因素3');
		$md->string('factor4')->nullable()->comment('因素4');
		$md->string('factor5')->nullable()->comment('因素5');

		$md->string('data_src_identity')->nullable()->comment('数据来源身份');

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
