<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class AllotRuleController extends Controller {
	public function index(Request $request) {
		$query = Models\AllotRule::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\AllotRule::with('purpose', 'method', 'element', 'group', 'lines', 'lines.group', 'lines.element');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'method', 'element', 'group']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\AllotRule::create($input);
		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$ldata = ['rule_id' => $data->id, 'rate' => $value['rate']];

				$ldata = InputHelper::fillEntity($ldata, $value, ['element', 'group']);
				Models\AllotRuleLine::create($ldata);
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'method', 'element', 'group']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		if (!Models\AllotRule::where('id', $id)->update($input)) {
			return $this->toError('没有更新任何数据 !');
		}
		$lines = $request->input('lines');
		Models\AllotRuleLine::where('rule_id', $id)->delete();
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$ldata = ['rule_id' => $id, 'rate' => $value['rate']];
				$ldata = InputHelper::fillEntity($ldata, $value, ['element', 'group']);
				Models\AllotRuleLine::create($ldata);
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
		Models\AllotRuleLine::whereIn('rule_id', $ids)->delete();

		Models\AllotRule::destroy($ids);
		return $this->toJson(true);
	}
}
