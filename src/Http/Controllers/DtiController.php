<?php

namespace Suite\Amiba\Http\Controllers;
use Carbon\Carbon;
use DB;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Suite\Amiba\Jobs;
use Suite\Cbo\Models\PeriodAccount;

class DtiController extends Controller {
	public function run(Request $request) {
		$inputDate = $request->input('date', Carbon::now()->toDateString());
		$query = Models\Dti::select('id')->where('ent_id', GAuth::entId());
		if ($request->has('dtis')) {
			$dtis = explode(",", $request->dtis);
			$query->where(function ($query) use ($dtis) {
				$query->whereIn('id', $dtis)->orWhereIn('code', $dtis);
			});
		}
		if ($request->has('dti')) {
			$query->where(function ($query) use ($request) {
				$query->where('id', $request->dti)->orWhere('code', $request->dti);
			});
		}
		$query->orderBy('sequence');
		$datas = $query->pluck('id')->toArray();
		$query = PeriodAccount::where('ent_id', GAuth::entId())
			->where('from_date', '<=', $inputDate)
			->where('to_date', '>=', $inputDate);
		$query->select(DB::raw('min(from_date) as from_date, max(to_date) as to_date'));
		$dates = $query->first();

		$context = [];
		$context['ent_id'] = GAuth::entId();
		$context['user_id'] = GAuth::id();

		$date = Carbon::parse($inputDate);

		$context['date'] = $date->toDateString();
		if ($dates && $dates->from_date) {
			$context['fm_date'] = Carbon::parse($dates->from_date)->toDateString();
			$context['to_date'] = Carbon::parse($dates->to_date)->toDateString();
		} else {
			$context['fm_date'] = Carbon::create($date->year, $date->month, 1)->toDateString();
			$context['to_date'] = Carbon::create($date->year, $date->month, 1)->addMonth()->addDays(-1)->toDateString();
		}
		$context['local_host'] = $request->getSchemeAndHttpHost() . '/';

		$job = new Jobs\AmibaDtiRunJob($context, $datas);
		// $job->handle();
		dispatch($job);

		return $this->toJson($datas);
	}
	public function log(Request $request) {
		$query = Models\DtiLog::with('dti', 'dti.category');
		$data = $query->get();
		return $this->toJson($data);
	}
}
