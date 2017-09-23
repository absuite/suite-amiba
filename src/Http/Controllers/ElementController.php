<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
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
		$input['is_leaf'] = 1;
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\Element::create($input);

		if ($data && $data->parent_id) {
			$t = Models\Element::where('parent_id', $data->parent_id)->where('id', '!=', $data->parent_id)->first();
			if ($t) {
				Models\Element::where('id', $data->parent_id)->update(['is_leaf' => 0]);
			} else {
				Models\Element::where('id', $data->parent_id)->update(['is_leaf' => 1]);
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
		$input = $request->only(['code', 'name', 'type_enum', 'direction_enum', 'factor_enum', 'scope_enum', 'is_manual']);

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
		$oldData = Models\Group::find($id);

		Models\Element::where('id', $id)->update($input);
		$data = Models\Element::find($id);
		if ($data && $data->id) {
			$t = Models\Element::where('parent_id', $data->id)->where('id', '!=', $data->id)->first();
			if ($t) {
				Models\Element::where('id', $data->id)->update(['is_leaf' => 0]);
			} else {
				Models\Element::where('id', $data->id)->update(['is_leaf' => 1]);
			}
		}
		if ($data && $data->parent_id) {
			$t = Models\Element::where('parent_id', $data->parent_id)->where('id', '!=', $data->parent_id)->first();
			if ($t) {
				Models\Element::where('id', $data->parent_id)->update(['is_leaf' => 0]);
			} else {
				Models\Element::where('id', $data->parent_id)->update(['is_leaf' => 1]);
			}
		}
		if ($oldData && $oldData->parent_id) {
			$t = Models\Element::where('parent_id', $oldData->parent_id)->where('id', '!=', $oldData->parent_id)->first();
			if ($t) {
				Models\Element::where('id', $oldData->parent_id)->update(['is_leaf' => 0]);
			} else {
				Models\Element::where('id', $oldData->parent_id)->update(['is_leaf' => 1]);
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
		Models\Element::destroy($ids);
		return $this->toJson(true);
	}
}
