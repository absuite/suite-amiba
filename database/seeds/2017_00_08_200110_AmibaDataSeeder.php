<?php
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaDataSeeder extends Seeder {
	private $entId = '';

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->entId = config('gmf.ent.id');

		Models\ResultAccount::where('ent_id', $this->entId)->delete();
		Models\ResultProfit::where('ent_id', $this->entId)->delete();
	}
}
