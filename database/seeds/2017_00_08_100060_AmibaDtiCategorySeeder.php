<?php
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;

class AmibaDtiCategorySeeder extends Seeder {
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

		//DtiCategory
		//Models\DtiCategory::where('ent_id', $this->entId)->delete();

		Models\DtiCategory::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('u8')->name("用友U8");
		});

		Models\DtiCategory::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('k3')->name("金蝶K3");
		});

		Models\DtiCategory::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('sap')->name("SAP BusinessOne");
		});
		// Models\DtiCategory::build(function (Builder $b) {
		// 	$b->ent_id($this->entId)->code('oracle')->name("Oracle ERP");
		// });
		// Models\DtiCategory::build(function (Builder $b) {
		// 	$b->ent_id($this->entId)->code('Infor')->name("Infor ERP");
		// });
		// Models\DtiCategory::build(function (Builder $b) {
		// 	$b->ent_id($this->entId)->code('Sage')->name("Sage Accpac ERP");
		// });
		// Models\DtiCategory::build(function (Builder $b) {
		// 	$b->ent_id($this->entId)->code('Epicor')->name("Epicor ERP");
		// });
		Models\DtiCategory::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('u9')->name("用友U9");
		});

		Models\DtiCategory::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('nc')->name("用友NC");
		});

		Models\DtiCategory::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('神州数码易助')->name("神州数码易助");
		});
		Models\DtiCategory::build(function (Builder $b) {
			$b->ent_id($this->entId)->code('浪潮GS')->name("浪潮GS");
		});

	}
}
