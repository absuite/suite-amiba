<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Suite\Amiba\Libs\QueryHelper;

class ReportStatementCompare extends Controller {
  public function index(Request $request) {
    Log::error(static::class);
    $result = [];
    $queryData = [];
    $timeData = false;
    $manualData = false;
    $employeeData = false;

    $qc = QueryCase::formatRequest($request);
    $period = QueryHelper::getPeriod($qc);
    $groups = QueryHelper::getGroups($qc);
    $dataCategories = [];
    $dataSumFields = [];
    if ($period && $groups) {

      $query = DB::table('suite_amiba_result_accounts as l');
      $query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
      $query->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id');

      $query->addSelect('e.name as elementName');
      $query->addSelect('e.type_enum as elementType');
      $query->addSelect('e.scope_enum as dataType');
      foreach ($groups as $mk => $mv) {
        $query->addSelect(DB::raw("SUM(CASE WHEN l.group_id='" . $mv->id . "' and p.id='" . $period->id . "' THEN 1  ELSE 0 END * l.money) AS money_m_" . $mk));
        $query->addSelect(DB::raw("SUM(CASE WHEN l.group_id='" . $mv->id . "' and p.month<='" . $period->month . "' THEN 1 ELSE 0 END * l.money) AS money_y_" . $mk));

        $dataCategories[] = $mv->name;

        $dataSumFields[] = 'money_m_' . $mk;
        $dataSumFields[] = 'money_y_' . $mk;
      }
      foreach ($qc->wheres as $key => $value) {
        if ($value->name == 'purpose_id') {
          QueryCase::attachWhere($query, $value, 'l.' . $value->name);
        } else if ($value->name == 'group_id') {
          QueryCase::attachWhere($query, $value, 'l.' . $value->name);
        }
      }
      $groupIds = QueryHelper::geMyGroups();
      if ($groupIds) {
        $query->whereIn('l.group_id', $groupIds);
      }
      $query->where('p.year', $period->year);
      $query->where('p.month', '<=', $period->month);

      $query->groupBy('e.name', 'e.type_enum', 'e.scope_enum');
      $queryData = $query->get();

      /*时间数据*/
      $query = DB::table('suite_amiba_data_time_lines as l');
      $query->join('suite_amiba_data_times as t', 'l.time_id', '=', 't.id');
      $query->join('suite_cbo_period_accounts as p', 't.period_id', '=', 'p.id');

      foreach ($groups as $mk => $mv) {
        $query->addSelect(DB::raw("SUM(CASE WHEN l.group_id='" . $mv->id . "' and p.id='" . $period->id . "' THEN 1  ELSE 0 END * l.total_time) AS time_m_" . $mk));
        $query->addSelect(DB::raw("SUM(CASE WHEN l.group_id='" . $mv->id . "' and p.year='" . $period->year . "' and p.month<='" . $period->month . "' THEN 1 ELSE 0 END * l.total_time) AS time_y_" . $mk));
      }
      foreach ($qc->wheres as $key => $value) {
        if ($value->name == 'group_id') {
          QueryCase::attachWhere($query, $value, 'l.' . $value->name);
        } else if ($value->name == 'purpose_id') {
          QueryCase::attachWhere($query, $value, 't.' . $value->name);
        }
      }
      $query->where('p.year', $period->year);
      $query->where('p.month', '<=', $period->month);
      $timeData = $query->first();

      //人工成本
      $query = DB::table('suite_amiba_result_accounts as l');
      $query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
      $query->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id');

      foreach ($groups as $mk => $mv) {
        $query->addSelect(DB::raw("SUM(CASE WHEN l.group_id='" . $mv->id . "' and p.id='" . $period->id . "' THEN 1  ELSE 0 END * l.money) AS manual_m_" . $mk));
        $query->addSelect(DB::raw("SUM(CASE WHEN l.group_id='" . $mv->id . "' and p.year='" . $period->year . "' and p.month<='" . $period->month . "' THEN 1 ELSE 0 END * l.money) AS manual_y_" . $mk));
      }
      foreach ($qc->wheres as $key => $value) {
        if ($value->name == 'group_id') {
          QueryCase::attachWhere($query, $value, 'l.' . $value->name);
        } else if ($value->name == 'purpose_id') {
          QueryCase::attachWhere($query, $value, 'l.' . $value->name);
        }
      }
      $query->where('e.is_manual', '1');
      $query->where('p.year', $period->year);
      $query->where('p.month', '<=', $period->month);

      $manualData = $query->first();

      //劳动人数
      $query = DB::table('suite_amiba_groups as l');
      foreach ($groups as $mk => $mv) {
        $query->addSelect(DB::raw("SUM(CASE WHEN l.id='" . $mv->id . "' THEN 1  ELSE 0 END * l.employees) AS employee_m_" . $mk));
      }
      foreach ($qc->wheres as $key => $value) {
        if ($value->name == 'group_id') {
          QueryCase::attachWhere($query, $value, 'l.id');
        } else if ($value->name == 'purpose_id') {
          QueryCase::attachWhere($query, $value, 'l.' . $value->name);
        }
      }
      $employeeData = $query->first();
    }
    //收入
    $item_rcv = new Builder;
    $item_rcv->itemName('收入')->nodes([])->indent(0);

    $item_rcv_out = new Builder;
    $item_rcv_out->itemName('外部收入')->nodes([])->indent(1);

    $item_rcv_in = new Builder;
    $item_rcv_in->itemName('内部收入')->nodes([])->indent(1);

    //成本
    $item_cost = new Builder;
    $item_cost->itemName('成本')->nodes([])->indent(0);

    $item_cost_in = new Builder;
    $item_cost_in->itemName('内采成本')->nodes([])->indent(1);

    $item_cost_out = new Builder;
    $item_cost_out->itemName('外部成本')->nodes([])->indent(1);

    //税金及附加
    $item_tax = new Builder;
    $item_tax->itemName('税金及附加')->nodes([])->indent(0);

    $item_free = new Builder;
    $item_free->itemName('费用')->nodes([])->indent(0);

    foreach ($queryData as $key => $v) {
      $item = new Builder;
      $item->itemName($v->elementName)->indent(2);
      foreach ($dataSumFields as $pk => $pv) {
        $item->{$pv} = $v->{$pv};
      }
      if ($v->elementType == 'rcv') {
        if ($v->dataType == 'outside') {
          $array = $item_rcv_out->nodes;
          $array[] = $item;
          $item_rcv_out->nodes = $array;
        } else {
          $array = $item_rcv_in->nodes;
          $array[] = $item;
          $item_rcv_in->nodes = $array;
        }
      }
      if ($v->elementType == 'cost') {
        if ($v->dataType == 'outside') {
          $array = $item_cost_out->nodes;
          $array[] = $item;
          $item_cost_out->nodes = $array;
        } else {
          $array = $item_cost_in->nodes;
          $array[] = $item;
          $item_cost_in->nodes = $array;
        }
      }
      if ($v->elementType == 'free') {
        $array = $item_free->nodes;
        $array[] = $item;
        $item_free->nodes = $array;
      }
      if ($v->elementType == 'tax') {
        $array = $item_tax->nodes;
        $array[] = $item;
        $item_tax->nodes = $array;
      }
    }

    $array = $item_rcv->nodes;
    $array[] = $item_rcv_out;
    $array[] = $item_rcv_in;
    $item_rcv->nodes = $array;

    $array = $item_cost->nodes;
    $array[] = $item_cost_in;
    $array[] = $item_cost_out;
    $item_cost->nodes = $array;

    //汇总上级
    QueryHelper::sumTreeNodes($item_rcv, $dataSumFields);
    QueryHelper::sumTreeNodes($item_cost, $dataSumFields);
    QueryHelper::sumTreeNodes($item_tax, $dataSumFields);
    QueryHelper::sumTreeNodes($item_free, $dataSumFields);

    //主营利润，收入合计－成本合计－税金
    $item_profit = new Builder;
    $item_profit->itemName('主营利润')->indent(0);
    foreach ($dataSumFields as $pk => $pv) {
      $item_profit->{$pv} = $item_rcv->{$pv}-$item_cost->{$pv}-$item_tax->{$pv};
    }

    //净利润，主营利润-费用
    $item_net_profit = new Builder;
    $item_net_profit->itemName('净利润')->indent(0);
    foreach ($dataSumFields as $pk => $pv) {
      $item_net_profit->{$pv} = $item_profit->{$pv}-$item_free->{$pv};
    }

    array_push($result, $item_rcv);
    QueryHelper::appendNodesToArray($result, $item_rcv);
    array_push($result, $item_cost);
    QueryHelper::appendNodesToArray($result, $item_cost);
    array_push($result, $item_tax);
    QueryHelper::appendNodesToArray($result, $item_tax);
    array_push($result, $item_profit);
    QueryHelper::appendNodesToArray($result, $item_profit);
    array_push($result, $item_free);
    QueryHelper::appendNodesToArray($result, $item_free);
    array_push($result, $item_net_profit);
    QueryHelper::appendNodesToArray($result, $item_net_profit);

    //计算比例
    foreach ($result as $key => $value) {
      foreach ($dataSumFields as $fk => $fv) {
        if ($item_rcv->{$fv} != 0) {
          $value->{$fv . '_ratio'} = round(($value->{$fv} * 100 / $item_rcv->{$fv}), 2) . '%';
        } else {
          $value->{$fv . '_ratio'} = '0%';
        }
      }
    }

    //收入/劳动人数
    $item = new Builder;
    $item->itemName('劳动人数')->indent(0);
    if ($employeeData) {
      foreach ($dataCategories as $ck => $cv) {
        $item->{'money_m_' . $ck} = $employeeData->{'employee_m_' . $ck};
        if ($employeeData->{'employee_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck . '_ratio'} = round($item_rcv->{'money_m_' . $ck} / $employeeData->{'employee_m_' . $ck}, 2);
        }
      }
    }
    array_push($result, $item);

    //劳动时间=收入/劳动时间
    $item = new Builder;
    $item->itemName('劳动时间')->indent(0);
    if ($timeData) {
      foreach ($dataCategories as $ck => $cv) {
        $item->{'money_m_' . $ck} = $timeData->{'time_m_' . $ck};
        if ($timeData->{'time_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck . '_ratio'} = round($item_rcv->{'money_m_' . $ck} / $timeData->{'time_m_' . $ck}, 2);
        }
      }
    }
    array_push($result, $item);

    //人工成本=收入/人工成本
    $item = new Builder;
    $item->itemName('人工成本')->indent(0);
    if ($manualData) {
      foreach ($dataCategories as $ck => $cv) {
        $item->{'money_m_' . $ck} = $manualData->{'manual_m_' . $ck};
        if ($manualData->{'manual_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck . '_ratio'} = round($item_rcv->{'money_m_' . $ck} / $manualData->{'manual_m_' . $ck}, 2);
        }
      }
    }
    array_push($result, $item);

    //人均毛利=毛利/劳动人数
    $item = new Builder;
    $item->itemName('人均毛利')->indent(0);
    if ($employeeData && $item_profit) {
      foreach ($dataCategories as $ck => $cv) {
        if ($employeeData->{'employee_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck} = round($item_profit->{'money_m_' . $ck} / $employeeData->{'employee_m_' . $ck}, 2);
        }
      }
    }
    array_push($result, $item);

    //人均净利=净利/劳动人数
    $item = new Builder;
    $item->itemName('人均净利')->indent(0);
    if ($employeeData && $item_net_profit) {
      foreach ($dataCategories as $ck => $cv) {
        if ($employeeData->{'employee_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck} = round($item_net_profit->{'money_m_' . $ck} / $employeeData->{'employee_m_' . $ck}, 2);
        }

      }
    }
    array_push($result, $item);

    //工作效率=毛利/劳动时间
    $item = new Builder;
    $item->itemName('工作效率')->indent(0);
    if ($timeData && $item_profit) {
      foreach ($dataCategories as $ck => $cv) {
        if ($timeData->{'time_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck} = round($item_profit->{'money_m_' . $ck} / $timeData->{'time_m_' . $ck}, 2);
        }
      }
    }
    array_push($result, $item);

    //经营效率=净利/劳动时间
    $item = new Builder;
    $item->itemName('经营效率')->indent(0);
    if ($timeData && $item_net_profit) {
      foreach ($dataCategories as $ck => $cv) {
        if ($timeData->{'time_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck} = round($item_net_profit->{'money_m_' . $ck} / $timeData->{'time_m_' . $ck}, 2);
        }
      }
    }
    array_push($result, $item);

    //工作效益=毛利/人工成本
    $item = new Builder;
    $item->itemName('工作效益')->indent(0);
    if ($manualData && $item_profit) {
      foreach ($dataCategories as $ck => $cv) {
        if ($manualData->{'manual_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck} = round($item_profit->{'money_m_' . $ck} / $manualData->{'manual_m_' . $ck}, 2);
        }
      }
    }
    array_push($result, $item);

    //经营效益=净利/人工成本
    $item = new Builder;
    $item->itemName('经营效益')->indent(0);
    if ($manualData && $item_net_profit) {
      foreach ($dataCategories as $ck => $cv) {
        if ($manualData->{'manual_m_' . $ck} != 0) {
          $item->{'money_m_' . $ck} = round($item_net_profit->{'money_m_' . $ck} / $manualData->{'manual_m_' . $ck}, 2);
        }
      }
    }
    array_push($result, $item);
    //构造结果
    $resultFormats = [];
    foreach ($result as $rk => $rv) {
      $item = new Builder;
      $item->itemName($rv->itemName)->indent($rv->indent);
      $categoryItems = [];
      foreach ($dataCategories as $ck => $cv) {
        $categoryItem = new Builder;
        $categoryItem->name($cv);
        $categoryItem->money_month($rv->{'money_m_' . $ck});
        $categoryItem->money_month_ratio($rv->{'money_m_' . $ck . '_ratio'});
        $categoryItem->money_year($rv->{'money_y_' . $ck});
        $categoryItem->money_year_ratio($rv->{'money_y_' . $ck . '_ratio'});

        $categoryItems[] = $categoryItem;
      }
      $item->categories($categoryItems);
      $resultFormats[] = $item;
    }
    unset($result);
    return $this->toJson($resultFormats, function ($b) use ($dataCategories) {
      $b->categories($dataCategories);
    });

  }
}
