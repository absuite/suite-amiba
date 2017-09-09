<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Validator;

class PriceAdjustController extends Controller {
	public function index(Request $request) {
		$query = Models\PriceAdjust::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\PriceAdjust::with('purpose', 'group', 'period', 'lines', 'lines.group');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group', 'period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'group_id' => 'required',
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\PriceAdjust::create($input);
		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['adjust_id'] = $data->id;
				if (empty($value['cost_price'])) {
					$value['cost_price'] = 0;
				}
				$value = InputHelper::fillEntity($value, $value, ['group', 'item']);
				Models\PriceAdjustLine::create($value);
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
		$input = $request->only(['code', 'name']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group', 'period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'group_id' => 'required',
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\PriceAdjust::where('id', $id)->update($input);
		$lines = $request->input('lines');
		Models\PriceAdjustLine::where('adjust_id', $id)->delete();
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['adjust_id'] = $id;
				if (empty($value['cost_price'])) {
					$value['cost_price'] = 0;
				}
				$value = InputHelper::fillEntity($value, $value, ['group', 'item']);
				Models\PriceAdjustLine::create($value);
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
		Models\PriceAdjustLine::whereIn('adjust_id', $ids)->delete();

		Models\PriceAdjust::destroy($ids);
		return $this->toJson(true);
	}
}
