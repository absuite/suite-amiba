<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class AllotRuleController extends Controller {
  public function index(Request $request) {
    $query = Models\AllotRule::select('id', 'code', 'name', 'memo');
    $data = $query->get();

    return $this->toJson($data);
  }
  public function relations(Request $request) {

    $query = DB::table('suite_amiba_allot_rules as r')
      ->join('suite_amiba_allot_rule_lines as rl', 'r.id', '=', 'rl.rule_id')

      ->join('suite_amiba_groups as fg', 'r.group_id', '=', 'fg.id')
      ->join('suite_amiba_groups as tg', 'rl.group_id', '=', 'tg.id')

      ->join('suite_amiba_allot_levels as fl', 'r.bizkey', '=', 'fl.bizkey')
      ->join('suite_amiba_allot_levels as tl', 'rl.bizkey', '=', 'tl.bizkey');
    $query->addSelect(DB::raw("sum(rl.rate) as rate"));

    $query->addSelect(DB::raw("max(fg.name) as fm_name"));
    $query->addSelect(DB::raw('max(tg.name) as to_name'));

    $query->addSelect('r.bizkey as fm_key');
    $query->addSelect('rl.bizkey as to_key');

    $query->whereRaw('fl.purpose_id=r.purpose_id');
    $query->whereRaw('tl.purpose_id=r.purpose_id');
    $query->where('fl.period_id', '');
    $query->where('tl.period_id', '');

    $query->orderBy('fl.level');
    $query->orderBy('tl.level');

    $query->groupBy('r.bizkey', 'rl.bizkey', 'tl.level','tl.level');
    $data = $query->get();

    $nodes = collect($data->all());
    $nodes = $nodes->each(function ($v) {
      $v->rate = intval(1);
    })->all();
    $this->sortNodes($nodes);

    return $this->toJson($nodes);
	}
	private function sortNodes(&$nodes){
		$c=count($nodes);
		for($i=0;$i<$c;$i++){
			$n1=$nodes[$i];
			$count=1;
			for($j=$i+1;$j<$c;$j++){
				$n2=$nodes[$j];
				if($n1->to_key==$n2->fm_key){
					array_splice($nodes,$j,1);
					array_splice($nodes,$i+$count,0,[$n2]);
					$count++;
				}
			}
		}
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

    DB::statement("CALL sp_amiba_allot_level(?,?,?);", [$data->ent_id, $data->purpose_id, '']);

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
    $data = Models\AllotRule::where('id', $id)->first();
    if ($data) {
      DB::statement("CALL sp_amiba_allot_level(?,?,?);", [$data->ent_id, $data->purpose_id, '']);
    }
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
