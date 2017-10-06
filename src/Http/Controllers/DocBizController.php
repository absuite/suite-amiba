<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Suite\Amiba\Models as AmibaModels;
use Validator;

class DocBizController extends Controller {
	public function index(Request $request) {
		return '';
	}
	public function batchStore(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'datas' => 'required|array|min:1',
			'datas.*.doc_no' => 'required',
			'datas.*.biz_type' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$batch = 1;
		$batch = intval($request->input('batch', $batch));
		$entId = $request->oauth_ent_id;

		$data_src_identity = '';
		if (!empty($input['data_src_identity'])) {
			$data_src_identity = $input['data_src_identity'];
		}
		if ($data_src_identity) {
			$query = AmibaModels\DocBiz::where('data_src_identity', $data_src_identity);
			$query->where('ent_id', $entId);
			if (!empty($input['fm_date'])) {
				$query->where('doc_date', '>=', $input['fm_date']);
			}
			if (!empty($input['to_date'])) {
				$query->where('doc_date', '<=', $input['to_date']);
			}
			if ($batch <= 1) {
				$query->delete();
			}

		}
		$datas = $request->input('datas');
		foreach ($datas as $k => $v) {
			$data = array_only($v, [
				'src_doc_id', 'src_doc_type', 'src_key_id', 'src_key_type',
				'doc_no', 'doc_date', 'memo', 'org', 'person',
				'biz_type', 'doc_type', 'direction',
				'fm_org', 'fm_dept', 'fm_work', 'fm_team', 'fm_wh', 'fm_person',
				'to_org', 'to_dept', 'to_work', 'to_team', 'to_wh', 'to_person',
				'trader', 'item', 'item_category', 'project', 'lot', 'mfc',
				'currency', 'uom', 'qty', 'price', 'money', 'tax',
				'factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'data_src_identity']);
			$data['ent_id'] = $entId;
			$data['data_src_identity'] = $data_src_identity;
			AmibaModels\DocBiz::create($data);
		}
		return $this->toJson(true);
	}
}
