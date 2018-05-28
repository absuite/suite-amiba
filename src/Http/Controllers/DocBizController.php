<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Suite\Amiba\Models as AmibaModels;
use Uuid;
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
			'datas.*.doc_date' => ['required', 'date'],
			'datas.*.biz_type' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$batch = intval($request->input('batch', 1));
		$entId = GAuth::entId();

		$data_src_identity = '';
		if (!empty($input['data_src_identity'])) {
			$data_src_identity = $input['data_src_identity'];
		}
		if ($data_src_identity && $batch <= 1) {
			$query = AmibaModels\DocBiz::where('data_src_identity', $data_src_identity);
			$query->where('ent_id', $entId);
			if (!empty($input['fm_date'])) {
				$query->where('doc_date', '>=', $input['fm_date']);
			}
			if (!empty($input['to_date'])) {
				$query->where('doc_date', '<=', $input['to_date']);
			}
			$query->delete();
		}
		$datas = $request->input('datas');
		$datas = collect($datas);
		$datas = $datas->map(function ($row) use ($entId, $data_src_identity) {
			$data = array_only($row, [
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
			$data['id'] = Uuid::generate();
			return $data;
		});
		DB::table('suite_amiba_doc_bizs')->insert($datas->all());
		// $datas->each(function ($row) {
		// 	AmibaModels\DocBiz::create($row);
		// });
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

}
