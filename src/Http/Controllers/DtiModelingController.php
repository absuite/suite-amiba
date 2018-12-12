<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Suite\Amiba\Jobs;
use Suite\Amiba\Models;
use Suite\Cbo\Models as CboModels;
use Validator;

class DtiModelingController extends Controller {
  public function GetPriceError(Request $request) {
    $pageSize = $request->input('size', 20);

    $query = DB::table('suite_amiba_dti_modeling_prices as l');
    $query->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id');
    $query->join('suite_amiba_modelings as m', 'l.model_id', '=', 'm.id');
    $query->leftJoin('suite_amiba_groups as fm', 'l.fm_group_id', '=', 'fm.id');
    $query->leftJoin('suite_amiba_groups as to', 'l.to_group_id', '=', 'to.id');
    $query->leftJoin('suite_cbo_items as item', 'l.item_id', '=', 'item.id');

    $query->addSelect('l.date as date');
    $query->addSelect('l.price as price');
    $query->addSelect('l.msg as msg');

    $query->addSelect('p.code as period_code', 'p.name as period_name');
    $query->addSelect('m.code as model_code', 'm.name as model_name');
    $query->addSelect('fm.code as fm_group_code', 'fm.name as fm_group_name');
    $query->addSelect('to.code as to_group_code', 'to.name as to_group_name');
    $query->addSelect('item.code as item_code', 'item.name as item_name');

    if (!empty($v = $request->input('purpose_id'))) {
      $query->where('l.purpose_id', $v);
    }
    if (!empty($v = $request->input('period_id'))) {
      $query->where('l.period_id', $v);
    }
    if (!empty($v = $request->input('q'))) {
      $query->where(function ($query) use ($v) {
        $query->where("item.code", "like", "%" . $v . "%")->orWhere("item.name", "like", "%" . $v . "%");
      });
    }

    $query->orderBy('p.from_date', "l.model_id", "l.fm_group_id", "l.to_group_id", "l.date", "l.item_id");

    $data = $query->paginate($pageSize);

    return $this->toJson($data);
  }
  public function index(Request $request) {
    $query = Models\Modeling::with('purpose', 'group');
    $items = $query->get();

    $query = Models\DtiModeling::with('period');
    $query->where('ent_id', GAuth::entId());
    if (!empty($v = $request->input('purpose_id'))) {
      $query->where('purpose_id', $v);
    }
    if (!empty($v = $request->input('period_id'))) {
      $query->where('period_id', $v);
    }
    $query->whereIn('model_id', $items->pluck('id')->all());

    $models = $query->get();
    $items->each(function ($item, $key) use ($models) {
      $item->succeed = 0;
      $item->status = 0;
      $models->each(function ($m, $key) use (&$item) {
        if ($item->id == $m->model_id) {
          $item->start_time = $m->start_time;
          $item->end_time = $m->end_time;
          $item->succeed = $m->succeed;
          $item->msg = $m->msg;
          $item->status = $m->status;
        }
      });
    });
    return $this->toJson($items);
  }
  public function show(Request $request, string $id) {
    $query = Models\DtiModeling::with('purpose', 'period');
    $data = $query->where('id', $id)->first();
    return $this->toJson($data);
  }

  /**
   * POST
   * @param  Request $request [description]
   * @return [type]           [description]
   */
  public function store(Request $request) {
    $input = array_only($request->all(), ['model_id', 'period_id']);
    $validator = Validator::make($input, [
      'model_id' => 'required',
      'period_id' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    $input['ent_id'] = GAuth::entId();
    $model = Models\Modeling::where('id', $input['model_id'])->first();
    if (empty($model)) {
      return $this->toJson(false);
    }
    $period = CboModels\PeriodAccount::where('id', $input['period_id'])->first();
    if (empty($period)) {
      return $this->toJson(false);
    }
    $job = new Jobs\AmibaDtiModelingJob($model, $period);
    $job->handle();

    return $this->toJson(true);
  }
}
