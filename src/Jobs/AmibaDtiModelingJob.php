<?php

namespace Suite\Amiba\Jobs;

use DB;
use GuzzleHttp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
  public function __construct($model, $period) {
    $this->model = $model;
    $this->period = $period;
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
    $result = (string) $res->getBody();
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
      if (!empty(env("GMF_RUNTIME_HOST"))) {
        $this->handleByRuntime();
        return;
      }
      DB::statement("CALL sp_amiba_data_modeling(?,?,?,?);", [$this->model->ent_id, $this->model->purpose_id, $this->period->period_id, $this->model->id]);
    } catch (\Exception $ex) {
      $e = $ex;
      throw $ex;
    } finally {

    }
  }

}
