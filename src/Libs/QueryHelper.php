<?php

namespace Suite\Amiba\Libs;
use Carbon\Carbon;
use DB;
use Gmf\Sys\Query\QueryCase;

class QueryHelper {
	public static function getPeriod($queryCase) {
		$purposeId = false;
		$periodId = false;
		foreach ($queryCase->wheres as $key => $value) {
			if ($value->name == 'purpose_id') {
				$purposeId = $value->value;
			} else if ($value->name == 'period_id') {
				$periodId = $value->value;
			}
		}
		$query = DB::table('suite_cbo_period_accounts as a');
		$query->addSelect('a.id');
		$query->addSelect('a.name');
		$query->addSelect('a.year');
		$query->addSelect('a.month');
		$query->addSelect('a.to_date');
		$query->addSelect('a.from_date');

		if ($purposeId) {
			$query->join('suite_amiba_purposes  as p', 'p.calendar_id', '=', 'a.calendar_id');
			$query->where('p.id', $purposeId);
		}
		if ($periodId) {
			$query->where('a.id', $periodId);
		} else {
			$t = Carbon::now();
			$date = $t->toDateString();
			$query->where('a.to_date', '>=', $date);
			$query->where('a.from_date', '<=', $date);
		}
		$query->orderBy('a.to_date');
		$monthData = $query->first();
		return $monthData;
	}
	public static function getPrevPeriod($queryCase) {
		$period = static::getPeriod($queryCase);

		$query = DB::table('suite_cbo_period_accounts as a');
		$query->addSelect('a.id');
		$query->addSelect('a.name');
		$query->addSelect('a.year');
		$query->addSelect('a.month');
		$query->addSelect('a.to_date');
		$query->addSelect('a.from_date');
		if ($period) {
			$query->where('a.from_date', '<', $period->from_date);
		}
		$query->orderBy('a.to_date', 'desc');
		$monthData = $query->first();

		return $monthData;
	}
	public static function getPeriods($queryCase) {
		$purposeId = false;
		$fm_period = false;
		$to_period = false;
		foreach ($queryCase->wheres as $key => $value) {
			if ($value->name == 'purpose_id') {
				$purposeId = $value->value;
			} else if ($value->name == 'fm_period') {
				$fm_period = $value->value;
			} else if ($value->name == 'to_period') {
				$to_period = $value->value;
			}
		}

		$t = Carbon::now();
		$date = $t->toDateString();

		$query = DB::table('suite_cbo_period_accounts as a');
		$query->addSelect('a.id');
		$query->addSelect('a.name');
		$query->addSelect('a.year');
		$query->addSelect('a.month');
		$query->addSelect('a.to_date');
		$query->addSelect('a.from_date');

		if ($purposeId) {
			$query->join('suite_amiba_purposes  as p', 'p.calendar_id', '=', 'a.calendar_id');
			$query->where('p.id', $purposeId);
		}
		if ($fm_period) {
			$query->where('a.code', '>=', $fm_period);
		} else {
			$query->where('a.to_date', '>=', $date);
		}
		if ($to_period) {
			$query->where('a.code', '<=', $to_period);
		} else {
			$query->where('a.from_date', '<=', $date);
		}
		$query->orderBy('a.to_date');
		$datas = $query->get();
		if ($datas && count($datas)) {
			return $datas;
		}
		return false;
	}
	public static function getGroups($queryCase, $is_leaf = false) {

		$hasGroupIds = false;
		$query = DB::table('suite_amiba_groups as l');
		$query->addSelect('l.id');
		$query->addSelect('l.name');
		$query->addSelect('l.is_leaf');
		$query->addSelect('l.employees');
		if ($is_leaf) {
			$hasGroupIds = true;
			$query->where('l.is_leaf', '1');
		}
		foreach ($queryCase->wheres as $key => $value) {
			if ($value->name == 'purpose_id') {
				QueryCase::attachWhere($query, $value, 'l.purpose_id');
			} else if ($value->name == 'group_id') {
				$hasGroupIds = true;
				QueryCase::attachWhere($query, $value, 'l.id');
			}
		}
		if (!$hasGroupIds) {
			$query->where('l.is_leaf', '1');
		}
		$query->orderBy('l.name');
		$datas = $query->get();
		if ($datas && count($datas)) {
			return $datas;
		}
		return false;
	}
	public static function getGroup($queryCase, $is_leaf = false) {

		$hasGroupIds = false;
		$query = DB::table('suite_amiba_groups as l');
		$query->addSelect('l.id');
		$query->addSelect('l.name');
		$query->addSelect('l.is_leaf');
		$query->addSelect('l.employees');
		if ($is_leaf) {
			$hasGroupIds = true;
			$query->where('l.is_leaf', '1');
		}
		foreach ($queryCase->wheres as $key => $value) {
			if ($value->name == 'purpose_id') {
				QueryCase::attachWhere($query, $value, 'l.purpose_id');
			} else if ($value->name == 'group_id') {
				$hasGroupIds = true;
				QueryCase::attachWhere($query, $value, 'l.id');
			}
		}
		if (!$hasGroupIds) {
			$query->where('l.is_leaf', '1');
		}
		$query->orderBy('l.name');
		$datas = $query->first();

		return $datas;
	}

	public static function sumTreeNodes($tree, $sumFields = []) {
		$tv = [];
		foreach ($sumFields as $fk => $fv) {
			$tv[$fv] = 0;
		}
		if (!empty($tree->nodes) && count($tree->nodes)) {
			foreach ($tree->nodes as $nk => $nv) {
				static::sumTreeNodes($nv, $sumFields);
				foreach ($sumFields as $fk => $fv) {
					$tv[$fv] = $tv[$fv] + $nv->{$fv};
				}
			}
			foreach ($sumFields as $fk => $fv) {
				$tree->{$fv} = $tv[$fv];
			}
		}
	}
	/**
	 * 把树型结构的数据平
	 * @param  array  $array [description]
	 * @param  [type] $item  [description]
	 * @return [type]        [description]
	 */
	public static function appendNodesToArray(array &$array, $item) {
		if (empty($item->nodes)) {
			return;
		}
		foreach ($item->nodes as $key => $value) {
			$value->indent($item->indent + 1);
			array_push($array, $value);
			if (!empty($value->nodes)) {
				static::appendNodesToArray($array, $value);
			}
		}
	}
}
