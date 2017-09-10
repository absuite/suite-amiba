<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class DataTimeController extends Controller {
	public function index(Request $request) {
		$query = Models\DataTime::with('purpose', 'period');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DataTime::with('purpose', 'period', 'lines.group');
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
		$data = Models\DataTime::create($input);

		$this->storeLines($request, $data->id);
		return $this->show($request, $data->id);
	}
	private function storeLines(Request $request, $headId) {
		Models\DataTimeLine::where('time_id', $headId)->delete();
		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['time_id'] = $headId;
				if (empty($value['nor_time'])) {
					$value['nor_time'] = 0;
				}
				if (empty($value['over_time'])) {
					$value['over_time'] = 0;
				}
				$value['total_time'] = $value['nor_time'] + $value['over_time'];
				$value['ent_id'] = $request->oauth_ent_id;
				$value = InputHelper::fillEntity($value, $value, ['group']);

				Models\DataTimeLine::create($value);
			}
		}
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->only(['memo']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\DataTime::where('id', $id)->update($input);
		$this->storeLines($request, $id);
		return $this->show($request, $id);
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\DataTime::destroy($ids);
		return $this->toJson(true);
	}
}
