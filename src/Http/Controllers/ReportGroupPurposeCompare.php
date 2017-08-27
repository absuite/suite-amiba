<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Suite\Amiba\Libs\QueryHelper;
use Gmf\Sys\Builder;
use Gmf\Sys\Database\QueryCase;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportGroupPurposeCompare extends Controller {
	public function index(Request $request) {
		$qc = QueryCase::formatRequest($request);

		$period = QueryHelper::getPeriod($qc);
		//实际值
		$query = DB::table('suite_amiba_result_profits as l');
		$query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
		$query->addSelect('l.group_id');
		$query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
		$query->addSelect(DB::raw("SUM(l.income) AS this_income"));
		$query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));
		$hasGroup = false;
		foreach ($qc->wheres as $key => $value) {
			if ($value->name == 'fm_period') {
				QueryCase::attachWhere($query, $value, 'p.code');
			} else if ($value->name == 'to_period') {
				QueryCase::attachWhere($query, $value, 'p.code');
			} else {
				QueryCase::attachWhere($query, $value, 'l.' . $value->name);
			}
			if ($value->name == 'group_id') {
				$hasGroup = true;
			}
		}

		$groups = QueryHelper::getGroups($qc, !$hasGroup);

		$query->groupBy('l.group_id');
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
		$query->addSelect('t.group_id as group_id');
		$query->addSelect(DB::raw("SUM(tl.money) AS money"));
		$query->whereIn('t.group_id', $groups->pluck('id')->all());
		$query->where('fp.from_date', '<=', $period->to_date);
		$query->where('tp.to_date', '>=', $period->from_date);
		$query->where('tl.type_enum', 'netProfit');
		$query->groupBy('t.group_id');
		$targetData = $query->get();

		$targetData = $targetData->each(function ($item) {
			$item->money = floatval($item->money);
		});

		$categories = [];
		$datas = [];
		foreach ($groups as $gk => $gv) {
			$data = new Builder;
			$data->name($gv->name);
			$data->this_cost(0)->this_income(0)->this_profit(0)->plan_profit(0);
			foreach ($monthData as $mk => $mv) {
				if ($mv->group_id == $gv->id) {
					$data->this_cost($mv->this_cost)->this_income($mv->this_income)->this_profit($mv->this_profit);
				}
			}
			foreach ($targetData as $tk => $tv) {
				if ($tv->group_id == $gv->id) {
					$data->plan_profit($tv->money);
				}
			}
			$datas[] = $data;
		}
		return $this->toJson($datas);
	}
}
