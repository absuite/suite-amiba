<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaResultProfitsTable extends Migration {
	public $mdID = "d6886dc0208611e7b545ed1f626611c5";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.result.profit')->comment('经营台账')->tableName('suite_amiba_result_profits');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->boolean('is_init')->default(0)->comment('是否期初');
		$md->decimal('init_profit', 30, 2)->default(0)->comment('期初利润');
		$md->decimal('income', 30, 2)->default(0)->comment('收入');
		$md->decimal('cost', 30, 2)->default(0)->comment('支出');
		$md->decimal('bal_profit', 30, 2)->default(0)->comment('期末利润');

		$md->decimal('time_profit', 30, 2)->default(0)->comment('单位时间利润');
		$md->decimal('time_output', 30, 2)->default(0)->comment('单位时间产值');
		$md->decimal('time_total', 30, 2)->default(0)->comment('时间');

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
