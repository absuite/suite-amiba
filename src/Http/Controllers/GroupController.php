<?php

namespace Suite\Amiba\Http\Controllers;
use Suite\Amiba\Models;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Libs\TreeBuilder;
use Illuminate\Http\Request;
use Validator;

class GroupController extends Controller {
	public function index(Request $request) {
		$query = Models\Group::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Group::with('purpose', 'parent', 'lines.data');
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}
	public function all(Request $request) {
		$query = Models\Group::with('purpose', 'parent', 'lines.data');
		if ($request->is_leaf) {
			$query->where('is_leaf', '1');
		}
		$data = $query->get();
		$tree = TreeBuilder::create($data);
		return $this->toJson($tree);
	}

	/**
	 * POST
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function store(Request $request) {
		$input = $request->all();
		$input = InputHelper::fillEntity($input, $request, ['parent', 'purpose']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['is_leaf'] = 1;
		$data = Models\Group::create($input);
		$lines = $request->input('lines');
		$dataType = Models\Group::getDataTypeName($data->type_enum);

		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['group_id'] = $data->id;
				$value['data_type'] = $dataType;
				$value = InputHelper::fillEntity($value, $value, ['data']);

				Models\GroupLine::create($value);
			}
		}
		if ($data && $data->parent_id) {
			$t = Models\Group::where('parent_id', $data->parent_id)->where('id', '!=', $data->parent_id)->first();
			if ($t) {
				Models\Group::where('id', $data->parent_id)->update(['is_leaf' => 0]);
			} else {
				Models\Group::where('id', $data->parent_id)->update(['is_leaf' => 1]);
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
		$input = $request->only(['code', 'name', 'type_enum']);
		$input = InputHelper::fillEntity($input, $request, ['parent', 'purpose']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$oldData = Models\Group::find($id);

		$data = Models\Group::where('id', $id)->update($input);
		$data = Models\Group::find($id);
		$lines = $request->input('lines');
		$dataType = Models\Group::getDataTypeName($data->type_enum);
		Models\GroupLine::where('group_id', $id)->delete();
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['group_id'] = $data->id;
				$value['data_type'] = $dataType;
				$value = InputHelper::fillEntity($value, $value, ['data']);

				Models\GroupLine::create($value);
			}
		}
		if ($data && $data->id) {
			$t = Models\Group::where('parent_id', $data->id)->where('id', '!=', $data->id)->first();
			if ($t) {
				Models\Group::where('id', $data->id)->update(['is_leaf' => 0]);
			} else {
				Models\Group::where('id', $data->id)->update(['is_leaf' => 1]);
			}
		}
		if ($data && $data->parent_id) {
			$t = Models\Group::where('parent_id', $data->parent_id)->where('id', '!=', $data->parent_id)->first();
			if ($t) {
				Models\Group::where('id', $data->parent_id)->update(['is_leaf' => 0]);
			} else {
				Models\Group::where('id', $data->parent_id)->update(['is_leaf' => 1]);
			}
		}
		if ($oldData && $oldData->parent_id) {
			$t = Models\Group::where('parent_id', $oldData->parent_id)->where('id', '!=', $oldData->parent_id)->first();
			if ($t) {
				Models\Group::where('id', $oldData->parent_id)->update(['is_leaf' => 0]);
			} else {
				Models\Group::where('id', $oldData->parent_id)->update(['is_leaf' => 1]);
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
		Models\GroupLine::whereIn('group_id', $ids)->delete();

		Models\Group::destroy($ids);
		return $this->toJson(true);
	}
}
