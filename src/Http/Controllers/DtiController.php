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
		$query = Models\Dti::select('id', 'local_id')->where('ent_id', GAuth::entId());
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
		$dtiAll = $query->get();

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

		Models\Dti::whereIn('id', $dtiAll->pluck('id')->all())->update(['is_running' => 1]);
		$dtiAll->groupBy('local_id')->each(function ($item, $key) use ($context) {
			$job = new Jobs\AmibaDtiRunJob($context, $item->pluck('id')->all());
			dispatch($job);
		});
		return $this->toJson($dtiAll);
	}
	public function log(Request $request) {
		$query = Models\DtiLog::with('dti', 'dti.category');
		$data = $query->get();
		return $this->toJson($data);
	}
}
