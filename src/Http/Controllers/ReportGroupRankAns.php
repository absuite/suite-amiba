<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Database\QueryCase;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportGroupRankAns extends Controller {
	public function index(Request $request) {
		$result = [];

		$query = DB::table('suite_amiba_result_profits as l')
			->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
			->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
		$query->addSelect('g.name as name');
		$query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
		$query->addSelect(DB::raw("SUM(l.income) AS this_income"));
		$query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));

		$qc = QueryCase::formatRequest($request);
		foreach ($qc->wheres as $key => $value) {
			if ($value->name == 'fm_period_id') {
				QueryCase::attachWhere($query, $value, 'p.code');
			} else if ($value->name == 'to_period_id') {
				QueryCase::attachWhere($query, $value, 'p.code');
			} else {
				QueryCase::attachWhere($query, $value, 'l.' . $value->name);
			}
		}
		$query->where('g.is_leaf', '1');
		$query->groupBy('g.name');
		$query->orderBy(DB::raw("SUM(l.income-l.cost)"), 'desc');
		$monthData = $query->get();

		$monthData = $monthData->each(function ($item) {
			$item->this_cost = floatval($item->this_cost);
			$item->this_income = floatval($item->this_income);
			$item->this_profit = floatval($item->this_profit);
		});

		$result = $monthData;

		return $this->toJson($result);
	}
}
