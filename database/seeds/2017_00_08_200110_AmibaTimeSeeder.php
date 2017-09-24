<?php
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaTimeSeeder extends Seeder {
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

		Models\DataTime::where('ent_id', $this->entId)->delete();

		Models\DataTime::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->period("201701")->id("f8f6b5d05e3211e7a562d9926787cf6b");});
		Models\DataTime::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->period("201702")->id("f8f6b5d05e3211e7a562d9926787cf6c");});
		Models\DataTime::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->period("201703")->id("f8f6b5d05e3211e7a562d9926787cf6d");});
		Models\DataTime::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->period("201704")->id("f8f6b5d05e3211e7a562d9926787cf6e");});
		Models\DataTime::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->period("201705")->id("f8f6b5d05e3211e7a562d9926787cf6f");});
		Models\DataTime::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->period("201706")->id("f8f6b5d05e3211e7a562d9926787cf6g");});
		Models\DataTime::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->period("201706")->id("f8f6b5d05e3211e7a562d9926787cf6h");});

		Models\DataTimeLine::where('ent_id', $this->entId)->delete();
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb0101")->nor_time("1408");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb0102")->nor_time("3520");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb0103")->nor_time("1584");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb0201")->nor_time("1056");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb020301")->nor_time("5280");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb020302")->nor_time("4224");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb0204")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb03")->nor_time("3168");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb0402")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6b")->group("amb0403")->nor_time("4400");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb0101")->nor_time("1408");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb0102")->nor_time("3520");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb0103")->nor_time("1584");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb0201")->nor_time("1056");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb020301")->nor_time("5280");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb020302")->nor_time("4224");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb0204")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb03")->nor_time("3168");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb0402")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6c")->group("amb0403")->nor_time("4400");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb0101")->nor_time("1408");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb0102")->nor_time("3520");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb0103")->nor_time("1584");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb0201")->nor_time("1056");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb020301")->nor_time("5280");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb020302")->nor_time("4224");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb0204")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb03")->nor_time("3168");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb0402")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6d")->group("amb0403")->nor_time("4400");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb0101")->nor_time("1408");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb0102")->nor_time("3520");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb0103")->nor_time("1584");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb0201")->nor_time("1056");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb020301")->nor_time("5280");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb020302")->nor_time("4224");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb0204")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb03")->nor_time("3168");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb0402")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6e")->group("amb0403")->nor_time("4400");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb0101")->nor_time("1408");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb0102")->nor_time("3520");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb0103")->nor_time("1584");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb0201")->nor_time("1056");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb020301")->nor_time("5280");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb020302")->nor_time("4224");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb0204")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb03")->nor_time("3168");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb0402")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6f")->group("amb0403")->nor_time("4400");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb0101")->nor_time("1408");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb0102")->nor_time("3520");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb0103")->nor_time("1584");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb0201")->nor_time("1056");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb020301")->nor_time("5280");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb020302")->nor_time("4224");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb0204")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb03")->nor_time("3168");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb0402")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6g")->group("amb0403")->nor_time("4400");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb0101")->nor_time("1408");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb0102")->nor_time("3520");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb0103")->nor_time("1584");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb0201")->nor_time("1056");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb020301")->nor_time("5280");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb020302")->nor_time("4224");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb0204")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb03")->nor_time("3168");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb0402")->nor_time("2640");});
		Models\DataTimeLine::build(function (Builder $b) {$b->ent_id($this->entId)->time_id("f8f6b5d05e3211e7a562d9926787cf6h")->group("amb0403")->nor_time("4400");});

	}
}
