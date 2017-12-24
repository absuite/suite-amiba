<?php

namespace Suite\Amiba\Http\Controllers;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
		$batch = intval($request->input('batch', 1));
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
			if (!empty($data['doc_date'])) {
				$data['doc_date'] = substr($data['doc_date'], 0, 10);
			}
			AmibaModels\DocBiz::create($data);
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
		AmibaModels\DocBiz::destroy($ids);
		return $this->toJson(true);
	}

	private function importData($data, $throwExp = true) {
		$validator = Validator::make($data, [
			'doc_no' => 'required',
			'doc_date' => ['required', 'date'],
			'biz_type' => [
				'required',
				Rule::in(['ship', 'rcv',
					'miscRcv', 'miscShip',
					'transfer', 'moRcv', 'moIssue',
					'process', 'receivables', 'payment',
					'ar', 'ap', 'plan',
					'expense']),
			],
			'direction' => [
				'required',
				Rule::in(['rcv', 'ship']),
			],
			'qty' => ['numeric'],
			'price' => ['numeric'],
			'money' => ['numeric'],
			'tax' => ['numeric'],
		]);
		if ($throwExp) {
			$validator->validate();
		} else if ($validator->fails()) {
			return false;
		}
		$data['ent_id'] = GAuth::entId();
		return AmibaModels\DocBiz::create($data);
	}
	public function import(Request $request) {
		$datas = app('Suite\Cbo\Bp\FileImport')->create($this, $request);
		$datas->each(function ($row, $key) {
			$row['data_src_identity'] = 'import';
			$this->importData($row);
		});
		return $this->toJson(true);
	}
}
