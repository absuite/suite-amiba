<?php
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;

class AmibaDtiParamSeeder extends Seeder {
	public $entId = '';

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		if (empty($this->entId)) {
			$this->entId = config('gmf.ent.id');
		}
		if (empty($this->entId)) {
			return;
		}

		//Models\DtiParam::where('ent_id', $this->entId)->delete();

		Models\DtiParam::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('date')->name("日期")->type_enum('input')->value('date');
		});
		Models\DtiParam::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('fm_date')->name("开始日期")->type_enum('input')->value('fm_date');
		});
		Models\DtiParam::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('to_date')->name("结束日期")->type_enum('input')->value('to_date');
		});

		Models\DtiParam::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code('ent_code')->name("企业编码")->type_enum('fixed')->value('100');
		});
	}
}
