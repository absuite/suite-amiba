<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class DataInitController extends Controller {
	public function index(Request $request) {
		$query = Models\DataInit::with('purpose', 'period', 'currency', 'lines.group');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DataInit::with('purpose', 'period', 'currency', 'lines.group');
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
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'period', 'currency']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\DataInit::create($input);

		$lines = $request->input('lines');
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['init_id'] = $data->id;
				$value['ent_id'] = $request->oauth_ent_id;
				$value = InputHelper::fillEntity($value, $value, ['group']);
				Models\DataInitLine::create($value);
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

		$tester = new Models\DataInit;
		$input = $request->only($tester->getFillable());

		$input = InputHelper::fillEntity($input, $request, ['purpose', 'period', 'currency']);
		$validator = Validator::make($input, [
			'purpose_id' => 'required',
			'period_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\DataInit::where('id', $id)->update($input);

		$lines = $request->input('lines');

		Models\DataInitLine::where('init_id', $id)->delete();
		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				$value['init_id'] = $id;
				$value['ent_id'] = $request->oauth_ent_id;
				$value = InputHelper::fillEntity($value, $value, ['group']);
				Models\DataInitLine::create($value);
			}
		}

		return $this->show($request, $id);
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\DataInit::destroy($ids);
		return $this->toJson(true);
	}
}
