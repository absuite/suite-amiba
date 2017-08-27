<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaPriceAdjustLinesTable extends Migration {
	private $mdID = "1022a62018d111e7b71f59feec19debb";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.price.adjust.line')->comment('考核价表明细')->tableName('suite_amiba_price_adjust_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('adjust', 'suite.amiba.price.adjust')->nullable()->comment('考核价表');
		$md->enum('type', 'suite.amiba.price.type.enum')->nullable()->comment('价格类型');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('目的阿米巴');
		$md->entity('item', 'suite.cbo.item')->nullable()->comment('物料');
		$md->decimal('cost_price', 30, 2)->default(0)->comment('价格');
		$md->timestamps();

		$md->foreign('adjust_id')->references('id')->on('suite_amiba_price_adjusts')->onDelete('cascade');

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
