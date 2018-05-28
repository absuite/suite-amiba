<?php

namespace Suite\Amiba\Http\Controllers;
use GAuth;
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
		Validator::make($input, [
			'datas' => 'required|array|min:1',
		])->validate();
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
			$data['ent_id'] = $entId;
			$data['data_src_identity'] = $data_src_identity;
			if (!empty($data['doc_date'])) {
				$data['doc_date'] = substr($data['doc_date'], 0, 10);
			}
			return $data;
		});
		AmibaModels\DocBiz::BatchImport($datas);
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
