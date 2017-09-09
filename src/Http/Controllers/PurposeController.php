<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Validator;

class PurposeController extends Controller {
	public function index(Request $request) {
		$query = Models\Purpose::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Purpose::with('calendar', 'currency');
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
		$input = InputHelper::fillEntity($input, $request, ['calendar', 'currency']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\Purpose::create($input);
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
		$input = InputHelper::fillEntity($input, $request, ['calendar', 'currency']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\Purpose::where('id', $id)->update($input);
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
		Models\Purpose::destroy($ids);
		return $this->toJson(true);
	}
}
