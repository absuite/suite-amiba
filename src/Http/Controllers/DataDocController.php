<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Jobs;
use Suite\Amiba\Models;
use Validator;

class DataDocController extends Controller {
	public function index(Request $request) {
		$pageSize = $request->input('size', 10);
		$query = Models\DataDoc::with('purpose', 'fm_group', 'to_group', 'period', 'element');
		$data = $query->paginate($pageSize);
		return $this->toJson($data);
	}
	public function showLines(Request $request, string $id) {
		$pageSize = $request->input('size', 10);
		$query = Models\DataDocLine::with('trader', 'item_category', 'item', 'unit');
		$query->where('doc_id', $id);
		$data = $query->paginate($pageSize);
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DataDoc::with('purpose', 'fm_group', 'to_group',
			'period', 'element', 'currency');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'fm_group', 'to_group', 'period', 'element', 'currency']);

		$validator = Validator::make($input, [
			'doc_no' => 'required',
			'doc_date' => 'required',
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\DataDoc::create($input);

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
		$input = $request->only(['doc_no', 'doc_date', 'state_enum', 'money', 'use_type_enum', 'created_by', 'src_type_enum', 'src_id', 'src_no', 'memo']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'fm_group', 'to_group', 'period', 'element', 'currency']);

		$validator = Validator::make($input, [
			'doc_no' => 'required',
			'doc_date' => 'required',
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\DataDoc::where('id', $id)->update($input);
		$this->storeLines($request, $id);
		return $this->show($request, $id);
	}
	private function storeLines(Request $request, $headId) {
		$lines = $request->input('lines');
		$fillable = ['qty', 'price', 'money', 'expense_code', 'subject_code', 'memo'];
		$entityable = ['trader', 'item_category', 'item', 'mfc', 'project', 'unit'];

		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					$data['doc_id'] = $headId;
					$data['ent_id'] = $request->oauth_ent_id;
					Models\DataDocLine::create($data);
					continue;
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					Models\DataDocLine::where('id', $value['id'])->update($data);
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
					Models\DataDocLine::destroy($value['id']);
					continue;
				}
			}
		}
		$job = new Jobs\AmibaDataDocMoneyJob($headId);
		$job->handle();
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\DataDocLine::whereIn('doc_id', $ids)->delete();
		Models\DataDoc::destroy($ids);
		return $this->toJson(true);
	}
}
