<?php
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaDataInitSeeder extends Seeder {
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

		if (Models\DataInit::where('ent_id', $this->entId)->count()) {
			return;
		}

		Models\DataInit::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->period("201701")->currency("CNY")->id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"));});

		Models\DataInitLine::where('ent_id', $this->entId)->delete();
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb0101")->profit("10000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb0102")->profit("30000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb0103")->profit("11000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb0201")->profit("5000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb020301")->profit("60000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb020302")->profit("50000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb0204")->profit("36000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb03")->profit("45000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb0402")->profit("32000");});
		Models\DataInitLine::build(function (Builder $b) {$b->ent_id($this->entId)->init_id(md5($this->entId . "02f31e005e3111e7992ff19c3fee0876"))->group("amb0403")->profit("50000");});

	}
}
