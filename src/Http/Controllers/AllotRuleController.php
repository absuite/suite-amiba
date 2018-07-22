<?php

namespace Suite\Amiba\Http\Controllers;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;
use GAuth;
use DB;
class AllotRuleController extends Controller {
	public function index(Request $request) {
		$query = Models\AllotRule::select('id', 'code', 'name', 'memo');
		$data = $query->get();

		return $this->toJson($data);
	}
	public function relations(Request $request) {
		$query = DB::table('suite_amiba_allot_rules as r')
		->join('suite_amiba_allot_rule_lines as rl','r.id','=','rl.rule_id')
		->leftJoin('suite_amiba_groups as fg','r.group_id','=','fg.id')
		->leftJoin('suite_amiba_groups as tg','rl.group_id','=','tg.id')
		->join('suite_amiba_elements as fe','r.element_id','=','fe.id')
		->join('suite_amiba_elements as te','rl.element_id','=','te.id')
		->select('r.group_id as fm_group_id','r.element_id as fm_element_id')
		->addSelect('rl.group_id as to_group_id','rl.element_id as to_element_id');
		$query->addSelect('rl.rate');
		$query->addSelect(DB::raw("CONCAT(IFNULL(r.group_id,''),'|',IFNULL(r.group_id,'')) as parent_id"));
		$query->addSelect(DB::raw("CONCAT(IFNULL(rl.group_id,''),'|',IFNULL(rl.group_id,'')) as id"));

		$query->orderBy('r.group_id');
		$query->orderBy('r.element_id');
		$query->orderBy('rl.group_id');
		$query->orderBy('rl.element_id');
		$data=$query->get();

	$nodes=	$data->all();
	foreach($nodes as $key=>$item){
		unset($nodes[$key+2]);
		
		var_dump($key);
	}
	var_dump($nodes);
		return $this->toJson($data);
	}
	
	public function showLines(Request $request, string $id) {
		$pageSize = $request->input('size', 10);
		$query = Models\AllotRuleLine::with('group', 'element');
		$query->where('rule_id', $id);
		$data = $query->paginate($pageSize);
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\AllotRule::with('purpose', 'method', 'element', 'group');
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}

	/**
	 * POST
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function store(Request $request) {
		$input = $request->all();
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'method', 'element', 'group']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = GAuth::entId();
		$data = Models\AllotRule::create($input);
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
		$input = $request->only(['code', 'name']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'method', 'element', 'group']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\AllotRule::where('id', $id)->update($input);
		$this->storeLines($request, $id);
		return $this->show($request, $id);
	}
	private function storeLines(Request $request, $headId) {
		$lines = $request->input('lines');
		$fillable = ['rate'];
		$entityable = ['group', 'element'];

		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					$data['rule_id'] = $headId;
					$data['ent_id'] = GAuth::entId();
					Models\AllotRuleLine::create($data);
					continue;
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					Models\AllotRuleLine::where('id', $value['id'])->update($data);
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
					Models\AllotRuleLine::destroy($value['id']);
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
		Models\AllotRuleLine::whereIn('rule_id', $ids)->delete();

		Models\AllotRule::destroy($ids);
		return $this->toJson(true);
	}
}
