<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;

class ReportGroupStructureAns extends Controller {
	public function index(Request $request) {
		$result = new Builder;

		$qc = QueryCase::formatRequest($request);
		//实际值
		$query = DB::table('suite_amiba_result_profits as l');
		$query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
		$query->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');

		$query->addSelect('p.name as periodName');
		$query->addSelect('g.name as groupName');

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
		$query->groupBy('p.name', 'g.name');
		$query->orderBy('g.name');
		$query->orderBy('p.name');

		$monthData = $query->get();

		return $this->toJson($monthData);
	}
}
