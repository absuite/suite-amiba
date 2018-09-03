<?php

namespace Suite\Amiba\Http\Controllers;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Illuminate\Http\Request;
use Suite\Amiba\Jobs;
use Suite\Amiba\Models;
use Suite\Cbo\Models as CboModels;
use Validator;

class DtiModelingController extends Controller {
  public function index(Request $request) {
    $query = Models\DtiModeling::with('purpose', 'period');

    $data = $query->get();

    return $this->toJson($data);
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
    $input = array_only($request->all(), ['purpose', 'period', 'modeling', 'memo']);
    $input = InputHelper::fillEntity($input, $request, ['purpose', 'period', 'modeling']);
    $validator = Validator::make($input, [
      'purpose_id' => 'required',
      'period_id' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    $input['ent_id'] = GAuth::entId();

    $run_in_job = $request->input('run_in_job');

    if (is_array($input['period_id'])) {
      $periods = CboModels\PeriodAccount::whereIn('id', $input['period_id'])->orderBy('from_date')->get();
      foreach ($periods as $value) {
        $input['period_id'] = $value->id;
        if (!empty($input['modeling_id']) && is_array($input['modeling_id'])) {
          $input['model_ids'] = implode(',', $input['modeling_id']);
        } else if (!empty($input['modeling_id']) && is_string($input['modeling_id'])) {
          $input['model_ids'] = implode(',', explode(',', $input['modeling_id']));
        } else {
          $input['model_ids'] = '';
        }

        $data = Models\DtiModeling::updateOrCreate(array_only($input, ['period_id', 'purpose_id', 'model_ids']), $input);

        $job = new Jobs\AmibaDtiModelingJob($data);
        if ($run_in_job) {
          dispatch($job);
        } else {
          $job->handle();
        }
      }
    } else {
      if (!empty($input['modeling_id']) && is_array($input['modeling_id'])) {
        $input['model_ids'] = implode(',', $input['modeling_id']);
      } else if (!empty($input['modeling_id']) && is_string($input['modeling_id'])) {
        $input['model_ids'] = implode(',', explode(',', $input['modeling_id']));
      } else {
        $input['model_ids'] = '';
      }
      $data = Models\DtiModeling::updateOrCreate(array_only($input, ['period_id', 'purpose_id', 'model_ids']), $input);

      $job = new Jobs\AmibaDtiModelingJob($data);
      if ($run_in_job) {
        dispatch($job);
      } else {
        $job->handle();
      }
    }
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
    $mds = Models\DtiModeling::whereIn('id', $ids)->get();
    $mds && $mds->each(function ($item) {
      dispatch(new Jobs\AmibaDtiModelingJob($item, true));
    });
    return $this->toJson(true);
  }
}
