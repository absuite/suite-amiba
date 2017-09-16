<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaElementsTable extends Migration {
	private $mdID = "fb332c2010eb11e7b73313ac15e829be";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.element')->comment('核算要素')->tableName('suite_amiba_elements');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');

		$md->entity('parent', 'suite.amiba.element')->nullable()->comment('上级核算要素');
		$md->enum('type', 'suite.amiba.element.type.enum')->nullable()->comment('类型');
		$md->enum('direction', 'suite.amiba.element.direction.enum')->nullable()->comment('方向');
		$md->enum('factor', 'suite.amiba.element.factor.enum')->nullable()->comment('性质');
		$md->enum('scope', 'suite.amiba.element.scope.enum')->nullable()->comment('范围');
		$md->string('code')->nullable()->comment('编码');

		$md->boolean('is_manual')->default(0)->comment('是否人工');
		$md->string('name')->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->boolean('is_leaf')->default(1)->comment('叶子');
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
