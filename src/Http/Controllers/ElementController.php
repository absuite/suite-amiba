<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Validator;

class ElementController extends Controller {
	public function index(Request $request) {
		$query = Models\Element::select('id', 'code', 'name', 'memo', 'scope_enum');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Element::with('parent', 'purpose');
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
		$input = InputHelper::fillEntity($input, $request, ['parent', 'purpose']);
		$parent = false;
		if (!empty($input['parent_id'])) {
			$parent = Models\Element::find($input['parent_id']);
		}
		if (!$parent) {
			$validator = Validator::make($input, [
				'code' => 'required',
				'name' => 'required',
			]);
		} else {
			$validator = Validator::make($input, [
				'code' => 'required',
				'name' => 'required',
				'type_enum' => 'required',
				'direction_enum' => 'required',
				'factor_enum' => 'required',
			]);
		}

		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\Element::create($input);
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->intersect(['code', 'name', 'type_enum', 'direction_enum', 'factor_enum', 'scope_enum', 'is_manual']);
		$input = InputHelper::fillEntity($input, $request, ['parent', 'purpose']);
		$parent = false;
		if (!empty($input['parent_id'])) {
			$parent = Models\Element::find($input['parent_id']);
		}
		if (!$parent) {
			$validator = Validator::make($input, [
				'code' => 'required',
				'name' => 'required',
			]);
		} else {
			$validator = Validator::make($input, [
				'code' => 'required',
				'name' => 'required',
				'type_enum' => 'required',
				'direction_enum' => 'required',
				'factor_enum' => 'required',
			]);
		}
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\Element::where('id', $id)->update($input);
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
		Models\Element::destroy($ids);
		return $this->toJson(true);
	}
}
