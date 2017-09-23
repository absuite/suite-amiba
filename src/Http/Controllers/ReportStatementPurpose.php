<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Suite\Amiba\Libs\QueryHelper;

class ReportStatementPurpose extends Controller {
	public function index(Request $request) {
		Log::error(static::class);
		$result = [];
		$elementsData = [];
		$itemsData = [];
		$qc = QueryCase::formatRequest($request);
		$period = QueryHelper::getPeriod($qc);
		$group = QueryHelper::getGroup($qc);
		if ($period && $group) {

			$query = DB::table('gmf_sys_entity_fields as el');
			$query->join('gmf_sys_entities as e', 'el.entity_id', '=', 'e.id');

			$query->leftJoin('suite_amiba_data_target_lines as l', function ($join) {
				$join->on('l.type_enum', '=', 'el.name');
			});

			$query->leftJoin('suite_amiba_data_targets as t', function ($join) use ($qc, $period, $group) {
				$join->on('l.target_id', '=', 't.id');
				foreach ($qc->wheres as $key => $value) {
					if ($value->name == 'group_id') {
						QueryCase::attachWhere($join, $value, 't.' . $value->name);
					} else if ($value->name == 'purpose_id') {
						QueryCase::attachWhere($join, $value, 't.' . $value->name);
					}
				}
			});
			$query->leftJoin('suite_cbo_period_accounts as fp', function ($join) use ($qc, $period, $group) {
				$join->on('t.fm_period_id', '=', 'fp.id');
				$join->where('fp.from_date', '>=', $period->from_date);
			});
			$query->leftJoin('suite_cbo_period_accounts as tp', function ($join) use ($qc, $period, $group) {
				$join->on('t.to_period_id', '=', 'tp.id');
				$join->where('tp.from_date', '<=', $period->from_date);
			});
			$query->addSelect('el.comment as itemName');
			$query->addSelect('l.type_enum as type_enum');
			$query->addSelect('l.element_id as element_id');

			$query->addSelect(DB::raw("avg(l.rate) AS rate"));
			$query->addSelect(DB::raw("SUM(l.money) as money"));

			$query->where('e.name', 'suite.amiba.data.target.type.enum');

			$query->groupBy('el.comment', 'l.type_enum', 'l.element_id');
			$itemsData = $query->get();

			//要素值
			$query = DB::table('suite_amiba_result_accounts as l');
			$query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
			$query->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id');

			$query->addSelect('e.name as element_name');
			$query->addSelect('e.direction_enum as element_direction_enum');
			$query->addSelect('e.type_enum as element_type_enum');
			$query->addSelect('e.is_manual as element_is_manual');

			$query->addSelect(DB::raw("SUM(CASE WHEN p.id='" . $period->id . "' THEN 1  ELSE 0 END * l.money) AS money"));

			foreach ($qc->wheres as $key => $value) {
				if ($value->name == 'group_id') {
					QueryCase::attachWhere($query, $value, 'l.' . $value->name);
				} else if ($value->name == 'purpose_id') {
					QueryCase::attachWhere($query, $value, 'l.' . $value->name);
				}
			}

			$query->where('p.year', '=', $period->year);
			$query->where('p.from_date', '<=', $period->from_date);

			$query->groupBy('e.name', 'e.direction_enum', 'e.type_enum', 'e.is_manual');
			$elementsData = $query->get();
		}

		return $this->toJson($itemsData);
	}
}
