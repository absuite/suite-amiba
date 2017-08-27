<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Suite\Amiba\Libs\QueryHelper;
use Gmf\Sys\Database\QueryCase;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportGroupTrendAns extends Controller {
	public function index(Request $request) {
		Log::error(static::class);
		$result = [];
		$qc = QueryCase::formatRequest($request);
		$group = QueryHelper::getGroup($qc);

		$query = DB::table('suite_amiba_result_profits as l')
			->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
			->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
		$query->addSelect('p.name as name');
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
		$query->groupBy('p.from_date', 'p.name');
		$query->orderBy('p.from_date');
		$monthData = $query->get();

		$monthData = $monthData->each(function ($item) {
			$item->this_cost = floatval($item->this_cost);
			$item->this_income = floatval($item->this_income);
			$item->this_profit = floatval($item->this_profit);
		});

		$result = $monthData;

		return $this->toJson($result, function ($r) use ($group) {
			$r->group($group);
		});
	}
}
