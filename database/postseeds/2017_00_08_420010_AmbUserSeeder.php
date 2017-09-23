<?php

use Gmf\Ac\Models\User;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Ent;
use Illuminate\Database\Seeder;

class AmbUserSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		$b = new Builder;
		$eid = config('gmf.ent.id');
		for ($i = 1; $i <= 10; $i++) {
			$b = new Builder;
			$b->account('amb' . $i . '@amb.com')->name('amb' . $i . '@amb.com')->password('amb' . $i . '@amb.com');
			$user = User::registerByAccount('sys', $b->toArray());

			Ent::addUser($eid, $user->id);

		}

	}
}
