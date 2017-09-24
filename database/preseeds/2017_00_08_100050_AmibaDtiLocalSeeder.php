<?php
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;

class AmibaDtiLocalSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Models\DtiLocal::where('code', 'like', 'api/amiba%')->delete();

		//业务数据
		Models\DtiLocal::build(function (Builder $b) {
			$b->method_enum('post')->code("api/amiba/doc-bizs/batch")->name("业务数据")->path('api/amiba/doc-bizs/batch');
			$b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});

		//财务数据
		Models\DtiLocal::build(function (Builder $b) {
			$b->method_enum('post')->code("api/amiba/doc-fis/batch")->name("财务数据")->path('api/cbo/doc-fis/batch');
			$b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
	}
}
