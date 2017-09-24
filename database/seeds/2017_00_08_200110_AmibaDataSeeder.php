<?php
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaDataSeeder extends Seeder {
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

		Models\ResultAccount::where('ent_id', $this->entId)->delete();
		Models\ResultProfit::where('ent_id', $this->entId)->delete();
	}
}
