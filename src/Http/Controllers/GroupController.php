<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Libs\TreeBuilder;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class GroupController extends Controller {
	public function index(Request $request) {
		$query = Models\Group::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function showLines(Request $request, string $id) {
		$pageSize = $request->input('size', 10);

		$withs = ['data'];

		$query = Models\GroupLine::with($withs);
		$query->where('group_id', $id);

		$sortField = $request->input('sortField');
		$sortOrder = $request->input('sortOrder', 'asc');
		if ($sortField) {
			$query->orderBy(in_array($sortField, $withs) ? $sortField . '_id' : $sortField, $sortOrder);
		}

		$data = $query->paginate($pageSize);
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Group::with('purpose', 'parent');
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}
	public function all(Request $request) {
		$input = $request->all();
		$query = Models\Group::with('purpose', 'parent', 'lines.data');
		if ($request->is_leaf) {
			$query->where('is_leaf', '1');
		}
		if (!empty($input['purpose_id'])) {
			$query->where('purpose_id', $input['purpose_id']);
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
		$tester = new Models\Group;
		$input = $request->only($tester->getFillable());
		$input = InputHelper::fillEntity($input, $request, ['parent', 'purpose']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['is_leaf'] = 1;
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\Group::create($input);

		$this->storeLines($request, $data->id);

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
		$tester = new Models\Group;
		$input = $request->only($tester->getFillable());
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

		$this->storeLines($request, $id);

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
	private function storeLines(Request $request, $headId) {
		$lines = $request->input('lines');
		$fillable = ['memo'];
		$entityable = ['data'];

		$header = Models\Group::find($headId);
		$dataType = Models\Group::getDataTypeName($header->type_enum);

		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					$data['group_id'] = $headId;
					$data['ent_id'] = $request->oauth_ent_id;
					$data['data_type'] = $dataType;
					Models\GroupLine::create($data);
					continue;
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					$data['data_type'] = $dataType;
					Models\GroupLine::where('id', $value['id'])->update($data);
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
					Models\GroupLine::destroy($value['id']);
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
		Models\GroupLine::whereIn('group_id', $ids)->delete();

		Models\Group::destroy($ids);
		return $this->toJson(true);
	}
}
