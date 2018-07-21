<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Models;
use Validator;

class ModelingController extends Controller {
  public function index(Request $request) {
    $query = Models\Modeling::with('purpose', 'group');
    $data = $query->get();

    return $this->toJson($data);
  }
  public function showLines(Request $request, string $id) {
    $pageSize = $request->input('size', 10);
    $withs = ['element', 'doc_type', 'item_category', 'item', 'trader', 'match_group', 'to_group'];
    $query = Models\ModelingLine::with($withs);
    $query->where('modeling_id', $id);

    $sortField = $request->input('sortField');
    $sortOrder = $request->input('sortOrder', 'asc');
    if ($sortField) {
      $query->orderBy(in_array($sortField, $withs) ? $sortField . '_id' : $sortField, $sortOrder);
    }
    $data = $query->paginate($pageSize);
    return $this->toJson($data);
  }
  public function show(Request $request, string $id) {
    $query = Models\Modeling::with('purpose', 'group');
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
    $input = InputHelper::fillEntity($input, $request, ['purpose', 'group']);
    $validator = Validator::make($input, [
      'purpose_id' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    $input['ent_id'] = GAuth::entId();
    $data = Models\Modeling::create($input);
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
    $input = InputHelper::fillEntity($input, $request, ['purpose', 'group']);
    $validator = Validator::make($input, [
      'purpose_id' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    Models\Modeling::where('id', $id)->update($input);
    $this->storeLines($request, $id);
    return $this->show($request, $id);
  }
  private function storeLines(Request $request, $headId) {
    $lines = $request->input('lines');
    $fillable = ['biz_type_enum', 'value_type_enum', 'project_code', 'account_code',
      'factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'adjust',
      'match_direction_enum'];
    $entityable = ['element', 'doc_type', 'item_category', 'item', 'trader', 'match_group', 'to_group'];

    if ($lines && count($lines)) {
      foreach ($lines as $key => $value) {
        if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
          $data = array_only($value, $fillable);
          $data = InputHelper::fillEntity($data, $value, $entityable);
          $data['modeling_id'] = $headId;
          $data['ent_id'] = GAuth::entId();
          Models\ModelingLine::create($data);
          continue;
        }
        if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && !empty($value['id'])) {
          $data = array_only($value, $fillable);
          $data = InputHelper::fillEntity($data, $value, $entityable);
          Models\ModelingLine::where('id', $value['id'])->update($data);
        }
        if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
          Models\ModelingLine::destroy($value['id']);
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
    Models\ModelingLine::whereIn('modeling_id', $ids)->delete();

    Models\Modeling::destroy($ids);
    return $this->toJson(true);
  }

  public function get_ref_project(Request $request) {
    
    $biz = DB::table('suite_amiba_doc_bizs')->select('project as item')->whereNotNull('project')
      ->where('ent_id', GAuth::entId());

    $fi = DB::table('suite_amiba_doc_fis')->select('project as item')->whereNotNull('project')
      ->where('ent_id', GAuth::entId());

      if($v=$request->input('q')){
        $biz->where('project','like','%'.$v.'%');
        $fi->where('project','like','%'.$v.'%');
      }

    $data = $fi->union($biz)->distinct()->take(40)->get();
    return $this->toJson($data);
  }
  public function get_ref_account(Request $request) {   

    $fi = DB::table('suite_amiba_doc_fis')->select('account as item')->whereNotNull('account')
      ->where('ent_id', GAuth::entId());

      if($v=$request->input('q')){
        $fi->where('account','like','%'.$v.'%');
      }

    $data = $fi->distinct()->take(40)->get();
    return $this->toJson($data);
  }
  public function get_ref_factor1(Request $request) {    
    $biz = DB::table('suite_amiba_doc_bizs')->select('factor1 as item')->whereNotNull('factor1')
      ->where('ent_id', GAuth::entId());
    $fi = DB::table('suite_amiba_doc_fis')->select('factor1 as item')->whereNotNull('factor1')
      ->where('ent_id', GAuth::entId());

      if($v=$request->input('q')){
        $biz->where('factor1','like','%'.$v.'%');
        $fi->where('factor1','like','%'.$v.'%');
      }

    $data = $fi->union($biz)->distinct()->take(40)->get();

    return $this->toJson($data);
  }

  public function get_ref_factor2(Request $request) {    
    $biz = DB::table('suite_amiba_doc_bizs')->select('factor2 as item')->whereNotNull('factor2')
      ->where('ent_id', GAuth::entId());
    $fi = DB::table('suite_amiba_doc_fis')->select('factor2 as item')->whereNotNull('factor2')
      ->where('ent_id', GAuth::entId());

      if($v=$request->input('q')){
        $biz->where('factor2','like','%'.$v.'%');
        $fi->where('factor2','like','%'.$v.'%');
      }

    $data = $fi->union($biz)->distinct()->take(40)->get();

    return $this->toJson($data);
  }
  public function get_ref_factor3(Request $request) {    
    $biz = DB::table('suite_amiba_doc_bizs')->select('factor3 as item')->whereNotNull('factor3')
      ->where('ent_id', GAuth::entId());
    $fi = DB::table('suite_amiba_doc_fis')->select('factor3 as item')->whereNotNull('factor3')
      ->where('ent_id', GAuth::entId());

      if($v=$request->input('q')){
        $biz->where('factor3','like','%'.$v.'%');
        $fi->where('factor3','like','%'.$v.'%');
      }

    $data = $fi->union($biz)->distinct()->take(40)->get();

    return $this->toJson($data);
  }
  public function get_ref_factor4(Request $request) {    
    $biz = DB::table('suite_amiba_doc_bizs')->select('factor4 as item')->whereNotNull('factor4')
      ->where('ent_id', GAuth::entId());
    $fi = DB::table('suite_amiba_doc_fis')->select('factor4 as item')->whereNotNull('factor4')
      ->where('ent_id', GAuth::entId());

      if($v=$request->input('q')){
        $biz->where('factor4','like','%'.$v.'%');
        $fi->where('factor4','like','%'.$v.'%');
      }

    $data = $fi->union($biz)->distinct()->take(40)->get();

    return $this->toJson($data);
  }
  public function get_ref_factor5(Request $request) {    
    $biz = DB::table('suite_amiba_doc_bizs')->select('factor5 as item')->whereNotNull('factor5')
      ->where('ent_id', GAuth::entId());
    $fi = DB::table('suite_amiba_doc_fis')->select('factor5 as item')->whereNotNull('factor5')
      ->where('ent_id', GAuth::entId());

      if($v=$request->input('q')){
        $biz->where('factor5','like','%'.$v.'%');
        $fi->where('factor5','like','%'.$v.'%');
      }

    $data = $fi->union($biz)->distinct()->take(40)->get();

    return $this->toJson($data);
  }
}
