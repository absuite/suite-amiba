<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDtiModelingPricesTable extends Migration {
	public $mdID = "01e8cf5ed31912c0b153b33ee2bbf66b";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.dti.modeling.price')->comment('接口数据建模')->tableName('suite_amiba_dti_modeling_prices');

		$md->increments('id');
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
		$md->entity('model', 'suite.amiba.modeling')->nullable()->comment('模型');
		$md->entity('fm_group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->entity('to_group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->entity('item', 'suite.cbo.item')->nullable()->comment('料品');
		$md->string('item_code')->nullable()->comment('料品编码');
		$md->timestamp('date')->nullable()->comment('日期');
		$md->decimal('price', 30, 2)->default(0)->comment('价格');
		$md->text('msg')->nullable()->comment('结果消息');
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
