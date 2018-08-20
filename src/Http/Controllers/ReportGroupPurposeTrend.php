<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Suite\Amiba\Libs\QueryHelper;

class ReportGroupPurposeTrend extends Controller {
	public function index(Request $request) {
		$result = new Builder;

		$qc = QueryCase::formatRequest($request);

		$periods = QueryHelper::getPeriods($qc);
		//实际值
		$query = DB::table('suite_amiba_result_profits as l');
		$query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
		$query->addSelect('l.period_id');
		$query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
		$query->addSelect(DB::raw("SUM(l.income) AS this_income"));
		$query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));

		foreach ($qc->wheres as $key => $value) {
			if ($value->name == 'fm_period') {
				QueryCase::attachWhere($query, $value, 'p.code');
			} else if ($value->name == 'to_period') {
				QueryCase::attachWhere($query, $value, 'p.code');
			} else {
				QueryCase::attachWhere($query, $value, 'l.' . $value->name);
			}
		}

		$groupIds = QueryHelper::geMyGroups();
    if ($groupIds) {
      $query->whereIn('l.group_id', $groupIds);
    }

		$query->groupBy('l.period_id');
		$monthData = $query->get();

		$monthData = $monthData->each(function ($item) {
			$item->this_cost = floatval($item->this_cost);
			$item->this_income = floatval($item->this_income);
			$item->this_profit = floatval($item->this_profit);
		});
		//目标/计划值
		$query = DB::table('suite_amiba_data_targets as t');
		$query->join('suite_amiba_data_target_lines as tl', 't.id', '=', 'tl.target_id');
		$query->join('suite_cbo_period_accounts as fp', 'fp.id', '=', 't.fm_period_id');
		$query->join('suite_cbo_period_accounts as tp', 'tp.id', '=', 't.to_period_id');
		$query->join('suite_cbo_period_accounts as p', 'tp.calendar_id', '=', 'p.calendar_id');

		$query->addSelect('p.id as period_id');
		$query->addSelect(DB::raw("SUM(tl.money) AS money"));

		foreach ($qc->wheres as $key => $value) {
			if ($value->name == 'fm_period') {
				QueryCase::attachWhere($query, $value, 'p.code');
			} else if ($value->name == 'to_period') {
				QueryCase::attachWhere($query, $value, 'p.code');
			} else {
				QueryCase::attachWhere($query, $value, 't.' . $value->name);
			}
		}

		$query->whereColumn('p.from_date', '>=', 'fp.from_date');
		$query->whereColumn('p.from_date', '<=', 'tp.from_date');

		$query->where('tl.type_enum', 'netProfit');
		$query->groupBy('p.id');
		$targetData = $query->get();

		$targetData = $targetData->each(function ($item) {
			$item->money = floatval($item->money);
		});

		$categories = [];
		$datas = [];
		foreach ($periods as $pk => $pv) {
			$categories[] = $pv->name;

			$data = new Builder;
			$data->name($pv->name);
			$data->this_cost(0)->this_income(0)->this_profit(0)->plan_profit(0);
			foreach ($monthData as $mk => $mv) {
				if ($mv->period_id == $pv->id) {
					$data->this_cost($mv->this_cost)->this_income($mv->this_income)->this_profit($mv->this_profit);
				}
			}
			foreach ($targetData as $tk => $tv) {
				if ($tv->period_id == $pv->id) {
					$data->plan_profit($tv->money);
				}
			}
			$datas[] = $data;
		}
		return $this->toJson($datas, function ($b) use ($categories) {
			$b->categories($categories);
		});
	}
}
