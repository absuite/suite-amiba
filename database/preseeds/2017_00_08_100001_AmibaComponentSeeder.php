<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmibaComponentSeeder extends Seeder {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		$this->down();
		$exception = DB::transaction(function () {
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaElementEdit')->name('核算要素')->path('amibaElementEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaElementList')->name('核算要素列表')->path('amibaElementList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaGroupEdit')->name('阿米巴')->path('amibaGroupEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaGroupList')->name('阿米巴列表')->path('amibaGroupList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPurposeEdit')->name('核算目的')->path('amibaPurposeEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPurposeList')->name('核算目的列表')->path('amibaPurposeList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaAllotMethodEdit')->name('分配方法')->path('amibaAllotMethodEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaAllotMethodList')->name('分配方法列表')->path('amibaAllotMethodList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaAllotRuleEdit')->name('分配标准')->path('amibaAllotRuleEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaAllotRuleList')->name('分配标准列表')->path('amibaAllotRuleList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDtiLog')->name('接口日志')->path('amibaDtiLog');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDtiRun')->name('接口执行')->path('amibaDtiRun');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDtiModelingEdit')->name('数据建模')->path('amibaDtiModelingEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaModelingEdit')->name('经营模型')->path('amibaModelingEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaModelingList')->name('经营模型列表')->path('amibaModelingList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataInitEdit')->name('期初')->path('amibaDataInitEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataInitList')->name('期初列表')->path('amibaDataInitList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataTimeEdit')->name('时间数据')->path('amibaDataTimeEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataTimeList')->name('时间数据列表')->path('amibaDataTimeList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataDocEdit')->name('核算数据')->path('amibaDataDocEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataDocList')->name('核算数据列表')->path('amibaDataDocList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataCloseEdit')->name('关账')->path('amibaDataCloseEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataCloseList')->name('关账列表')->path('amibaDataCloseList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataAccountingEdit')->name('核算')->path('amibaDataAccountingEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataAccountingList')->name('核算列表')->path('amibaDataAccountingList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataDistributeEdit')->name('费用分配')->path('amibaDataDistributeEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataDistributeList')->name('费用分配列表')->path('amibaDataDistributeList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceEdit')->name('交易价表')->path('amibaPriceEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceList')->name('交易价表列表')->path('amibaPriceList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceAdjustEdit')->name('调价单')->path('amibaPriceAdjustEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceAdjustList')->name('调价单列表')->path('amibaPriceAdjustList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaQueryAccount')->name('查询-考核结果')->path('amibaQueryAccount');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaQueryProfit')->name('查询-经营台账')->path('amibaQueryProfit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaQueryDocBiz')->name('查询-业务数据')->path('amibaQueryDocBiz');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaQueryDocFi')->name('查询-财务数据')->path('amibaQueryDocFi');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportGroupTrendAns')->name('报表-趋势分析');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportGroupCompareAns')->name('报表-比较分析');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportGroupAnalogyAns')->name('报表-类比分析');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportGroupRankAns')->name('报表-排名分析');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportGroupPurposeTrend')->name('报表-目标达成趋势分析');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportGroupPurposeCompare')->name('报表-目标达成比较分析');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportStatementFunctionAns')->name('报表-职能式损益表');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportStatementDevoteAns')->name('报表-贡献式损益表');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportStatementCompare')->name('报表-损益横比');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportStatementTrend')->name('报表-损益趋势分析');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaReportStatementPurpose')->name('报表-损益目标达成');
			});
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Models\Component::where('code', 'like', 'amiba.%')->delete();
	}
}
