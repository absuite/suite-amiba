<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Validator;

class AllotMethodController extends Controller {
	public function index(Request $request) {
		$query = Models\AllotMethod::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\AllotMethod::with('purpose', 'lines', 'lines.group');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\AllotMethod::create($input);
		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['method_id'] = $data->id;
				$value = InputHelper::fillEntity($value, $value, ['group']);
				Models\AllotMethodLine::create($value);
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
		$input = $request->intersect(['code', 'name']);
		$input = InputHelper::fillEntity($input, $request, ['purpose']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		if (!Models\AllotMethod::where('id', $id)->update($input)) {
			return $this->toError('没有更新任何数据 !');
		}
		$lines = $request->input('lines');
		Models\AllotMethodLine::where('method_id', $id)->delete();
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['method_id'] = $id;
				$value = InputHelper::fillEntity($value, $value, ['group']);
				Models\AllotMethodLine::create($value);
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
		Models\AllotMethodLine::whereIn('mark_id', $ids)->delete();

		Models\AllotMethod::destroy($ids);
		return $this->toJson(true);
	}
}
