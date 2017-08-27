<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Suite\Amiba\Libs\QueryHelper;
use Gmf\Sys\Builder;
use Gmf\Sys\Database\QueryCase;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportStatementFunctionAns extends Controller {
	public function index(Request $request) {
		Log::error(static::class);
		$result = [];
		$monthData = [];
		$timeData = false;
		$qc = QueryCase::formatRequest($request);
		$period = QueryHelper::getPeriod($qc);
		$group = QueryHelper::getGroup($qc);
		if ($period && $group) {

			$query = DB::table('suite_amiba_result_accounts as l');
			$query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
			$query->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id');

			$query->addSelect('e.name as elementName');
			$query->addSelect('e.direction_enum as elementDirection');
			$query->addSelect('e.type_enum as elementType');
			$query->addSelect('e.is_manual as element_is_manual');
			$query->addSelect('l.type_enum as dataType');

			$query->addSelect(DB::raw("SUM(CASE WHEN p.id='" . $period->id . "' THEN 1  ELSE 0 END * l.money) AS money"));
			$query->addSelect(DB::raw("SUM(l.money) as year_money"));

			foreach ($qc->wheres as $key => $value) {
				if ($value->name == 'group_id') {
					QueryCase::attachWhere($query, $value, 'l.' . $value->name);
				} else if ($value->name == 'purpose_id') {
					QueryCase::attachWhere($query, $value, 'l.' . $value->name);
				}
			}

			$query->where('p.year', '=', $period->year);
			$query->where('p.from_date', '<=', $period->from_date);

			$query->groupBy('e.name', 'e.direction_enum', 'e.type_enum', 'l.type_enum', 'e.is_manual');
			$monthData = $query->get();

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
		}

		//收入
		$item_rcv = new Builder;
		$item_rcv->itemName('收入')->direction('－')->month_value(0)->year_value(0)->nodes([])->indent(0);

		$item_rcv_out = new Builder;
		$item_rcv_out->itemName('外部收入')->direction('－')->month_value(0)->year_value(0)->nodes([])->indent(1);

		$item_rcv_in = new Builder;
		$item_rcv_in->itemName('内部收入')->direction('－')->month_value(0)->year_value(0)->nodes([])->indent(1);

		//成本
		$item_cost = new Builder;
		$item_cost->itemName('成本')->direction('－')->month_value(0)->year_value(0)->nodes([])->indent(0);

		$item_cost_in = new Builder;
		$item_cost_in->itemName('内采成本')->direction('－')->month_value(0)->year_value(0)->nodes([])->indent(1);

		$item_cost_out = new Builder;
		$item_cost_out->itemName('外部成本')->direction('－')->month_value(0)->year_value(0)->nodes([])->indent(1);

		//税金及附加
		$item_tax = new Builder;
		$item_tax->itemName('税金及附加')->direction('－')->month_value(0)->year_value(0)->nodes([])->indent(0);

		$item_free = new Builder;
		$item_free->itemName('费用')->direction('－')->month_value(0)->year_value(0)->nodes([])->indent(0);

		foreach ($monthData as $key => $v) {
			$item = new Builder;
			$item->itemName($v->elementName)->month_value($v->money)->year_value($v->year_money)->indent(2);
			if ($v->elementDirection == 'decrease') {
				$tv = 0 - $v->money;
				$year_tv = 0 - $v->year_money;
				$item->direction('减');
			} else {
				$tv = $v->money;
				$year_tv = $v->year_money;
				$item->direction('增');
			}

			if ($v->elementType == 'rcv') {
				if ($v->dataType == 'es') {
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
				if ($v->dataType == 'ep') {
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
		QueryHelper::sumTreeNodes($item_rcv, ['month_value', 'year_value']);
		QueryHelper::sumTreeNodes($item_cost, ['month_value', 'year_value']);
		QueryHelper::sumTreeNodes($item_tax, ['month_value', 'year_value']);
		QueryHelper::sumTreeNodes($item_free, ['month_value', 'year_value']);

		//主营利润，收入合计－成本合计－税金
		$item_profit = new Builder;
		$item_profit->itemName('主营利润')->direction('－')->month_value($item_rcv->month_value - $item_cost->month_value - $item_tax->month_value)->indent(0);
		$item_profit->year_value($item_rcv->year_value - $item_cost->year_value - $item_tax->year_value);
		//净利润，主营利润-费用
		$item_net_profit = new Builder;
		$item_net_profit->itemName('净利润')->direction('－')->month_value($item_profit->month_value - $item_free->month_value)->indent(0);
		$item_net_profit->year_value($item_profit->year_value - $item_free->year_value);

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

		foreach ($result as $key => $value) {
			if ($item_rcv->month_value != 0) {
				$value->month_ratio(round($value->month_value * 100 / $item_rcv->month_value, 2) . "%");
			} else {
				$value->month_ratio("0%");
			}
			if ($item_rcv->year_value != 0) {
				$value->year_ratio(round($value->year_value * 100 / $item_rcv->year_value, 2) . "%");
			} else {
				$value->year_ratio("0%");
			}
		}
		$totalManel = 0;
		if ($monthData) {
			foreach ($monthData as $key => $v) {
				if ($v->element_is_manual) {
					$totalManel += $v->money;
				}
			}
		}

		//收入/劳动人数
		$item = new Builder;
		$item->itemName('劳动人数')->direction('－')->month_value(0)->indent(0);
		if ($group && $group->employees) {
			$item->month_value($group->employees)->month_ratio(round($item_rcv->month_value / $group->employees, 2));
		}
		array_push($result, $item);

		//劳动时间=收入/劳动时间
		$item = new Builder;
		$item->itemName('劳动时间')->direction('－')->month_value(0)->indent(0);
		if ($timeData && $timeData->time_month != 0) {
			$item->month_value($timeData->time_month)->month_ratio(round($item_rcv->month_value / $timeData->time_month, 2));
		}
		array_push($result, $item);

		//人工成本=收入/人工成本
		$item = new Builder;
		$item->itemName('人工成本')->direction('－')->month_value(0)->indent(0);
		if ($totalManel != 0) {
			$item->month_value($totalManel)->month_ratio(round($item_rcv->month_value / $totalManel, 2));
		}
		array_push($result, $item);

		//人均毛利=毛利/劳动人数
		$item = new Builder;
		$item->itemName('人均毛利')->direction('－')->month_value(0)->indent(0);
		if ($group && $group->employees && $item_profit) {
			$item->month_value(round($item_profit->month_value / $group->employees, 2));
		}
		array_push($result, $item);

		//人均净利=净利/劳动人数
		$item = new Builder;
		$item->itemName('人均净利')->direction('－')->month_value(0)->indent(0);
		if ($group && $group->employees && $item_net_profit) {
			$item->month_value(round($item_net_profit->month_value / $group->employees, 2));
		}
		array_push($result, $item);

		//工作效率=毛利/劳动时间
		$item = new Builder;
		$item->itemName('工作效率')->direction('－')->month_value(0)->indent(0);
		if ($timeData && $timeData->time_month != 0 && $item_profit) {
			$item->month_value(round($item_profit->month_value / $timeData->time_month, 2));
		}
		array_push($result, $item);

		//经营效率=净利/劳动时间
		$item = new Builder;
		$item->itemName('经营效率')->direction('－')->month_value(0)->indent(0);
		if ($timeData && $timeData->time_month != 0 && $item_net_profit) {
			$item->month_value(round($item_net_profit->month_value / $timeData->time_month, 2));
		}
		array_push($result, $item);

		//工作效益=毛利/人工成本
		$item = new Builder;
		$item->itemName('工作效益')->direction('－')->month_value(0)->indent(0);
		if ($totalManel) {
			$item->month_value(round($item_profit->month_value / $totalManel, 2));
		}
		array_push($result, $item);

		//经营效益=净利/人工成本
		$item = new Builder;
		$item->itemName('经营效益')->direction('－')->month_value(0)->indent(0);
		if ($totalManel) {
			$item->month_value(round($item_net_profit->month_value / $totalManel, 2));
		}
		array_push($result, $item);

		foreach ($result as $key => $value) {
			$value->nodes([]);
		}

		return $this->toJson($result);
	}
}
