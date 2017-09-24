<?php

namespace Suite\Amiba\Jobs;
use Carbon\Carbon;
use Gmf\Ac\Models\User;
use Gmf\Sys\Models;
use Gmf\Sys\Uuid;
use GuzzleHttp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AmibaDtiRunJob implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $dtis = [];
	protected $context;
	protected $sessionId;
	public $timeout = 12000;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Array $context = [], Array $dtis = []) {
		$this->context = $context;
		$this->dtis = $dtis;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		if (empty($this->dtis)) {
			return;
		}
		$e = false;
		try {
			$dt = Carbon::now();
			$this->sessionId = $dt->toDateString() . '' . Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
			//日志
			Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'state_enum' => 'runing', 'memo' => '接口程序处理.开始，上下文：' . json_encode($this->context)]);

			$query = Models\Dti::with('category');
			$query->whereIn('id', $this->dtis);
			$query->orderBy('sequence');
			$datas = $query->get();

			Models\Dti::whereIn('id', $this->dtis)->update(['is_running' => 1, 'begin_date' => new Carbon, 'end_date' => null]);
			Models\Dti::whereIn('id', $this->dtis)->increment('total_runs');

			if (empty($this->context['access_token'])) {
				$this->context['access_token'] = $this->getLocalToken();
			}

			foreach ($datas as $key => $value) {
				$this->runDtiItem($value);
			}

		} catch (\Exception $ex) {
			$e = $ex;
		} finally {
			Models\Dti::whereIn('id', $this->dtis)->update(['is_running' => 0, 'end_date' => new Carbon]);
			//日志
			if ($e) {
				Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'state_enum' => 'failed', 'memo' => '接口程序处理.结束', 'content' => $e->getMessage()]);
			} else {
				Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'state_enum' => 'succeed', 'memo' => '接口程序处理.结束']);
			}
		}
		if ($e) {
			throw $e;
		}
	}
	private function runDtiItem_U9($dti, $paramsConfig = []) {
		$result = false;
		$base_uri = '';
		$apiPath = 'RestServices/UFIDA.U9.ISV.TransData.ICommonQuery.svc/Do';
		if ($dti->category && $dti->category->host) {
			$base_uri = $dti->category->host;
		}
		if (!ends_with($base_uri, "/")) {
			$base_uri = $base_uri . '/';
		}
		$client = new GuzzleHttp\Client([
			'base_uri' => $base_uri,
			'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
			'verify' => false,
		]);

		$input = [
			'serverName' => $dti->code,
			'parameters' => '',
		];
		if (!empty($paramsConfig['ent_code'])) {
			$input['context']['EntCode'] = $paramsConfig['ent_code'];
		}
		if ($dti->body && count($dti->body)) {
			$input['parameters'] = $this->parseParams($dti->body, $paramsConfig);
		}

		Log::error(static::class . "dti post to u9 {$base_uri}{$apiPath}:" . json_encode($input));
		$res = $client->request('POST', $apiPath, [
			'json' => $input,
		]);
		$result = (string) $res->getBody();

		$result = json_decode($result);

		if ($result->d->Error) {
			Log::error($result->d->Error);
			throw new \Exception($result->d->Error . ',请查看U9日志', 1);
		}
		$result = $result->d->Datas;
		if ($result) {
			$result = json_decode($result);
		}
		return $result;
	}
	private function parseParams($paramStr, $paramsConfig = []) {
		if ($paramStr) {
			$paramsObj = json_decode($paramStr);
			if ($paramsObj) {
				foreach ($paramsObj as $pk => $pv) {
					$parseValue = $pv;
					foreach ($paramsConfig as $key => $value) {
						if ($pv == '${' . $key . '}') {
							$parseValue = $value;
							break;
						}
					}
					$paramsObj->{$pk} = $parseValue;
				}
			}
			$paramStr = json_encode($paramsObj);
		}
		return $paramStr;
	}
	private function getDtiParamConfig($dti) {
		$dtiParams = Models\DtiParam::where('dti_id', $dti->id)->get();
		$dtiCategoryParams = Models\DtiParam::where('category_id', $dti->category_id)->get();
		$emptyParams = Models\DtiParam::whereNull('category_id')->whereNull('dti_id')->get();

		$params = [];
		foreach ($emptyParams as $key => $value) {
			if ($value->type_enum == 'input') {
				if (!empty($this->context[$value->value])) {
					$params[$value->code] = $this->context[$value->value];
				}
			} else {
				$params[$value->code] = $value->value;
			}
		}
		foreach ($dtiCategoryParams as $key => $value) {
			if ($value->type_enum == 'input') {
				if (!empty($this->context[$value->value])) {
					$params[$value->code] = $this->context[$value->value];
				}
			} else {
				$params[$value->code] = $value->value;
			}
		}
		foreach ($dtiParams as $key => $value) {
			if ($value->type_enum == 'input') {
				if (!empty($this->context[$value->value])) {
					$params[$value->code] = $this->context[$value->value];
				}
			} else {
				$params[$value->code] = $value->value;
			}
		}
		return $params;
	}
	private function runDtiItem($dti) {
		$e = false;
		$result = false;
		try {
			Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'runing', 'memo' => '接口程序[' . $dti->name . ']远程调用.开始']);
			$paramsConfig = $this->getDtiParamConfig($dti);
			$result = $this->runDtiItem_U9($dti, $paramsConfig);

			$this->callLocalStore($dti, $result, $paramsConfig);

		} catch (\Exception $exception) {
			$e = $exception;
			Log::error($e);
		} finally {
			if ($e) {
				Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'failed', 'memo' => '接口程序[' . $dti->name . ']远程调用.结束', 'content' => $e->getMessage()]);
			} else {
				Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'succeed', 'memo' => '接口程序[' . $dti->name . ']远程调用.结束']);
			}
		}
		if ($e) {
			throw $e;
		}
	}
	private function getLocalToken() {
		$token = User::find($this->context['user_id'])->createToken('api batch local call');
		return $token->accessToken;
	}
	private function callLocalStore($dti, $data = null) {
		$e = false;
		$apiPath = false;
		if ($dti->local && $dti->local->path) {
			$apiPath = $dti->local->path;
		}
		$base_uri = $this->context['local_host'];

		if (empty($apiPath) || empty($data)) {
			return;
		}

		if (!ends_with($base_uri, "/")) {
			$base_uri = $base_uri . '/';
		}
		$client = new GuzzleHttp\Client([
			'base_uri' => $base_uri,
			'headers' => [
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
				'Ent' => $this->context['ent_id'],
				'Authorization' => 'Bearer ' . $this->context['access_token'],
			],
			'verify' => false,
		]);
		$input = [
			'server_name' => $dti->code,
			'data_src_identity' => $dti->code,
			'date' => $this->context['date'],
			'datas' => $data,
		];

		try {
			Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'runing', 'memo' => '接口程序[' . $dti->name . ']本地数据存储.开始']);
			Log::error($base_uri . $apiPath);
			$res = $client->request('POST', $apiPath, [
				'json' => $input,
			]);
			$result = (string) $res->getBody();

			$result = json_decode($result);
			$result = $result->data;

		} catch (\Exception $exception) {
			$e = $exception;
			Log::error($e);
		} finally {
			if ($e) {
				Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'failed', 'memo' => '接口程序[' . $dti->name . ']本地数据存储.结束', 'content' => $e->getMessage()]);
			} else {
				Models\DtiLog::create(['session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'succeed', 'memo' => '接口程序[' . $dti->name . ']本地数据存储.结束']);
			}
		}
		if ($e) {
			throw $e;
		}
	}
}
