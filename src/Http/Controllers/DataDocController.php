<?php

namespace Suite\Amiba\Http\Controllers;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Jobs;
use Suite\Amiba\Models;
use Validator;

class DataDocController extends Controller {
  public function index(Request $request) {
    $pageSize = $request->input('size', 10);
    $query = Models\DataDoc::with('purpose', 'fm_group', 'to_group', 'period', 'element');
    $data = $query->paginate($pageSize);
    return $this->toJson($data);
  }
  public function showLines(Request $request, string $id) {
    $pageSize = $request->input('size', 10);
    $withs = ['trader', 'item_category', 'item', 'unit'];
    $query = Models\DataDocLine::with($withs);
    $query->where('doc_id', $id);

    $sortField = $request->input('sortField');
    $sortOrder = $request->input('sortOrder', 'asc');
    if ($sortField) {
      $query->orderBy(in_array($sortField, $withs) ? $sortField . '_id' : $sortField, $sortOrder);
    }
    $data = $query->paginate($pageSize);
    return $this->toJson($data);
  }
  public function show(Request $request, string $id) {
    $query = Models\DataDoc::with('purpose', 'fm_group', 'to_group',
      'period', 'element', 'currency');
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
    $input = InputHelper::fillEntity($input, $request, ['purpose', 'fm_group', 'to_group', 'period', 'element', 'currency']);

    $validator = Validator::make($input, [
      'doc_no' => 'required',
      'doc_date' => 'required',
      'purpose_id' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    $input['ent_id'] = GAuth::entId();
    if (($input['src_type_enum'] == "manual" || empty($input['src_type_enum'])) && empty($input['use_type_enum'])) {
      $input['use_type_enum'] = "direct";
    }
    $data = Models\DataDoc::create($input);

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
    $input = $request->only(['doc_no', 'doc_date', 'state_enum', 'money', 'use_type_enum', 'created_by', 'src_type_enum', 'src_id', 'src_no', 'memo']);
    $input = InputHelper::fillEntity($input, $request, ['purpose', 'fm_group', 'to_group', 'period', 'element', 'currency']);

    $validator = Validator::make($input, [
      'doc_no' => 'required',
      'doc_date' => 'required',
      'purpose_id' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    if (empty($input['money'])) {
      $input['money'] = 0;
		}
		if (($input['src_type_enum'] == "manual" || empty($input['src_type_enum'])) && empty($input['use_type_enum'])) {
      $input['use_type_enum'] = "direct";
    }

    Models\DataDoc::where('id', $id)->update($input);
    $this->storeLines($request, $id);
    return $this->show($request, $id);
  }
  private function storeLines(Request $request, $headId) {
    $lines = $request->input('lines');
    $fillable = ['qty', 'price', 'money', 'expense_code', 'account_code', 'memo'];
    $entityable = ['trader', 'item_category', 'item', 'mfc', 'project', 'unit'];

    if ($lines && count($lines)) {
      foreach ($lines as $key => $value) {
        if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
          $data = array_only($value, $fillable);
          $data = InputHelper::fillEntity($data, $value, $entityable);
          $data['doc_id'] = $headId;
          $data['ent_id'] = GAuth::entId();
          Models\DataDocLine::create($data);
          continue;
        }
        if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
          $data = array_only($value, $fillable);
          $data = InputHelper::fillEntity($data, $value, $entityable);
          if (empty($data['money'])) {
            $data['money'] = 0;
          }
          if (empty($data['price'])) {
            $data['price'] = 0;
          }
          if (empty($data['qty'])) {
            $data['qty'] = 0;
          }
          Models\DataDocLine::where('id', $value['id'])->update($data);
        }
        if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
          Models\DataDocLine::destroy($value['id']);
          continue;
        }
      }
    }
    $job = new Jobs\AmibaDataDocMoneyJob($headId);
    $job->handle();
  }
  /**
   * DELETE
   * @param  Request $request [description]
   * @param  [type]  $id      [description]
   * @return [type]           [description]
   */
  public function destroy(Request $request, $id) {
    $ids = explode(",", $id);
    Models\DataDocLine::whereIn('doc_id', $ids)->delete();
    Models\DataDoc::destroy($ids);
    return $this->toJson(true);
  }
}
