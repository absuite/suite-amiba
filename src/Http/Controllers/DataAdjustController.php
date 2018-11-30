<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Libs\MonthClose;
use Suite\Amiba\Models;
use Validator;
use GAuth;
class DataAdjustController extends Controller {
	public function index(Request $request) {
		$query = Models\DataAdjust::with('purpose', 'period');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function showLines(Request $request, string $id) {
		$pageSize = $request->input('size', 10);

		$withs = ['fm_group', 'to_group', 'fm_element', 'to_element'];

		$query = Models\DataAdjustLine::with($withs);
		$query->where('adjust_id', $id);

		$sortField = $request->input('sortField');
		$sortOrder = $request->input('sortOrder', 'asc');
		if ($sortField) {
			$query->orderBy(in_array($sortField, $withs) ? $sortField . '_id' : $sortField, $sortOrder);
		}

		$data = $query->paginate($pageSize);
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DataAdjust::with('purpose', 'period');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		//月结校验
		MonthClose::check($request, $input['period_id'], $input['purpose_id']);
		$input['ent_id'] = GAuth::entId();
		$data = Models\DataAdjust::create($input);
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
		$input = $request->only(['memo','doc_no','doc_date']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'period']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		//月结校验
		MonthClose::check($request, $input['period_id'], $input['purpose_id']);

		Models\DataAdjust::where('id', $id)->update($input);

		$this->storeLines($request, $id);
		return $this->show($request, $id);
	}
	private function storeLines(Request $request, $headId) {
		$lines = $request->input('lines');
		$fillable = ['money'];
		$entityable = ['fm_group', 'to_group', 'fm_element', 'to_element'];

		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					$data['adjust_id'] = $headId;
					$data['ent_id'] = GAuth::entId();
					Models\DataAdjustLine::create($data);
					continue;
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					Models\DataAdjustLine::where('id', $value['id'])->update($data);
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
					Models\DataAdjustLine::destroy($value['id']);
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
		Models\DataAdjustLine::whereIn('adjust_id', $ids)->delete();
		Models\DataAdjust::destroy($ids);
		return $this->toJson(true);
	}
}
