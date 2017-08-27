<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Validator;

class DataTargetController extends Controller {
	public function index(Request $request) {
		$query = Models\DataTarget::with('purpose', 'group', 'fm_period', 'to_period');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DataTarget::with('purpose', 'group', 'fm_period', 'to_period', 'lines.element');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group', 'fm_period', 'to_period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'group_id' => 'required',
			'fm_period_id' => 'required',
			'to_period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\DataTarget::create($input);
		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['target_id'] = $data->id;
				$value = InputHelper::fillEntity($value, $value, ['element']);
				Models\DataTargetLine::create($value);
			}
		}
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->intersect(['memo']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group', 'fm_period', 'to_period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'group_id' => 'required',
			'fm_period_id' => 'required',
			'to_period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\DataTarget::where('id', $id)->update($input);

		$lines = $request->input('lines');
		Models\DataTargetLine::where('target_id', $id)->delete();
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['target_id'] = $id;
				$value = InputHelper::fillEntity($value, $value, ['element']);
				Models\DataTargetLine::create($value);
			}
		}

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
		Models\DataTarget::destroy($ids);
		return $this->toJson(true);
	}
}
