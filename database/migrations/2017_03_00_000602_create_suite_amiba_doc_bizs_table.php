<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDocBizsTable extends Migration {
	private $mdID = "be47d560209511e7a995398957d7d24f";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.doc.biz')->comment('原始业务数据')->tableName('suite_amiba_doc_bizs');

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

		$md->string('to_org')->nullable()->comment('去向组织');
		$md->string('to_dept')->nullable()->comment('去向部门');
		$md->string('to_work')->nullable()->comment('去向工作中心');
		$md->string('to_team')->nullable()->comment('去向班组');
		$md->string('to_wh')->nullable()->comment('去向存储地点');
		$md->string('to_person')->nullable()->comment('去向业务人员');

		$md->string('direction')->nullable()->comment('收发类型');
		$md->string('trader')->nullable()->comment('客商');
		$md->string('item')->nullable()->comment('料品');
		$md->string('item_category')->nullable()->comment('料品分类');
		$md->string('project')->nullable()->comment('项目');
		$md->string('mfc')->nullable()->comment('厂牌');
		$md->string('lot')->nullable()->comment('批号');

		$md->string('currency')->nullable()->comment('币种');
		$md->string('uom')->nullable()->comment('计量单位');

		$md->decimal('qty', 30, 2)->default(0)->comment('数量');
		$md->decimal('price', 30, 2)->default(0)->comment('单价');
		$md->decimal('money', 30, 2)->default(0)->comment('金额');
		$md->decimal('tax', 30, 2)->default(0)->comment('税额');

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
