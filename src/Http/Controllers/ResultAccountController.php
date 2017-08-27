<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ResultAccountController extends Controller {
	public function index(Request $request) {
		$query = Models\ResultAccount::with('purpose', 'group', 'period', 'element');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\ResultAccount::with('purpose', 'group', 'period', 'element');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group', 'period', 'element']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'group_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\ResultAccount::create($input);
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->intersect(['type_enum', 'is_init', 'is_outside', 'money', 'src_id', 'src_no']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group', 'period', 'element']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'group_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		if (!Models\ResultAccount::where('id', $id)->update($input)) {
			return $this->toError('没有更新任何数据 !');
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
		Models\ResultAccount::destroy($ids);
		return $this->toJson(true);
	}
}
