<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataInitLinesTable extends Migration {
	private $mdID = "5d85d750186011e79a1ebf1d5297ec2c";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.init.line')->comment('期间考核数据明细')->tableName('suite_amiba_data_init_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('init', 'suite.amiba.data.init')->nullable()->comment('期间考核数据');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');

		$md->boolean('is_init', 30, 2)->default(0)->comment('是否期初');
		$md->decimal('income', 30, 2)->default(0)->comment('收入');
		$md->decimal('cost', 30, 2)->default(0)->comment('成本');
		$md->decimal('profit', 30, 2)->default(0)->comment('利润');
		$md->decimal('ext_income', 30, 2)->default(0)->comment('外部收入');
		$md->decimal('ext_cost', 30, 2)->default(0)->comment('外部成本');
		$md->decimal('ext_profit', 30, 2)->default(0)->comment('外部利润');

		$md->timestamps();

		$md->foreign('init_id')->references('id')->on('suite_amiba_data_inits')->onDelete('cascade');
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
