<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaAllotMethodLinesTable extends Migration {
	private $mdID = "697f24e0129e11e7a3972ff11ddb6b9e";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.allot.method.line')->comment('分配方法明细')->tableName('suite_amiba_allot_method_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('method', 'suite.amiba.allot.method')->nullable()->comment('分配方法');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->decimal('rate', 30, 2)->comment('基数');
		$md->timestamps();

		$md->foreign('method_id')->references('id')->on('suite_amiba_allot_methods')->onDelete('cascade');

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
