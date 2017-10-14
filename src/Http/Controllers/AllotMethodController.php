<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class AllotMethodController extends Controller {
	public function index(Request $request) {
		$query = Models\AllotMethod::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function showLines(Request $request, string $id) {
		$pageSize = $request->input('size', 10);
		$query = Models\AllotMethod::with('group', 'element');
		$query->where('method_id', $id);
		$data = $query->paginate($pageSize);
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\AllotMethod::with('purpose');
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
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\AllotMethod::create($input);

		$this->storeLines($request, $data->id);
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
		$input = InputHelper::fillEntity($input, $request, ['purpose']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\AllotMethod::where('id', $id)->update($input);
		$this->storeLines($request, $id);
		return $this->show($request, $id);
	}
	private function storeLines(Request $request, $headId) {
		$lines = $request->input('lines');
		$fillable = ['rate'];
		$entityable = ['group', 'element'];

		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					$data['method_id'] = $headId;
					$data['ent_id'] = $request->oauth_ent_id;
					Models\AllotMethodLine::create($data);
					continue;
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					Models\AllotMethodLine::where('id', $value['id'])->update($data);
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
					Models\AllotMethodLine::destroy($value['id']);
					continue;
				}
			}
		}
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {

		$ids = explode(",", $id);
		Models\AllotMethodLine::whereIn('method_id', $ids)->delete();

		Models\AllotMethod::destroy($ids);
		return $this->toJson(true);
	}
}
