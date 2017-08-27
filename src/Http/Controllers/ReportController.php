<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models as AmibaModels;
use Suite\Cbo\Models as CboModels;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller {
	public function getPeriodInfo(Request $request) {
		$result = [];

		$config = new Builder();

		$calendar_id = false;
		if (!empty($request->calendar_id)) {
			$calendar_id = $request->calendar_id;
		}
		if (!$calendar_id) {
			$tmp = AmibaModels\Purpose::where('id', '!=', '')->first();
			if ($tmp) {
				$calendar_id = $tmp->calendar_id;
			}
		}

		//当前期间
		$date = date('Y-m-d');
		$item = false;
		$tmp = CboModels\PeriodAccount::where('calendar_id', $calendar_id)
			->where('from_date', '<=', $date)
			->where('to_date', '>=', $date)
			->first();
		if ($tmp) {
			$item = new Builder();
			$item->id($tmp->id)->code($tmp->code)->name($tmp->name)->from_date($tmp->from_date)->to_date($tmp->to_date);
		}
		$config->period($item);

		//前六期间
		$date = date('Y-m-d');
		$item = false;
		$tmp = CboModels\PeriodAccount::where('calendar_id', $calendar_id)
			->where('from_date', '<=', $date)
			->orderBy('from_date', 'desc')->offset(6)
			->first();
		if ($tmp) {
			$item = new Builder();
			$item->id($tmp->id)->code($tmp->code)->name($tmp->name)->from_date($tmp->from_date)->to_date($tmp->to_date);
		}
		$config->prevSixPeriod($item);
		//后前六期间
		$date = date('Y-m-d');
		$item = false;
		$tmp = CboModels\PeriodAccount::where('calendar_id', $calendar_id)
			->where('from_date', '>=', $date)
			->orderBy('from_date')->skip(5)
			->first();
		if ($tmp) {
			$item = new Builder();
			$item->id($tmp->id)->code($tmp->code)->name($tmp->name)->from_date($tmp->from_date)->to_date($tmp->to_date);
		}
		$config->nextSixPeriod($item);
		//年度第一期
		$date = date('Y-1-1');
		$item = false;
		$tmp = CboModels\PeriodAccount::where('calendar_id', $calendar_id)
			->where('from_date', '<=', $date)
			->where('to_date', '>=', $date)
			->first();
		if ($tmp) {
			$item = new Builder();
			$item->id($tmp->id)->code($tmp->code)->name($tmp->name)->from_date($tmp->from_date)->to_date($tmp->to_date);
		}
		$config->yearFirstPeriod($item);

		//年度最后一期
		$date = date('Y-12-30');
		$item = false;
		$tmp = CboModels\PeriodAccount::where('calendar_id', $calendar_id)
			->where('from_date', '<=', $date)
			->where('to_date', '>=', $date)
			->first();
		if ($tmp) {
			$item = new Builder();
			$item->id($tmp->id)->code($tmp->code)->name($tmp->name)->from_date($tmp->from_date)->to_date($tmp->to_date);
		}
		$config->yearLastPeriod($item);

		return $this->toJson($config);
	}
}
