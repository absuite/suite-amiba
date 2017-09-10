<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class PriceController extends Controller {
	public function index(Request $request) {
		$query = Models\Price::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Price::with('purpose', 'group', 'period', 'lines', 'lines.group', 'lines.item');
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
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\Price::create($input);
		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['price_id'] = $data->id;
				if (empty($value['cost_price'])) {
					$value['cost_price'] = 0;
				}
				$value['ent_id'] = $request->oauth_ent_id;
				$value = InputHelper::fillEntity($value, $value, ['group', 'item']);
				Models\PriceLine::create($value);
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
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\Price::where('id', $id)->update($input);
		$lines = $request->input('lines');
		Models\PriceLine::where('price_id', $id)->delete();
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['price_id'] = $id;
				if (empty($value['cost_price'])) {
					$value['cost_price'] = 0;
				}
				$value['ent_id'] = $request->oauth_ent_id;
				$value = InputHelper::fillEntity($value, $value, ['group', 'item']);
				Models\PriceLine::create($value);
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
		Models\PriceLine::whereIn('price_id', $ids)->delete();

		Models\Price::destroy($ids);
		return $this->toJson(true);
	}
}
