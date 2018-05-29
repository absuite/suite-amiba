<?php

namespace Suite\Amiba\Http\Controllers;
use GAuth;
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
		Validator::make($input, [
			'datas' => 'required|array|min:1',
		])->validate();
		$batch = intval($request->input('batch', 1));
		$entId = GAuth::entId();
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

		$datas = collect($datas);
		$datas = $datas->map(function ($row) use ($entId, $data_src_identity) {
			$row['ent_id'] = $entId;
			$row['data_src_identity'] = $data_src_identity;
			if (!empty($row['doc_date'])) {
				$row['doc_date'] = substr($row['doc_date'], 0, 10);
			}
			return $row;
		});
		AmibaModels\DocFi::BatchImport($datas);
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
