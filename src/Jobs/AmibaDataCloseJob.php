<?php

namespace Suite\Amiba\Jobs;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Suite\Amiba\Models\DataClose;

class AmibaDataCloseJob implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $model;
	protected $revoked;
	public $timeout = 12000;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(DataClose $model, $revoked = false) {
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
			return;
		}
		$e = false;
		try {
			$m = DataClose::find($this->model->id);
			$m->succeed = false;
			$m->revoked = $this->revoked ? '1' : '0';
			$m->start_time = new Carbon;
			$m->end_time = null;
			$m->msg = null;
			$m->save();

		} catch (\Exception $ex) {
			$e = $ex;
		} finally {
			$m = DataClose::find($this->model->id);
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
