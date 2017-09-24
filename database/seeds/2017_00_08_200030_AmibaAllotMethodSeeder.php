<?php
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaAllotMethodSeeder extends Seeder {
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

		Models\AllotMethod::where('ent_id', $this->entId)->delete();
		Models\AllotMethod::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->code("FF01")->name("方法1");});

		Models\AllotMethodLine::where('ent_id', $this->entId)->delete();
		Models\AllotMethodLine::build(function (Builder $b) {$b->ent_id($this->entId)->method("FF01")->group("amb0103")->rate("9");});
		Models\AllotMethodLine::build(function (Builder $b) {$b->ent_id($this->entId)->method("FF01")->group("amb0201")->rate("6");});
		Models\AllotMethodLine::build(function (Builder $b) {$b->ent_id($this->entId)->method("FF01")->group("amb0402")->rate("30");});
		Models\AllotMethodLine::build(function (Builder $b) {$b->ent_id($this->entId)->method("FF01")->group("amb0403")->rate("15");});
		Models\AllotMethodLine::build(function (Builder $b) {$b->ent_id($this->entId)->method("FF01")->group("amb03")->rate("10");});

	}
}
