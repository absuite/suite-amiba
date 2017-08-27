<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ResultProfitController extends Controller {
	public function index(Request $request) {
		$query = Models\ResultProfit::with('purpose', 'group', 'period');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\ResultProfit::with('purpose', 'group', 'period');
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
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
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\ResultProfit::create($input);
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->intersect(['is_init', 'init_profit', 'income', 'cost', 'bal_profit', 'time_profit', 'time_output', 'time_total']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group', 'period', 'element']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'group_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\ResultProfit::where('id', $id)->update($input);
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
		Models\ResultProfit::destroy($ids);
		return $this->toJson(true);
	}
}
