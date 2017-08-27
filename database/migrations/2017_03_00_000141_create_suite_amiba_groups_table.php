<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaGroupsTable extends Migration {
	private $mdID = "260c755010ed11e7864eb935dfea81a5";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.group')->comment('阿米巴')->tableName('suite_amiba_groups');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('parent', 'suite.amiba.group')->nullable()->comment('上级');
		$md->enum('type', 'suite.amiba.group.type.enum')->nullable()->comment('类型');
		$md->enum('factor', 'suite.amiba.group.factor.enum')->nullable()->comment('经营体类型');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->comment('名称');
		$md->integer('employees')->default(0)->comment('员工人数');
		$md->boolean('is_leaf')->default(1)->comment('叶子');
		$md->text('memo')->nullable()->comment('备注');
		$md->timestamps();

		$md->entity('lines', 'suite.amiba.group.line')->nullable()->collection()->comment('明细行');

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
