<?php
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;

class AmibaDtiParamSeeder extends Seeder {
	private $entId = '';

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->entId = config('gmf.ent.id');

		Models\DtiParam::where('ent_id', $this->entId)->delete();

		$categories = Models\DtiCategory::where('ent_id', $this->entId)->get();
		foreach ($categories as $ik => $iv) {
			Models\DtiParam::build(function (Builder $b) use ($iv) {
				$b->ent_id($this->entId)->category($iv->code)->code('type')->name("类型")->type_enum('fixed')->value($iv->code);
			});
			Models\DtiParam::build(function (Builder $b) use ($iv) {
				$b->ent_id($this->entId)->category($iv->code)->code('date')->name("日期")->type_enum('input')->value('date');
			});
			Models\DtiParam::build(function (Builder $b) use ($iv) {
				$b->ent_id($this->entId)->category($iv->code)->code('fm_date')->name("日期")->type_enum('input')->value('fm_date');
			});
			Models\DtiParam::build(function (Builder $b) use ($iv) {
				$b->ent_id($this->entId)->category($iv->code)->code('to_date')->name("日期")->type_enum('input')->value('to_date');
			});
		}

		Models\DtiParam::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code('ent_code')->name("企业编码")->type_enum('fixed')->value('002');
		});
	}
}
