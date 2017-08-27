<?php

namespace Suite\Amiba\Jobs;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AmibaDataDocMoneyJob implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $id;
	public $timeout = 12000;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(string $id) {
		$this->id = $id;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		if (empty($this->id)) {
			return;
		}
		$e = false;
		try {
			DB::statement("CALL sp_amiba_data_doc_money(?);", [$this->id]);
		} catch (\Exception $ex) {
			$e = $ex;
		} finally {
		}
	}
}
