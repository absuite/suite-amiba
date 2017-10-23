<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaGroupFactorEnum extends Migration {
	public $mdID = "7996c21017ec11e7bd0287cb3559cce8";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.group.factor.enum')->comment('经营体类型');
		$md->string('sales')->comment('销售')->default(0);
		$md->string('purchase')->comment('采购')->default(1);
		$md->string('manufacture')->comment('生产')->default(2);
		$md->string('storage')->comment('仓储')->default(3);
		$md->string('service')->comment('服务')->default(4);
		$md->string('finance')->comment('财务')->default(4);
		$md->string('other')->comment('其他')->default(4);
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
