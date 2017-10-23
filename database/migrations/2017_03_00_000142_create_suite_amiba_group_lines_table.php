<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaGroupLinesTable extends Migration {
	public $mdID = "260c785010ed11e7908829e4468497b2";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.group.line')->comment('阿米巴构成')->tableName('suite_amiba_group_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->string('data_id')->nullable()->comment('数据ID');
		$md->string('data_type')->nullable()->comment('数据类型');
		$md->text('memo')->nullable()->comment('备注');
		$md->timestamps();

		$md->foreign('group_id')->references('id')->on('suite_amiba_groups')->onDelete('cascade');

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
