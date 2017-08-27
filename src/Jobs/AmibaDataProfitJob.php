<?php

namespace Suite\Amiba\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AmibaDataProfitJob implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $params;
	public $timeout = 12000;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Array $params) {
		$this->params = $params;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		if (empty($this->params)) {
			return;
		}
		$e = false;
		try {
			DB::statement("CALL sp_amiba_data_profit(?,?,?);", [$this->params['ent_id'], $this->params['purpose_id'], $this->params['period_id']]);
		} catch (\Exception $ex) {
			$e = $ex;
		} finally {
		}
	}
}
