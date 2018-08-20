<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Suite\Amiba\Libs\QueryHelper;

class ReportStatementFunctionAns extends Controller {
  public function index(Request $request) {
    Log::error(static::class);
    $result = [];
    $monthData = [];
    $timeData = false;
    $qc = QueryCase::formatRequest($request);
    $period = QueryHelper::getPeriod($qc);
    $group = QueryHelper::getGroup($qc);
    if (empty($period) || empty($group)) {
      return $this->toJson(false);
    }

    $query = DB::table('suite_amiba_result_accounts as l');
    $query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
    $query->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id');

    $query->addSelect('e.id as element_id');
    $query->addSelect(DB::raw("SUM(CASE WHEN p.id='" . $period->id . "' THEN 1  ELSE 0 END * l.money) AS month_value"));
    $query->addSelect(DB::raw("SUM(l.money) as year_value"));

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

    $query->where('p.year', '=', $period->year);
    $query->where('p.from_date', '<=', $period->from_date);

    $query->groupBy('e.id');
    $monthData = $query->get();
    //elements 数据
    $query = DB::table('suite_amiba_elements as l');
    $query->addSelect('l.id');
    $query->addSelect('l.parent_id');
    $query->addSelect('l.name as itemName');
    $query->addSelect('l.direction_enum as direction_enum');
    $query->addSelect('l.type_enum as type_enum');
    $query->addSelect('l.is_manual as is_manual');
    $query->addSelect('l.scope_enum as scope_enum');

    foreach ($qc->wheres as $key => $value) {
      if ($value->name == 'purpose_id') {
        QueryCase::attachWhere($query, $value, 'l.' . $value->name);
      }
    }
    $query->orderBy('l.code');
    $elements = $query->get();
    $elements = $elements->each(function ($item) use ($monthData) {
      $monthData->each(function ($im) use ($item) {
        if ($im->element_id == $item->id) {
          $item->month_value = $im->month_value;
          $item->year_value = $im->year_value;
        }
      });
		});

		$rootNode =new \StdClass;
		$rootNode->indent=-1;
		$rootNode ->nodes= QueryHelper::buildTree($elements);

    /*时间数据*/
    $query = DB::table('suite_amiba_data_time_lines as l');
    $query->join('suite_amiba_data_times as t', 'l.time_id', '=', 't.id');
    $query->join('suite_cbo_period_accounts as p', 't.period_id', '=', 'p.id');

    $query->addSelect(DB::raw("SUM(CASE WHEN p.id='" . $period->id . "' THEN 1  ELSE 0 END * l.total_time) AS time_month"));
    $query->addSelect(DB::raw("SUM(l.total_time) as time_year"));

    foreach ($qc->wheres as $key => $value) {
      if ($value->name == 'group_id') {
        QueryCase::attachWhere($query, $value, 'l.' . $value->name);
      } else if ($value->name == 'purpose_id') {
        QueryCase::attachWhere($query, $value, 't.' . $value->name);
      }
    }
    $query->where('p.year', '=', $period->year);
    $query->where('p.from_date', '<=', $period->from_date);

    $timeData = $query->first();

    //汇总上级
		QueryHelper::sumTreeNodes($rootNode, ['month_value', 'year_value']);
		QueryHelper::itemRatioTreeNodes($rootNode,'month_ratio','month_value');
		QueryHelper::itemRatioTreeNodes($rootNode,'year_ratio','year_value');
		foreach ($rootNode->nodes as $key => $value) {
			
			$value->month_ratio=empty( $value->month_value)?'0.00%':"100%";
			$value->year_ratio=empty( $value->year_value)?'0.00%':"100%";
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
			if ($value->type_enum == 'cost'||$value->type_enum == 'tax') {
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
		$rootNode->nodes[]=$item_profit;
		$rootNode->nodes[]=$item_net_profit;
		

		$result=[];
		
    QueryHelper::appendNodesToArray($result,$rootNode);
    foreach($result as $key => $value) {
      $value->month_value=round($value->month_value,2);
      $value->year_value=round($value->year_value,2);
    }

    return $this->toJson($result);
  }
}
