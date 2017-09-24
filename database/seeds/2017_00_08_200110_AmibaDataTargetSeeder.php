<?php
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaDataTargetSeeder extends Seeder {
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

		Models\DataTarget::where('ent_id', $this->entId)->delete();
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb0101")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30c");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb0102")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30d");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb0103")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30e");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb0201")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30f");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb020301")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30g");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb020302")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30h");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb0204")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30i");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb03")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30j");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb0402")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30k");});
		Models\DataTarget::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->group("amb0403")->fm_period("201701")->to_period("201712")->id("7207fdc05e3411e7bec6b5699372a30l");});

		Models\DataTargetLine::where('ent_id', $this->entId)->delete();
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30c")->type_name("净利润目标")->element("")->money("170000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30d")->type_name("净利润目标")->element("")->money("420000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30e")->type_name("净利润目标")->element("")->money("190000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30f")->type_name("净利润目标")->element("")->money("120000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30g")->type_name("净利润目标")->element("")->money("650000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30h")->type_name("净利润目标")->element("")->money("500000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30i")->type_name("净利润目标")->element("")->money("320000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30j")->type_name("净利润目标")->element("")->money("380000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30k")->type_name("净利润目标")->element("")->money("320000")->rate("");});
		Models\DataTargetLine::build(function (Builder $b) {$b->ent_id($this->entId)->target_id("7207fdc05e3411e7bec6b5699372a30l")->type_name("净利润目标")->element("")->money("520000")->rate("");});

	}
}
