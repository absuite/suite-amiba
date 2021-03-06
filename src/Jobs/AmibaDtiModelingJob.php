<?php

namespace Suite\Amiba\Jobs;

use Carbon\Carbon;
use DB;
use GuzzleHttp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Suite\Amiba\Models\DtiModeling;

class AmibaDtiModelingJob implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  protected $model;
  protected $period;
  public $timeout = 12000;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($model=null, $period=null) {
    $this->model = $model;
    $this->period = $period;
  }
  public function Cache($ent_id, $data) {
    $base_uri = env("GMF_RUNTIME_HOST");
    $client = new GuzzleHttp\Client([
      'base_uri' => $base_uri,
      'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Ent' => $ent_id],
      'verify' => false,
    ]);
    $res = $client->request('POST', "api/amiba/models/cache", [
      'json' => $data,
    ]);
  }
  private function handleByRuntime() {
    $base_uri = env("GMF_RUNTIME_HOST");
    $client = new GuzzleHttp\Client([
      'base_uri' => $base_uri,
      'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Ent' => $this->model->ent_id],
      'verify' => false,
    ]);
    $input = [
      "purpose_id" => $this->model->purpose_id,
      "period_ids" => [$this->period->id],
      "model_ids" => [$this->model->id],
    ];
    //参数{purpose_id:string,period_ids:[string],model_ids:[string]}
    $res = $client->request('POST', "api/amiba/models/modeling", [
      'json' => $input,
    ]);
    // $result = (string) $res->getBody();
  }
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {
    if (empty($this->model)) {
      return;
    }
    $e = false;
    try {
      $m = DtiModeling::updateOrCreate([
        "ent_id" => $this->model->ent_id,
        "purpose_id" => $this->model->purpose_id,
        "period_id" => $this->period->id,
        "model_id" => $this->model->id,
      ], ["msg" => "开始执行", "start_time" => new Carbon]);

      if (!empty(env("GMF_RUNTIME_HOST"))) {
        $this->handleByRuntime();
      } else {
        $m->succeed = false;
        $m->start_time = new Carbon;
        $m->end_time = null;
        $m->msg = null;
        $m->save();
        DB::statement("CALL sp_amiba_data_modeling(?,?,?,?);", [$this->model->ent_id, $this->model->purpose_id, $this->period->id, $this->model->id]);
      }
    } catch (\Exception $ex) {
      $e = $ex;
      throw $ex;
    } finally {
      if (!empty(env("GMF_RUNTIME_HOST"))) {
      } else {
        $m = DtiModeling::updateOrCreate([
          "ent_id" => $this->model->ent_id,
          "purpose_id" => $this->model->purpose_id,
          "period_id" => $this->period->id,
          "model_id" => $this->model->id,
        ], ["msg" => "执行成功"]);
        if ($e) {
          $m->succeed = false;
          $m->msg = $e->getMessage();
        } else {
          $m->succeed = true;
        }
        $m->end_time = new Carbon;
        $m->save();
      }
    }
  }

}
