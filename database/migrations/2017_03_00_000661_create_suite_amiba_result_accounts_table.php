<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaResultAccountsTable extends Migration {
	private $mdID = "4cf153e0556611e78e423b559a737c97";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		$md = Metadata::create($this->mdID);
		$md->mdEntity('suite.amiba.result.account')->comment('核算结果表')->tableName('suite_amiba_result_accounts');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
		$md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
		$md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');
		$md->entity('element', 'suite.amiba.element')->nullable()->comment('核算要素');
		//归集direct，分配indirect，调整adjust
		$md->enum('type', 'suite.amiba.data.type.enum')->nullable()->comment('业务类型');
		$md->boolean('is_init')->default(0)->comment('是否期初');
		$md->boolean('is_outside')->default(0)->comment('外部交易');
		$md->decimal('init_money', 30, 2)->default(0)->comment('期初金额');
		$md->decimal('money', 30, 2)->default(0)->comment('金额');
		$md->decimal('bal_money', 30, 2)->default(0)->comment('结存金额');
		$md->string('src_id')->nullable()->comment('来源ID');
		$md->string('src_no')->nullable()->comment('来源单据号');

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
