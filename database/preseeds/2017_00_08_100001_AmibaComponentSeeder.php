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
		$exception = DB::transaction(function () {
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaElementEdit')->name('核算要素')->type_enum('ui')->path('amibaElementEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaElementList')->name('核算要素列表')->type_enum('ui')->path('amibaElementList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaGroupEdit')->name('阿米巴')->type_enum('ui')->path('amibaGroupEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaGroupList')->name('阿米巴列表')->type_enum('ui')->path('amibaGroupList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPurposeEdit')->name('核算目的')->type_enum('ui')->path('amibaPurposeEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPurposeList')->name('核算目的列表')->type_enum('ui')->path('amibaPurposeList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaAllotMethodEdit')->name('分配方法')->type_enum('ui')->path('amibaAllotMethodEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaAllotMethodList')->name('分配方法列表')->type_enum('ui')->path('amibaAllotMethodList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaAllotRuleEdit')->name('分配标准')->type_enum('ui')->path('amibaAllotRuleEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaAllotRuleList')->name('分配标准列表')->type_enum('ui')->path('amibaAllotRuleList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDtiLog')->name('接口日志')->type_enum('ui')->path('amibaDtiLog');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDtiRun')->name('接口执行')->type_enum('ui')->path('amibaDtiRun');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDtiModelingEdit')->name('数据建模')->type_enum('ui')->path('amibaDtiModelingEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaModelingEdit')->name('经营模型')->type_enum('ui')->path('amibaModelingEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaModelingList')->name('经营模型列表')->type_enum('ui')->path('amibaModelingList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataInitEdit')->name('期初')->type_enum('ui')->path('amibaDataInitEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataInitList')->name('期初列表')->type_enum('ui')->path('amibaDataInitList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataTimeEdit')->name('时间数据')->type_enum('ui')->path('amibaDataTimeEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataTimeList')->name('时间数据列表')->type_enum('ui')->path('amibaDataTimeList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataDocEdit')->name('核算数据')->type_enum('ui')->path('amibaDataDocEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataDocList')->name('核算数据列表')->type_enum('ui')->path('amibaDataDocList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataCloseEdit')->name('关账')->type_enum('ui')->path('amibaDataCloseEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataCloseList')->name('关账列表')->type_enum('ui')->path('amibaDataCloseList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataAccountingEdit')->name('核算')->type_enum('ui')->path('amibaDataAccountingEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataAccountingList')->name('核算列表')->type_enum('ui')->path('amibaDataAccountingList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataDistributeEdit')->name('费用分配')->type_enum('ui')->path('amibaDataDistributeEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataDistributeList')->name('费用分配列表')->type_enum('ui')->path('amibaDataDistributeList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceEdit')->name('交易价表')->type_enum('ui')->path('amibaPriceEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceList')->name('交易价表列表')->type_enum('ui')->path('amibaPriceList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceAdjustEdit')->name('调价单')->type_enum('ui')->path('amibaPriceAdjustEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceAdjustList')->name('调价单列表')->type_enum('ui')->path('amibaPriceAdjustList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaPriceQuery')->name('料价查询')->type_enum('ui')->path('amibaPriceQuery');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataTargetEdit')->name('经营目标')->type_enum('ui')->path('amibaDataTargetEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataTargetList')->name('经营目标列表')->type_enum('ui')->path('amibaDataTargetList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataAdjustEdit')->name('责任调整单')->type_enum('ui')->path('amibaDataAdjustEdit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaDataAdjustList')->name('责任调整单列表')->type_enum('ui')->path('amibaDataAdjustList');
			});

			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaQueryAccount')->name('查询-考核结果')->type_enum('ui')->path('amibaQueryAccount');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaQueryProfit')->name('查询-经营台账')->type_enum('ui')->path('amibaQueryProfit');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaQueryDocBiz')->name('查询-业务数据')->type_enum('ui')->path('amibaQueryDocBiz');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('amibaQueryDocFi')->name('查询-财务数据')->type_enum('ui')->path('amibaQueryDocFi');
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

			Models\Component::build(function (Builder $builder) {
				$builder->code('suite.amiba.modeling.project.code.ref')->name('项目');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('suite.amiba.modeling.account.code.ref')->name('科目');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('suite.amiba.modeling.factor1.ref')->name('因素1');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('suite.amiba.modeling.factor2.ref')->name('因素2');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('suite.amiba.modeling.factor3.ref')->name('因素3');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('suite.amiba.modeling.factor4.ref')->name('因素4');
			});
			Models\Component::build(function (Builder $builder) {
				$builder->code('suite.amiba.modeling.factor5.ref')->name('因素5');
			});


			Models\Component::build(function (Builder $builder) {
				$builder->code('AmibaDtiModelingPrice')->name('建模单价异常')->type_enum('ui')->path('AmibaDtiModelingPrice');
			});
		});
	}

}
