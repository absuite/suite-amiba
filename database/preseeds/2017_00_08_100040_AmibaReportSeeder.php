<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmibaReportSeeder extends Seeder {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		$exception = DB::transaction(function () {

			$id = "a0a059e0706611e79920d1cd3cd298a2";
			Models\Query::build(function (Builder $b) use ($id) {
				$b->id($id)->name('suite.amiba.group.periods.ans.report');

				$wheres = [];
				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.amiba.purpose.ref');
				$item->name('purpose_id')->comment('核算目的')->type('ref')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('code');
				$item->name('fm_period')->comment('开始期间')->type('ref')->operator('greater_than_equal')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('code');
				$item->name('to_period')->comment('结束期间')->type('ref')->operator('less_than_equal')->refs($refInfo);
				array_push($wheres, $item);

				$qm = new Builder;
				$qm->wheres($wheres);

				$b->data($qm->toJson());
			});

			$id = "dc5d29c0713d11e787986957ecfc21ee";
			Models\Query::build(function (Builder $b) use ($id) {
				$b->id($id)->name('suite.amiba.period.groups.ans.report');

				$wheres = [];
				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.amiba.purpose.ref');
				$item->name('purpose_id')->comment('核算目的')->type('ref')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('id');
				$item->name('period_id')->comment('期间')->type('ref')->operator('equal')->refs($refInfo);
				array_push($wheres, $item);

				$qm = new Builder;
				$qm->wheres($wheres);

				$b->data($qm->toJson());
			});

			$id = "beb518d0713e11e7a4fe0bce749bd541";
			Models\Query::build(function (Builder $b) use ($id) {
				$b->id($id)->name('suite.amiba.periods.groups.ans.report');

				$wheres = [];
				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.amiba.purpose.ref');
				$item->name('purpose_id')->comment('核算目的')->type('ref')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('code');
				$item->name('fm_period')->comment('开始期间')->type('ref')->operator('greater_than_equal')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('code');
				$item->name('to_period')->comment('结束期间')->type('ref')->operator('less_than_equal')->refs($refInfo);
				array_push($wheres, $item);

				$qm = new Builder;
				$qm->wheres($wheres);

				$b->data($qm->toJson());
			});

			$id = "fc23a140713e11e7863f8b8084b7edcd";
			Models\Query::build(function (Builder $b) use ($id) {
				$b->id($id)->name('suite.amiba.period.groups.rank.report');

				$wheres = [];
				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.amiba.purpose.ref');
				$item->name('purpose_id')->comment('核算目的')->type('ref')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('id');
				$item->name('period_id')->comment('期间')->type('ref')->operator('equal')->refs($refInfo);
				array_push($wheres, $item);

				$qm = new Builder;
				$qm->wheres($wheres);

				$b->data($qm->toJson());
			});

			$id = "048d9e90713f11e795d6db6977185ccb";
			Models\Query::build(function (Builder $b) use ($id) {
				$b->id($id)->name('suite.amiba.group.periods.purpose.report');

				$wheres = [];
				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.amiba.purpose.ref');
				$item->name('purpose_id')->comment('核算目的')->type('ref')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('code');
				$item->name('fm_period')->comment('开始期间')->type('ref')->operator('greater_than_equal')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('code');
				$item->name('to_period')->comment('结束期间')->type('ref')->operator('less_than_equal')->refs($refInfo);
				array_push($wheres, $item);

				$qm = new Builder;
				$qm->wheres($wheres);

				$b->data($qm->toJson());
			});

			$id = "1d6b7880713f11e7bec6637e2b1a1524";
			Models\Query::build(function (Builder $b) use ($id) {
				$b->id($id)->name('suite.amiba.period.groups.purpose.report');

				$wheres = [];
				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.amiba.purpose.ref');
				$item->name('purpose_id')->comment('核算目的')->type('ref')->refs($refInfo);
				array_push($wheres, $item);

				$item = new Builder;
				$refInfo = new Builder;
				$refInfo->id('suite.cbo.period.account.ref')->valueField('id');
				$item->name('period_id')->comment('期间')->type('ref')->operator('equal')->refs($refInfo);
				array_push($wheres, $item);

				$qm = new Builder;
				$qm->wheres($wheres);

				$b->data($qm->toJson());
			});

		});
	}
}
