<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class ModelingController extends Controller {
	public function index(Request $request) {
		$query = Models\Modeling::with('purpose', 'group');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Modeling::with('purpose', 'group',
			'lines.element', 'lines.doc_type', 'lines.item_category', 'lines.item', 'lines.trader', 'lines.match_group', 'lines.to_group');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\Modeling::create($input);
		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['modeling_id'] = $data->id;
				$value['ent_id'] = $request->oauth_ent_id;
				$value = InputHelper::fillEntity($value, $value, ['element', 'doc_type', 'item_category', 'item', 'trader', 'match_group', 'to_group']);
				Models\ModelingLine::create($value);
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
		$input = $request->only(['purpose', 'group']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'group']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\Modeling::where('id', $id)->update($input);
		$lines = $request->input('lines');
		Models\ModelingLine::where('modeling_id', $id)->delete();
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['modeling_id'] = $id;
				$value['ent_id'] = $request->oauth_ent_id;
				$value['match_group_id'] = '';
				$value = InputHelper::fillEntity($value, $value, ['element', 'doc_type', 'item_category', 'item', 'trader', 'match_group', 'to_group']);
				Models\ModelingLine::create($value);
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
		Models\ModelingLine::whereIn('modeling_id', $ids)->delete();

		Models\Modeling::destroy($ids);
		return $this->toJson(true);
	}
}
