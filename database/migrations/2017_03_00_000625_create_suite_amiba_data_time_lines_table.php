<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaDataTimeLinesTable extends Migration {
	public $mdID = "5d85d9f0186011e7b6a837fad39fec0d";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.data.time.line')->comment('时间数据明细')->tableName('suite_amiba_data_time_lines');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('time', 'suite.amiba.data.time')->nullable()->comment('时间数据');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');

		$md->decimal('nor_time', 30, 1)->default(0)->comment('正常工作时间');
		$md->decimal('over_time', 30, 1)->default(0)->comment('加班时间');
		$md->decimal('total_time', 30, 1)->default(0)->comment('总劳动时间');

		$md->timestamps();

		$md->foreign('time_id')->references('id')->on('suite_amiba_data_times')->onDelete('cascade');

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
