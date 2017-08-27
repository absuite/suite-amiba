<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaPricesTable extends Migration {
	private $mdID = "4bbd5dc018cf11e7bb3b9d54706c1965";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.price')->comment('考核价表')->tableName('suite_amiba_prices');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->timestamps();

		$md->entity('lines', 'suite.amiba.price.line')->nullable()->collection()->comment('明细行');

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
