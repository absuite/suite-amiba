<?php

namespace Suite\Amiba\Http\Controllers;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Suite\Amiba\Models as AmibaModels;
use Suite\Cbo\Models as CboModels;

class ReportController extends Controller {
	private function buildPerio($period){
		$item = new Builder();
		$item->id($period->id)->code($period->code)->name($period->name)->from_date($period->from_date)->to_date($period->to_date);
		return $item;
	}
	public function getPeriodInfo(Request $request) {
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
		//期间列表
		$periodAll = CboModels\PeriodAccount::where('calendar_id', $calendar_id)->orderBy('from_date')->get();
		$item =[];
		if ($periodAll) {
			foreach($periodAll as $v){
				$item[]=$this->buildPerio($v);
			}
		}
		if ($item) {
			$config->periods($item);
		}	
		//当前期间
		$date = date('Y-m-d');
		$item = false;
		foreach($periodAll as $v){
			if($date>=$v->from_date && $date<=$v->to_date){
				$item=$this->buildPerio($v);
				break;
			}
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
		if ($item) {
			$config->prevSixPeriod($item);
		}

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
		if ($item) {
			$config->nextSixPeriod($item);
		}

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
		if ($item) {
			$config->yearFirstPeriod($item);
		}

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
		if ($item) {
			$config->yearLastPeriod($item);
		}

		return $this->toJson($config);
	}
}
