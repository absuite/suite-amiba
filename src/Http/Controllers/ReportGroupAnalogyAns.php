<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Suite\Amiba\Libs\QueryHelper;

class ReportGroupAnalogyAns extends Controller {
  public function index(Request $request) {
    $result = new Builder;
    $qc = QueryCase::formatRequest($request);

    $periods = QueryHelper::getPeriods($qc);
    $hasGroup = false;
    $query = DB::table('suite_amiba_result_profits as l')
      ->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
      ->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
    $query->addSelect('g.name as name');
    $query->addSelect('l.period_id');
    $query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
    $query->addSelect(DB::raw("SUM(l.income) AS this_income"));
    $query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));

    foreach ($qc->wheres as $key => $value) {
      if ($value->name == 'fm_period') {
        QueryCase::attachWhere($query, $value, 'p.code');
      } else if ($value->name == 'to_period') {
        QueryCase::attachWhere($query, $value, 'p.code');
      } else if ($value->name == 'group_id') {
        QueryCase::attachWhere($query, $value, 'l.' . $value->name);
        $hasGroup = true;
      } else {
        QueryCase::attachWhere($query, $value, 'l.' . $value->name);
      }
    }
    $groupIds = QueryHelper::geMyGroups();
    if ($groupIds) {
      $query->whereIn('l.group_id', $groupIds);
    }
    if (!$hasGroup) {
      $query->where('g.is_leaf', '1');
    }

    $query->groupBy('g.name');
    $query->groupBy('l.period_id');
    $query->orderBy(DB::raw("SUM(l.income-l.cost)"), 'desc');
    $monthData = $query->get();

    $monthData = $monthData->each(function ($item) {
      $item->this_cost = floatval($item->this_cost);
      $item->this_income = floatval($item->this_income);
      $item->this_profit = floatval($item->this_profit);
    });
    $grouped = $monthData->groupBy('name');
    $grouped = $grouped->all();

    $categories = [];
    foreach ($periods as $key => $value) {
      $categories[] = $value->name;
    }
    $datas = [];
    foreach ($grouped as $key => $value) {
      $data = new Builder;
      $data->name($key);

      $data_this_cost = [];
      $data_this_income = [];
      $data_this_profit = [];
      foreach ($periods as $pk => $pv) {
        $data_v = [];
        $data_v[0] = 0;
        $data_v[1] = 0;
        $data_v[2] = 0;
        foreach ($value as $vk => $vv) {
          if ($vv->period_id === $pv->id) {
            $data_v[0] = $vv->this_cost;
            $data_v[1] = $vv->this_income;
            $data_v[2] = $vv->this_profit;
          }
        }
        $data_this_cost[] = $data_v[0];
        $data_this_income[] = $data_v[1];
        $data_this_profit[] = $data_v[2];
      }
      $data->cost($data_this_cost)->income($data_this_income)->profit($data_this_profit);

      $datas[] = $data;
    }
    return $this->toJson($datas, function ($b) use ($categories) {
      $b->categories($categories);
    });
  }
}
