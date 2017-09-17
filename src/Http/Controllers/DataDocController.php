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
		$input = $request->only(['doc_no', 'doc_date', 'state_enum', 'memo', 'biz_type_enum', 'is_outside', 'qty', 'money']);
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
		Models\DataDocLine::where('doc_id', $headId)->delete();
		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['doc_id'] = $headId;
				$value['ent_id'] = $request->oauth_ent_id;
				$value = InputHelper::fillEntity($value, $value, ['trader', 'item_category', 'item', 'mfc', 'project', 'unit']);

				Models\DataDocLine::create($value);
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
