<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaPriceTypeEnum extends Migration {
	public $mdID = "4bbd5f2018cf11e79f0fb7e84ec45d5e";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('suite.amiba.price.type.enum')->comment('价格类型');
		$md->string('sale')->comment('销售价')->default(0);
		$md->string('purchase')->comment('采购价')->default(1);
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
