<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmibaQuerySeeder extends Seeder {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		$this->down();

		$exception = DB::transaction(function () {

			$id = "e65d0160110011e79e7e8d1b445167b9";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.purpose.list')->entity('suite.amiba.purpose')
					->fields(['id', 'code', 'name', 'calendar.name', 'currency.name']);
				$builder->orders(['purpose.code', 'code', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "e65d00b0110011e79e09910babe715b3";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.element.list')->entity('suite.amiba.element')
					->fields(['id', 'code', 'name', 'parent.name', 'purpose.name', 'type_enum', 'direction_enum', 'factor_enum', 'is_manual']);
				$builder->orders(['purpose.code', 'code', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "e65d01e0110011e78d818fc2c7a29451";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.group.list')->entity('suite.amiba.group')
					->fields(['id', 'code', 'name', 'purpose.name', 'parent.name', 'employees']);
				$builder->orders(['purpose.code' => 'asc', 'code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "9784348017f911e7bc30bf7975a72311";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.modeling.list')->entity('suite.amiba.modeling')
					->fields(['id', 'purpose.name', 'group.name']);
				$builder->orders(['purpose.code' => 'asc', 'group.code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "a3839bf04d7511e7ae62ebb3d08184bd";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.price.list')->entity('suite.amiba.price');
				$builder->fields(['id', 'code', 'name', 'purpose.name', 'group.name']);
				$builder->orders(['purpose.code' => 'asc', 'code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "a3839d804d7511e795c47b089b0d8968";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.price.adjust.list')->entity('suite.amiba.price.adjust');
				$builder->fields(['id', 'code', 'name', 'purpose.name', 'group.name']);
				$builder->orders(['purpose.code' => 'asc', 'code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			// $id = "ee21ef90129e11e7bb3eb37969aa7bcc";
			// Models\Query::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->name('suite.amiba.allot.mark.list')->entity('suite.amiba.allot.mark')
			// 		->fields(['id', 'code' => '编码', 'name' => '名称']);
			// });
			$id = "ee21f480129e11e7b07ca9c5c8738ce1";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.allot.method.list')->entity('suite.amiba.allot.method')
					->fields(['id', 'code', 'name', 'purpose.name']);
				$builder->orders(['purpose.code' => 'asc', 'code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});
			$id = "ee21f520129e11e7add855df959d67ff";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.allot.rule.list')->entity('suite.amiba.allot.rule')
					->fields(['id', 'code', 'name', 'purpose.name', 'method.name']);
				$builder->orders(['purpose.code' => 'asc', 'code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			// $id = "41f263a0185511e7a3f26b50dfe44db7";
			// Models\Query::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->name('suite.amiba.dti.category.list')->entity('suite.amiba.dti.category')
			// 		->fields(['id', 'code', 'name']);
			// });

			// $id = "41f26640185511e79543d510ec945605";
			// Models\Query::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->name('suite.amiba.dti.data.list')->entity('suite.amiba.dti.data')
			// 		->fields(['id', 'code', 'name']);
			// });

			$id = "af9b64e036c211e78a4569ad164cea46";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.dti.modeling.list')->entity('suite.amiba.dti.modeling')
					->fields(['id', 'purpose.name', 'period.name', 'revoked', 'succeed', 'start_time', 'end_time', 'msg']);
				$builder->orders(['purpose.code' => 'asc', 'period.code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "e073262018b011e7991607650ebb51ea";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.data.init.list')->entity('suite.amiba.data.init')
					->fields(['id', 'purpose.name', 'period.name']);
				$builder->orders(['purpose.code' => 'asc', 'period.code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "1e03cbd0207911e7b9622381b06239f7";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.data.target.list')->entity('suite.amiba.data.target');
				$builder->fields(['id', 'purpose.name', 'group.name', 'fm_period.name', 'to_period.name']);
				$builder->orders(['purpose.code' => 'asc', 'group.code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "41f26700185511e7b1cbd3cd287633d2";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.data.time.list')->entity('suite.amiba.data.time');
				$builder->fields(['id', 'purpose.name', 'period.name']);
				$builder->orders(['purpose.code' => 'asc', 'period.code' => 'asc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "41f26810185511e79f3ac91fc6c2d825";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.data.doc.list')->entity('suite.amiba.data.doc')
					->fields(['id', 'doc_no', 'doc_date', 'purpose.name', 'period.name', 'use_type_enum', 'fm_group.name', 'to_group.name', 'element.name', 'money']);
				$builder->orders(['purpose.code', 'period.name', 'fm_group.code', 'element.code', 'doc_date' => 'desc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "41f268f0185511e79ed4891bc41aa7ef";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.data.adjust.list')->entity('suite.amiba.data.adjust')
					->fields(['id', 'purpose.name', 'period.name']);
				$builder->orders(['purpose.code' => 'asc', 'period.code' => 'desc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "e18b328036c111e7a66583a070cd15cc";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.data.distribute.list')->entity('suite.amiba.data.distribute')
					->fields(['id', 'purpose.name', 'period.name', 'revoked', 'succeed', 'start_time', 'end_time', 'msg']);
				$builder->orders(['purpose.code' => 'asc', 'period.code' => 'desc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "e18b39a036c111e79dfced4b59bbfd08";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.data.accounting.list')->entity('suite.amiba.data.accounting')
					->fields(['id', 'purpose.name', 'period.name', 'revoked', 'succeed', 'start_time', 'end_time', 'msg']);
				$builder->orders(['purpose.code' => 'asc', 'period.code' => 'desc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "553ea700185811e7bbd4d7067dec57f8";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.data.close.list')->entity('suite.amiba.data.close')
					->fields(['id', 'purpose.name', 'period.name', 'revoked', 'succeed', 'start_time', 'end_time', 'msg']);
				$builder->orders(['purpose.code' => 'asc', 'period.code' => 'desc', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "813a8110208b11e7bac6bf25115d9f22";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.result.profit.list')->entity('suite.amiba.result.profit')
					->fields(['id', 'purpose.name', 'period.name', 'group.name', 'init_profit', 'income', 'cost', 'bal_profit', 'time_profit', 'time_output', 'time_total']);
				$builder->orders(['purpose.code', 'period.code' => 'desc', 'group.code', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "813a7ee0208b11e789ba9ffc001cff1d";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.result.account.list')->entity('suite.amiba.result.account')
					->fields(['id', 'purpose.name', 'period.name', 'group.name', 'element.name', 'type_enum', 'money']);
				$builder->orders(['purpose.code', 'period.code' => 'desc', 'group.code', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "82eede50209611e7937ae36283ac4a86";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.doc.biz.list')->entity('suite.amiba.doc.biz');
				$builder->fields(['id', 'biz_type', 'doc_type', 'doc_no', 'doc_date',
					'fm_org', 'fm_dept', 'fm_wh', 'to_org', 'to_dept', 'to_wh',
					'direction', 'item', 'uom', 'qty', 'price', 'money', 'tax', 'org', 'person',
					'fm_work', 'fm_team', 'fm_person', 'to_work', 'to_team', 'to_person',
					'trader', 'item_category', 'project', 'mfc',
					'factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'data_src_identity',
				]);
				$builder->orders(['biz_type', 'doc_date' => 'desc', 'doc_no' => 'desc', 'item', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

			$id = "82eee120209611e7b5b09379f4a50d53";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('suite.amiba.doc.fi.list')->entity('suite.amiba.doc.fi');
				$builder->orders(['biz_type', 'doc_date' => 'desc', 'doc_no' => 'desc', 'account', 'created_at' => 'desc']);
				$builder->filter('a0.ent_id=#{entId}#');
			});

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Models\Query::where('name', 'like', 'suite.amiba.%.list')->delete();
	}
}
