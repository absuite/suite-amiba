<?php
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaPurposeSeeder extends Seeder {
	private $entId = '';

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->entId = config('gmf.ent.id');

		Models\Purpose::where('ent_id', $this->entId)->delete();

		Models\Purpose::build(function (Builder $b) {$b->ent_id($this->entId)->code("ob01")->name("主核算目的")->currency("CNY")->calendar("month");});
	}
}
