<?php

namespace Suite\Amiba\Jobs;
use Carbon\Carbon;
use DB;
use Suite\Amiba\Models\DataDistribute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AmibaDataDistributeJob implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $model;
	protected $revoked;
	public $timeout = 12000;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(DataDistribute $model, $revoked = false) {
		$this->model = $model;
		$this->revoked = $revoked;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		if (empty($this->model)) {

		}
		$e = false;
		try {
			$m = DataDistribute::find($this->model->id);
			$m->succeed = false;
			$m->revoked = $this->revoked;
			$m->start_time = new Carbon;
			$m->end_time = null;
			$m->msg = null;
			$m->save();

			DB::statement("CALL sp_amiba_data_distribute(?,?,?);", [$this->model->ent_id, $this->model->purpose_id, $this->model->period_id]);
		} catch (\Exception $ex) {
			$e = $ex;
		} finally {
			$m = DataDistribute::find($this->model->id);
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
