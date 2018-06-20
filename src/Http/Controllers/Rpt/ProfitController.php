<?php

namespace Suite\Amiba\Http\Controllers\Rpt;

use DB;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfitController extends Controller {
  public function total(Request $request) {
    $size = $request->input('size', 10);

    $query = DB::table('suite_amiba_result_profits as l')
      ->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
      ->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
    $query->addSelect('g.name as group_name');
    $query->addSelect('p.name as period_name');
    $query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
    $query->addSelect(DB::raw("SUM(l.income) AS this_income"));
    $query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));

    if ($v = $request->input('purpose_id')) {
      $query->where('g.purpose_id', $v);
    }
    if ($v = $request->input('period')) {
      $query->where('p.code', $v);
    }
    if ($v = $request->input('group')) {
      $query->where('g.code', $v);
    }
    $query->groupBy('g.name', 'p.name');
    $query->orderBy('g.name');
    $query->orderBy('p.name');

    $pager = $query->paginate($size);
    $items = $pager->getCollection();
    $items = $items->each(function ($item) {
      $item->this_cost = floatval($item->this_cost);
      $item->this_income = floatval($item->this_income);
      $item->this_profit = floatval($item->this_profit);
      $item->this_profit_rate = $item->this_cost != 0 ? ($item->this_profit / $item->this_cost) * 100 : '';
    });
    $pager->setCollection($items);
    return $this->toJson($pager);
  }
  public function rank(Request $request) {
    $size = $request->input('size', 10);

    $query = DB::table('suite_amiba_result_profits as l')
      ->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
      ->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
    $query->addSelect('g.name as group_name');
    $query->addSelect('p.name as period_name');
    $query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
    $query->addSelect(DB::raw("SUM(l.income) AS this_income"));
    $query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));

    if ($v = $request->input('purpose_id')) {
      $query->where('g.purpose_id', $v);
    }
    if ($v = $request->input('period')) {
      $query->where('p.code', $v);
    }
    $query->groupBy('g.name', 'p.name');
    $query->orderBy(DB::raw("SUM(l.income-l.cost)"), 'desc');

    $pager = $query->paginate($size);
    $items = $pager->getCollection();
    $items = $items->each(function ($item) {
      $item->this_cost = floatval($item->this_cost);
      $item->this_income = floatval($item->this_income);
      $item->this_profit = floatval($item->this_profit);
      $item->this_profit_rate = $item->this_cost != 0 ? ($item->this_profit / $item->this_cost) * 100 : '';
    });
    $pager->setCollection($items);
    return $this->toJson($pager);
  }
  public function trend(Request $request) {
    $size = $request->input('size', 10);
    $query = DB::table('suite_amiba_result_profits as l')
      ->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
      ->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
    $query->addSelect('g.name as group_name');
    $query->addSelect('p.name as period_name');
    $query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
    $query->addSelect(DB::raw("SUM(l.income) AS this_income"));
    $query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));

    if ($v = $request->input('purpose_id')) {
      $query->where('g.purpose_id', $v);
    }
    if ($v = $request->input('group')) {
      $query->where('g.code', $v);
    }
    if ($v = $request->input('fm_period')) {
      $query->where('p.code', '>=', $v);
    }
    if ($v = $request->input('to_period')) {
      $query->where('p.code', '<=', $v);
    }
    $query->groupBy('g.name', 'p.name');
    $query->orderBy('g.name');
    $query->orderBy('p.name');

    $pager = $query->paginate($size);
    $items = $pager->getCollection();
    $items = $items->each(function ($item) {
      $item->this_cost = floatval($item->this_cost);
      $item->this_income = floatval($item->this_income);
      $item->this_profit = floatval($item->this_profit);
      $item->this_profit_rate = $item->this_cost != 0 ? ($item->this_profit / $item->this_cost) * 100 : '';
    });
    $pager->setCollection($items);
    return $this->toJson($pager);
  }
}
