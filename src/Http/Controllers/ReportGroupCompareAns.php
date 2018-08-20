<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Suite\Amiba\Libs\QueryHelper;

class ReportGroupCompareAns extends Controller {
  public function index(Request $request) {
    $result = [];
    $qc = QueryCase::formatRequest($request);
    $query = DB::table('suite_amiba_result_profits as l')
      ->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
      ->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
    $query->addSelect('g.name as name');
    $query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
    $query->addSelect(DB::raw("SUM(l.income) AS this_income"));
    $query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));

    $hasGroup = false;
    foreach ($qc->wheres as $key => $value) {
      if ($value->name == 'fm_period_id') {
        QueryCase::attachWhere($query, $value, 'p.code');
      } else if ($value->name == 'to_period_id') {
        QueryCase::attachWhere($query, $value, 'p.code');
      } else {
        QueryCase::attachWhere($query, $value, 'l.' . $value->name);
      }
      if ($value->name == 'group_id') {
        $hasGroup = true;
      }
    }
    if (!$hasGroup) {
      $query->where('g.is_leaf', '1');
    }
    $groupIds = QueryHelper::geMyGroups();
    if ($groupIds) {
      $query->whereIn('l.group_id', $groupIds);
    }
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
