<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Jobs;
use Suite\Amiba\Libs\MonthClose;
use Suite\Amiba\Models;
use Suite\Cbo\Models as CboModels;
use Validator;
use GAuth;
class DataAccountingController extends Controller {
	public function index(Request $request) {
		$query = Models\DataAccounting::with('purpose', 'period');

		$data = $query->get();

		return $this->toJson($data);
	}

	/**
	 * POST
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function store(Request $request) {
		$input = array_only($request->all(), ['purpose', 'period', 'memo']);

		$input = InputHelper::fillEntity($input, $request, ['purpose', 'period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		//月结校验

		$input['ent_id'] = GAuth::entId();
		if (is_array($input['period_id'])) {
			$periods = CboModels\PeriodAccount::whereIn('id', $input['period_id'])->orderBy('from_date')->get();
			foreach ($periods as $value) {
				$input['period_id'] = $value->id;
				MonthClose::check($request, $input['period_id'], $input['purpose_id']);
				$data = Models\DataAccounting::updateOrCreate(array_only($input, ['period_id', 'purpose_id']), $input);

				$job = new Jobs\AmibaDataAccountingJob($data);
				$job->handle();
			}
		} else {
			MonthClose::check($request, $input['period_id'], $input['purpose_id']);
			$data = Models\DataAccounting::updateOrCreate(array_only($input, ['period_id', 'purpose_id']), $input);

			$job = new Jobs\AmibaDataAccountingJob($data);
			$job->handle();
		}

		//dispatch($job);
		return $this->toJson(true);
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		$mds = Models\DataAccounting::whereIn('id', $ids)->get();
		$mds && $mds->each(function ($item) {
			dispatch(new Jobs\AmibaDataAccountingJob($item, true));
		});
		return $this->toJson(true);
	}
}
