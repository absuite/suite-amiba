<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Libs\MonthClose;
use Suite\Amiba\Models;
use Validator;

class DataAdjustController extends Controller {
	public function index(Request $request) {
		$query = Models\DataAdjust::with('purpose', 'period');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DataAdjust::with('purpose', 'period');
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
		//月结校验
		MonthClose::check($request, $input['period_id'], $input['purpose_id']);
		$data = Models\DataAdjust::create($input);

		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['adjust_id'] = $data->id;
				$value = InputHelper::fillEntity($value, $value, ['fm_group', 'to_group', 'fm_element', 'to_element']);

				Models\DataAdjustLine::create($value);
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
		$input = $request->only(['memo']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		//月结校验
		MonthClose::check($request, $input['period_id'], $input['purpose_id']);

		Models\DataAdjust::where('id', $id)->update($input);

		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['adjust_id'] = $id;
				$value = InputHelper::fillEntity($value, $value, ['fm_group', 'to_group', 'fm_element', 'to_element']);

				Models\DataAdjustLine::create($value);
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
		Models\DataAdjust::destroy($ids);
		return $this->toJson(true);
	}
}
