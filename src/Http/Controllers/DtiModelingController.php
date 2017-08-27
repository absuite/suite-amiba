<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Jobs;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Validator;

class DtiModelingController extends Controller {
	public function index(Request $request) {
		$query = Models\DtiModeling::with('purpose', 'period');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DtiModeling::with('purpose', 'period');
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}

	/**
	 * POST
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function store(Request $request) {
		$input = $request->all();
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\DtiModeling::updateOrCreate(array_only($input, ['period_id', 'purpose_id']), $input);

		$job = new Jobs\AmibaDtiModelingJob($data);
		$job->handle();

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
		$mds = Models\DtiModeling::whereIn('id', $ids)->get();
		$mds && $mds->each(function ($item) {
			dispatch(new Jobs\AmibaDtiModelingJob($item, true));
		});
		return $this->toJson(true);
	}
}
