<?php

namespace Suite\Amiba\Libs;
use DB;
use Exception;
use Illuminate\Http\Request;

class MonthClose {
	public static function check(Request $request, $date, $purposeId = false) {
		$query = DB::table('suite_amiba_data_closes as l')
			->join('suite_cbo_period_accounts as p', function ($join) {
				$join->on('l.period_id', '=', 'p.id')->on('l.ent_id', '=', 'p.ent_id');
			})
			->addSelect('p.name')
			->where(function ($query) use ($date) {
				$query->where('p.id', $date)
					->orWhere(function ($query) use ($date) {
						$query->where('p.from_date', '>=', $date)->where('p.to_date', '<=', $date);
					});
			});
		if ($purposeId) {
			$query->where('purpose_id', $purposeId);
		}
		$data = $query->first();
		if ($data) {
			throw new Exception("期间已经月结，不能进行此操作!", 1);
		}
	}
}
