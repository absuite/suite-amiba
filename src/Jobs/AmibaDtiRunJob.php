<?php

namespace Suite\Amiba\Jobs;
use Carbon\Carbon;
use Gmf\Sys\Models;
use Gmf\Sys\Models\User;
use GuzzleHttp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Uuid;

class AmibaDtiRunJob implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $dtis = [];
	protected $context;
	protected $ent_id;
	protected $sessionId;
	public $timeout = 12000;
	public $tries = 1;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Array $context = [], Array $dtis = []) {
		$this->context = $context;
		$this->dtis = $dtis;
		if (!empty($this->context['ent_id'])) {
			$this->ent_id = $this->context['ent_id'];
		}
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
		if (!empty($this->context['ent_id'])) {
			$this->ent_id = $this->context['ent_id'];
		}
		$e = false;
		try {
			$dt = Carbon::now();
			$this->sessionId = $dt->toDateString() . '' . Uuid::generate();
			//日志
			Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'state_enum' => 'runing', 'memo' => '接口程序处理.开始，上下文：' . json_encode($this->context)]);

			$query = Models\Dti::with('category');
			$query->whereIn('id', $this->dtis);
			$query->orderBy('sequence');
			$datas = $query->get();

			Models\Dti::whereIn('id', $this->dtis)->update(['is_running' => 1, 'begin_date' => new Carbon, 'end_date' => null, 'msg' => '正在执行!' . (new Carbon)]);
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
				Models\Dti::whereIn('id', $this->dtis)->update(['msg' => $e->getMessage()]);
				Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'state_enum' => 'failed', 'memo' => '接口程序处理.结束', 'content' => $e->getMessage()]);
			} else {
				Models\Dti::whereIn('id', $this->dtis)->update(['msg' => '']);
				Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'state_enum' => 'succeed', 'memo' => '接口程序处理.结束']);
			}

		}
		if ($e) {
			throw $e;
		}
	}
	private function fetchDataFromU9($dti, $paramsConfig = []) {
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
			$input['parameters'] = json_encode($this->parseParams($dti->body, $paramsConfig));
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
	private function fetchDataFromDti($dti, $paramsConfig = []) {
		$result = false;
		$base_uri = '';
		$apiPath = $dti->path ?: $dti->code;
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
		$input = [];
		if (!empty($dti->header)) {
			$header = $this->parseParams($dti->header, $paramsConfig);
			if ($header) {
				foreach ($header as $pk => $pv) {
					$input[$pk] = $pv;
				}
			}
		}
		if ($dti->body && count($dti->body)) {
			$input['params'] = $this->parseParams($dti->body, $paramsConfig);
		}
		Log::error(static::class . "dti post to {$base_uri}{$apiPath} begin:" . json_encode($input));
		$res = $client->request('POST', $apiPath, [
			'json' => $input,
		]);
		Log::error(static::class . "dti post to {$base_uri}{$apiPath} end:" . json_encode($input));

		$result = (string) $res->getBody();

		$result = json_decode($result);
		if (isset($result->d)) {
			$result = $result->d;
		}
		if (isset($result->data)) {
			$result = $result->data;
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
						if ($pv == '#{' . $key . '}#') {
							$parseValue = $value;
							break;
						}
					}
					$paramsObj->{$pk} = $parseValue;
				}
			}
			return $paramsObj;
		}
		return [];
	}
	private function getDtiParamConfig($dti) {
		$dtiParams = Models\DtiParam::where('dti_id', $dti->id)->get();
		$dtiCategoryParams = Models\DtiParam::where('category_id', $dti->category_id)->get();
		$emptyParams = Models\DtiParam::where(function ($query) {
			$query->whereNull('category_id')->orWhere('category_id', '');
		})->where(function ($query) {
			$query->whereNull('dti_id')->orWhere('dti_id', '');
		})->get();

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
			Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'runing', 'memo' => '接口程序[' . $dti->name . ']远程调用.开始']);
			$paramsConfig = $this->getDtiParamConfig($dti);
			Models\Dti::where('id', $dti->id)->update(['msg' => '远程获取数据开始...']);
			if ($dti->category && starts_with($dti->category->code, 'u9')) {
				$result = $this->fetchDataFromU9($dti, $paramsConfig);
			} else {
				$result = $this->fetchDataFromDti($dti, $paramsConfig);
			}
			Models\Dti::where('id', $dti->id)->update(['msg' => '远程获取数据结束...']);

			Models\Dti::where('id', $dti->id)->update(['msg' => '处理数据存储开始...']);

			$this->callLocalStore($dti, $result, $paramsConfig);

			Models\Dti::where('id', $dti->id)->update(['msg' => '处理数据存储结束...']);

		} catch (\Exception $exception) {
			$e = $exception;
			Log::error($e);
		} finally {
			if ($e) {
				Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'failed', 'memo' => '接口程序[' . $dti->name . ']远程调用.结束', 'content' => $e->getMessage()]);
			} else {
				Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'succeed', 'memo' => '接口程序[' . $dti->name . ']远程调用.结束']);
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
		Log::error(static::class . ' callLocalStore call:');
		$e = false;
		$apiPath = false;
		if ($dti->local && $dti->local->path) {
			$apiPath = $dti->local->path;
		}
		$base_uri = $this->context['local_host'];
		if (empty($apiPath)) {
			Log::error(static::class . ' callLocalStore apiPath is null');
			return;
		}
		if (empty($data)) {
			Log::error(static::class . ' callLocalStore data is null,returned!');
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

		try {

			$collection = collect($data);

			$chunks = $collection->chunk(5000);

			$batchItems = $chunks->toArray();
			$i = 1;
			foreach ($batchItems as $key => $value) {
				Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'runing', 'memo' => '接口程序[' . $dti->name . ']本地数据存储.开始 ' . count($value) . ':' . $i . '/' . count($batchItems)]);
				Log::error(static::class . ' callLocalStore post ' . $i . ':' . $base_uri . $apiPath);

				$input = [
					'server_name' => $dti->code,
					'data_src_identity' => ($dti->category ? $dti->category->code : '') . ':' . $dti->code,
					'date' => $this->context['date'],
					'fm_date' => $this->context['fm_date'],
					'to_date' => $this->context['to_date'],
					'datas' => $value,
					'batch' => $i,
				];
				$res = $client->request('POST', $apiPath, [
					'json' => $input,
				]);
				$result = (string) $res->getBody();
				$result = json_decode($result);
				$result = $result->data;
				$i++;
			}

		} catch (\Exception $exception) {
			$e = $exception;
			Log::error($e);
		} finally {
			if ($e) {
				Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'failed', 'memo' => '接口程序[' . $dti->name . ']本地数据存储.结束', 'content' => $e->getMessage()]);
			} else {
				Models\DtiLog::create(['ent_id' => $this->ent_id, 'session' => $this->sessionId, 'date' => $this->context['date'], 'dti_id' => $dti->id, 'state_enum' => 'succeed', 'memo' => '接口程序[' . $dti->name . ']本地数据存储.结束']);
			}
		}
		if ($e) {
			throw $e;
		}
	}
}
