<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\LnsItem;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models\Group;

class AmibaLnsItemPreSeeder extends Seeder {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		LnsItem::build(function (Builder $b) {
			$b->type(Group::class)->code('amiba')->name('阿米巴数');
		});
	}
}
