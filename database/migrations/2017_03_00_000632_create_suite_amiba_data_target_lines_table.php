<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataTargetLinesTable extends Migration {
	private $mdID = "df15a680203311e79306a3b3bf6f41fd";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.target.line')->comment('考核目标明细')->tableName('suite_amiba_data_target_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('target', 'suite.amiba.data.target')->nullable()->comment('考核目标');
		$md->entity('element', 'suite.amiba.element')->nullable()->comment('核算要素');
		$md->enum('type', 'suite.amiba.data.target.type.enum')->nullable()->comment('指标类型');
		$md->decimal('rate', 30, 2)->default(0)->comment('目标比率');
		$md->decimal('money', 30, 2)->default(0)->comment('目标额度');

		$md->timestamps();

		$md->foreign('target_id')->references('id')->on('suite_amiba_data_targets')->onDelete('cascade');

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
