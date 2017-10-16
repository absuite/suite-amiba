<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Suite\Amiba\Models as AmibaModels;
use Validator;

class DocFiController extends Controller {
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
		$batch = intval($request->input('batch', 1));
		$entId = $request->oauth_ent_id;
		$data_src_identity = '';
		if (!empty($input['data_src_identity'])) {
			$data_src_identity = $input['data_src_identity'];
		}
		if ($data_src_identity) {
			$query = AmibaModels\DocFi::where('data_src_identity', $data_src_identity);
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
			$data = array_only($v, ['src_doc_id', 'src_doc_type', 'src_key_id', 'src_key_type',
				'doc_no', 'doc_date', 'memo',
				'biz_type', 'doc_type',
				'fm_org', 'fm_dept', 'fm_work', 'fm_team', 'fm_wh', 'fm_person',
				'trader', 'project', 'account', 'debit_money', 'credit_money',
				'factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'data_src_identity']);
			$data['ent_id'] = $entId;
			$data['data_src_identity'] = $data_src_identity;
			AmibaModels\DocFi::create($data);
		}
		return $this->toJson(true);
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		AmibaModels\DocFi::destroy($ids);
		return $this->toJson(true);
	}
}
