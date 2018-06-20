<?php

namespace Suite\Amiba\Http\Controllers\Rpt;

use DB;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpendController extends Controller {
  public function analy(Request $request) {
    $size = $request->input('size', 10);
    $query = DB::table('suite_amiba_result_accounts as l')
      ->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
      ->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id')
      ->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
    $query->addSelect('g.name as group_name');
    $query->addSelect('p.name as period_name');
    $query->addSelect('e.name as element_name');
    $query->addSelect(DB::raw("SUM(l.money) AS value"));

    if ($v = $request->input('purpose_id')) {
      $query->where('g.purpose_id', $v);
    }
    if ($v = $request->input('group')) {
      $query->where('g.code', $v);
    }
    if ($v = $request->input('period')) {
      $query->where('p.code', '=', $v);
    }
    $query->where('e.type_enum', '=', 'cost');

    $query->groupBy('g.name', 'p.name');
    $query->groupBy('e.name');
    $query->orderBy('g.name');
    $query->orderBy('p.name');
    $query->orderBy('e.name');

    $pager = $query->paginate($size);
    $items = $pager->getCollection();
    $items = $items->each(function ($item) {
      $item->value = floatval($item->value);
    });
    $pager->setCollection($items);
    return $this->toJson($pager);
  }
}
