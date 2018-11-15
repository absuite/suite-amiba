<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Suite\Amiba\Libs\QueryHelper;
use Suite\Amiba\Models\Element;

class ReportStatementTrend extends Controller {
  public function index(Request $request) {
    Log::error(static::class);
    $result = [];
    $queryData = [];
    $manualData = false;
    $qc = QueryCase::formatRequest($request);
    $periods = QueryHelper::getPeriods($qc);
    $group = QueryHelper::getGroup($qc);

    $dataCategories = [];
    $dataSumFields = [];

    if (empty($periods) || empty($group)) {
      return $this->toJson(false);
    }

    $lastPeriod = $periods->last();

    $query = DB::table('suite_amiba_result_accounts as l');
    $query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
    $query->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id');
    $query->addSelect('e.id as element_id');
    foreach ($periods as $mk => $mv) {
      $query->addSelect(DB::raw("SUM(CASE WHEN p.id='" . $mv->id . "' THEN 1  ELSE 0 END * l.money) AS money_m_" . $mv->name));
      $query->addSelect(DB::raw("SUM(CASE WHEN p.year='" . $mv->year . "' and p.month<='" . $mv->month . "' THEN 1 ELSE 0 END * l.money) AS money_y_" . $mv->name));

      $dataCategories[] = $mv->name;

      $dataSumFields[] = 'money_m_' . $mv->name;
      $dataSumFields[] = 'money_y_' . $mv->name;
    }
    foreach ($qc->wheres as $key => $value) {
      if ($value->name == 'group_id') {
        QueryCase::attachWhere($query, $value, 'l.' . $value->name);
      } else if ($value->name == 'purpose_id') {
        QueryCase::attachWhere($query, $value, 'l.' . $value->name);
      }
    }
    $groupIds = QueryHelper::geMyGroups();
    if ($groupIds) {
      $query->whereIn('l.group_id', $groupIds);
    }
    $query->whereIn('p.year', $periods->groupBy('year')->keys()->all());
    $query->where('p.month', '<=', $lastPeriod->month);

    $query->groupBy('e.id');
    $monthData = $query->get();

    //elements 数据
    $elements = Element::with('parent')->whereIn('id', $monthData->pluck('element_id'))->get();
    $elements = $elements->each(function ($item) use ($monthData, $dataCategories) {
      $monthData->each(function ($im) use ($item, $dataCategories) {
        if ($im->element_id == $item->id) {
          foreach ($dataCategories as $ci => $cv) {
            $item->{"money_m_" . $cv} = $im->{"money_m_" . $cv};
            $item->{"money_y_" . $cv} = $im->{"money_y_" . $cv};
          }
        }
      });
    });

    $elementMap = [];
    foreach ($elements as $key => $value) {
      $elementMapItem = new Builder([
        'id' => $value->id,
        'parent_id' => $value->parent_id,
        'itemName' => $value->name,
        'direction_enum' => $value->direction_enum,
        'type_enum' => $value->type_enum,
        'is_manual' => $value->is_manual,
        'scope_enum' => $value->scope_enum,
      ]);
      foreach ($dataCategories as $ci => $cv) {
        $elementMapItem->{"money_m_" . $cv} = $value->{"money_m_" . $cv};
        $elementMapItem->{"money_y_" . $cv} = $value->{"money_y_" . $cv};
      }
      $elementMap[$value->id] = $elementMapItem;
      $p = $value;
      while (true) {
        if ($p && $p->id != $p->parent_id && !empty($p->parent) && $p->parent_id == $p->parent->id) {
          $elementMap[$p->parent->id] = new Builder([
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
    $elements = [];
    foreach ($elementMap as $key => $value) {
      $elements[] = $value;
    }
    $rootNode = new \StdClass;
    $rootNode->indent = -1;
    $rootNode->nodes = QueryHelper::buildTree($elements);

    //汇总上级
    QueryHelper::sumTreeNodes($rootNode, $dataSumFields);
    //主营利润，收入合计－成本合计－税金
    $item_profit = new Builder;
    $item_profit->itemName('主营利润');
    foreach ($dataCategories as $ci => $cv) {
      $item_profit->{"money_m_" . $cv} = 0;
      $item_profit->{"money_y_" . $cv} = 0;
    }

    //净利润，主营利润-费用
    $item_net_profit = new Builder;
    $item_net_profit->itemName('净利润');
    foreach ($dataCategories as $ci => $cv) {
      $item_net_profit->{"money_m_" . $cv} = 0;
      $item_net_profit->{"money_y_" . $cv} = 0;
    }
    foreach ($rootNode->nodes as $key => $value) {
      if ($value->type_enum == 'rcv') {
        foreach ($dataCategories as $ci => $cv) {
          if (!empty($value->{"money_m_" . $cv})) {
            $item_profit->{"money_m_" . $cv} = $item_profit->{"money_m_" . $cv}+$value->{"money_m_" . $cv};
            $item_net_profit->{"money_m_" . $cv} = $item_net_profit->{"money_m_" . $cv}+$value->{"money_m_" . $cv};
          }
          if (!empty($value->{"money_y_" . $cv})) {
            $item_profit->{"money_y_" . $cv} = $item_profit->{"money_y_" . $cv}+$value->{"money_y_" . $cv};
            $item_net_profit->{"money_y_" . $cv} = $item_net_profit->{"money_y_" . $cv}+$value->{"money_y_" . $cv};
          }
        }
      }
      if ($value->type_enum == 'cost' || $value->type_enum == 'tax') {
        foreach ($dataCategories as $ci => $cv) {
          if (!empty($value->{"money_m_" . $cv})) {
            $item_profit->{"money_m_" . $cv} = $item_profit->{"money_m_" . $cv}-$value->{"money_m_" . $cv};
            $item_net_profit->{"money_m_" . $cv} = $item_net_profit->{"money_m_" . $cv}-$value->{"money_m_" . $cv};
          }
          if (!empty($value->{"money_y_" . $cv})) {
            $item_profit->{"money_y_" . $cv} = $item_profit->{"money_y_" . $cv}-$value->{"money_y_" . $cv};
            $item_net_profit->{"money_y_" . $cv} = $item_net_profit->{"money_y_" . $cv}-$value->{"money_y_" . $cv};
          }
        }
      }
      if ($value->type_enum == 'free') {
        foreach ($dataCategories as $ci => $cv) {
          if (!empty($value->{"money_m_" . $cv})) {
            $item_net_profit->{"money_m_" . $cv} = $item_net_profit->{"money_m_" . $cv}-$value->{"money_m_" . $cv};
          }
          if (!empty($value->{"money_y_" . $cv})) {
            $item_net_profit->{"money_y_" . $cv} = $item_net_profit->{"money_y_" . $cv}-$value->{"money_y_" . $cv};
          }
        }
      }
    }
    $rootNode->nodes[] = $item_profit;
    $rootNode->nodes[] = $item_net_profit;

    $result = [];

    QueryHelper::appendNodesToArray($result, $rootNode);
    foreach ($result as $key => $value) {
      foreach ($dataCategories as $ci => $cv) {
        $value->{"money_m_" . $cv} = round($value->{"money_m_" . $cv}, 2);
        $value->{"money_y_" . $cv} = round($value->{"money_y_" . $cv}, 2);
      }
    }
    return $this->toJson($result, function ($b) use ($dataCategories) {
      $b->categories($dataCategories);
    });
  }
}
