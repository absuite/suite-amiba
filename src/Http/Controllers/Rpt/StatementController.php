<?php

namespace Suite\Amiba\Http\Controllers\Rpt;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Suite\Amiba\Libs\QueryHelper;
use Suite\Amiba\Models\Element;

class StatementController extends Controller {
  private function getPeriodItem($periodId) {
    $query = DB::table('suite_cbo_period_accounts as a');
    $query->addSelect('a.id');
    $query->addSelect('a.name');
    $query->addSelect('a.year');
    $query->addSelect('a.month');
    $query->addSelect('a.to_date');
    $query->addSelect('a.from_date');
    $query->where('a.id', $periodId);

    return $query->first();
  }
  public function ans(Request $request) {
    Log::error(static::class);
    $result = [];
    $monthData = [];

    $purpose_id = $request->input('purpose_id');

    $period=$this->getPeriodItem($request->input('period_id'));
    if(empty($period)){
      return $this->toJson($result); 
    }
    $query = DB::table('suite_amiba_result_accounts as l');
    $query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
    $query->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id');

    $query->addSelect('e.id as element_id');
    $query->addSelect(DB::raw("SUM(CASE WHEN p.id='" . $period->id . "' THEN 1  ELSE 0 END * l.money) AS month_value"));
    $query->addSelect(DB::raw("SUM(l.money) as year_value"));

    if ($v = $request->input('purpose_id')) {
      $query->where('l.purpose_id', $v);
    }
    if ($v = $request->input('group_id')) {
      $query->where('l.group_id', $v);
    }
    $groupIds = QueryHelper::geMyGroups();
    if ($groupIds) {
      $query->whereIn('l.group_id', $groupIds);
    }
    $query->where('p.year', '=', $period->year);
    $query->where('p.from_date', '<=', $period->from_date);

    $query->groupBy('e.id');
    $monthData = $query->get();
    //elements 数据
    $elements = Element::with('parent')->whereIn('id', $monthData->pluck('element_id'))->get();
    $elements = $elements->each(function ($item) use ($monthData) {
      $monthData->each(function ($im) use ($item) {
        if ($im->element_id == $item->id) {
          $item->month_value = $im->month_value;
          $item->year_value = $im->year_value;
        }
      });
    });

    $elementNodes = [];
    foreach ($elements as $key => $value) {
      $elementNodes[] = new Builder([
        'id' => $value->id,
        'parent_id' => $value->parent_id,
        'itemName' => $value->name,
        'direction_enum' => $value->direction_enum,
        'type_enum' => $value->type_enum,
        'is_manual' => $value->is_manual,
        'scope_enum' => $value->scope_enum,
        'month_value' => $value->month_value,
        'year_value' => $value->year_value,
      ]);
      $p = $value;
      while (true) {
        if ($p && $p->id != $p->parent_id && !empty($p->parent)) {
          $elementNodes[] = new Builder([
            'id' => $p->parent->id,
            'parent_id' => $p->parent->parent_id,
            'itemName' => $p->parent->name,
            'direction_enum' => $p->parent->direction_enum,
            'type_enum' => $p->parent->type_enum,
            'is_manual' => $p->parent->is_manual,
            'scope_enum' => $p->parent->scope_enum,
          ]);
          $p = $p->parent;
        } else {
          break;
        }
      }
    }
    $rootNode = new \StdClass;
    $rootNode->indent = -1;
    $rootNode->nodes = QueryHelper::buildTree($elementNodes);

    //汇总上级
    QueryHelper::sumTreeNodes($rootNode, ['month_value', 'year_value']);
    QueryHelper::itemRatioTreeNodes($rootNode, 'month_ratio', 'month_value');
    QueryHelper::itemRatioTreeNodes($rootNode, 'year_ratio', 'year_value');
    foreach ($rootNode->nodes as $key => $value) {
      $value->month_ratio = empty($value->month_value) ? '0.00%' : "100%";
      $value->year_ratio = empty($value->year_value) ? '0.00%' : "100%";
    }

    //主营利润，收入合计－成本合计－税金
    $item_profit = new Builder;
    $item_profit->itemName('主营利润')->month_value(0)->year_value(0);

    //净利润，主营利润-费用
    $item_net_profit = new Builder;
    $item_net_profit->itemName('净利润')->month_value(0)->year_value(0);

    foreach ($rootNode->nodes as $key => $value) {
      if ($value->type_enum == 'rcv') {
        $item_profit->month_value = $item_profit->month_value + (empty($value->month_value) ? 0 : $value->month_value);
        $item_profit->year_value = $item_profit->year_value + (empty($value->year_value) ? 0 : $value->year_value);

        $item_net_profit->month_value = $item_net_profit->month_value + (empty($value->month_value) ? 0 : $value->month_value);
        $item_net_profit->year_value = $item_net_profit->year_value + (empty($value->year_value) ? 0 : $value->year_value);
      }
      if ($value->type_enum == 'cost' || $value->type_enum == 'tax') {
        $item_profit->month_value = $item_profit->month_value - (empty($value->month_value) ? 0 : $value->month_value);
        $item_profit->year_value = $item_profit->year_value - (empty($value->year_value) ? 0 : $value->year_value);

        $item_net_profit->month_value = $item_net_profit->month_value - (empty($value->month_value) ? 0 : $value->month_value);
        $item_net_profit->year_value = $item_net_profit->year_value - (empty($value->year_value) ? 0 : $value->year_value);
      }
      if ($value->type_enum == 'free') {
        $item_net_profit->month_value = $item_net_profit->month_value - (empty($value->month_value) ? 0 : $value->month_value);
        $item_net_profit->year_value = $item_net_profit->year_value - (empty($value->year_value) ? 0 : $value->year_value);
      }
    }
    $rootNode->nodes[] = $item_profit;
    $rootNode->nodes[] = $item_net_profit;

    $result = [];

    QueryHelper::appendNodesToArray($result, $rootNode);
    foreach ($result as $key => $value) {
      $value->month_value = round($value->month_value, 2);
      $value->year_value = round($value->year_value, 2);
    }
    foreach ($result as $key => $value) {
      $value->nodes=null;
    }

    return $this->toJson($result);
  }
}
