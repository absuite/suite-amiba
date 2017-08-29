<?php

namespace Suite\Amiba\Http\Controllers;
use Auth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Suite\Amiba\Jobs;

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

		$context = [];
		$context['ent_id'] = $request->oauth_ent_id;
		$context['user_id'] = Auth::id();
		$context['date'] = $request->date;
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
