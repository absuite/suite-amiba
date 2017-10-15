<?php

namespace Suite\Amiba\Http\Controllers;
use Auth;
use DB;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Suite\Amiba\Jobs;
use Suite\Cbo\Models\PeriodAccount;

class DtiController extends Controller {
	public function run(Request $request) {
		$query = Models\Dti::select('id')->where('ent_id', $request->oauth_ent_id);
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
		$query = PeriodAccount::where('ent_id', $request->oauth_ent_id)
			->where('from_date', '<=', $request->date)
			->where('to_date', '>=', $request->date);
		$query->select(DB::raw('min(from_date) as from_date, max(to_date) as to_date'));
		$dates = $query->first();

		$context = [];
		$context['ent_id'] = $request->oauth_ent_id;
		$context['user_id'] = Auth::id();
		$context['date'] = $request->date;
		if ($dates) {
			$context['fm_date'] = $dates->from_date;
			$context['to_date'] = $dates->to_date;
		} else {
			$context['fm_date'] = $request->date;
			$context['to_date'] = $request->date;
		}
		$context['local_host'] = $request->getSchemeAndHttpHost() . '/';

		$job = new Jobs\AmibaDtiRunJob($context, $datas);
		$job->handle();
		//dispatch($job);

		return $this->toJson($datas);
	}
	public function log(Request $request) {
		$query = Models\DtiLog::with('dti', 'dti.category');
		$data = $query->get();
		return $this->toJson($data);
	}
}
