<?php

namespace Suite\Amiba\Jobs;

use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Suite\Amiba\Models\DtiModeling;

class AmibaDtiModelingJob implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $model;
	public $timeout = 12000;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(DtiModeling $dtiModeling) {
		$this->model = $dtiModeling;
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
			$m = DtiModeling::find($this->model->id);
			$m->succeed = false;
			$m->start_time = new Carbon;
			$m->end_time = null;
			$m->msg = null;
			$m->save();

			DB::statement("CALL sp_amiba_data_modeling(?,?,?,?);", [$this->model->ent_id, $this->model->purpose_id, $this->model->period_id, $this->model->model_ids]);
		} catch (\Exception $ex) {
			$e = $ex;
			throw $ex;
		} finally {
			$m = DtiModeling::find($this->model->id);
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
