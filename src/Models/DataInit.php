<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Suite\Cbo\Models as CboModels;

class DataInit extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_inits';
	public $incrementing = false;
	protected $fillable = ['id', 'doc_no', 'ent_id', 'purpose_id', 'period_id', 'currency_id'];

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function period() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodAccount');
	}
	public function currency() {
		return $this->belongsTo('Suite\Cbo\Models\Currency');
	}
	public function lines() {
		return $this->hasMany('Suite\Amiba\Models\DataInitLine', 'init_id');
	}

	private static function importHeadData($data) {
		$input = array_only($data, ['doc_no', 'memo']);

		$input = InputHelper::fillEnum($input, $data, [
			'use_type' => 'suite.amiba.doc.use.type.enum',
			'src_type' => 'suite.amiba.doc.src.type.enum',
		]);
		$ent_id = GAuth::entId();
		$input = InputHelper::fillEntity($input, $data, [
			'purpose' => function ($value) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return Purpose::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'period' => function ($value, $input) use ($ent_id) {
				if (empty($input['purpose_id'])) {
					return false;
				}
				$query = DB::table('suite_cbo_period_accounts as pa');
				$query->join('suite_amiba_purposes as p', 'pa.calendar_id', '=', 'p.calendar_id');
				$query->where('p.id', $input['purpose_id']);
				$query->where('p.ent_id', $ent_id);
				$query->where("pa.name", $value);
				return $query->value('pa.id');
			},
		]);
		$validator = Validator::make($input, [
			'doc_no' => 'required',
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = $ent_id;
		return static::create($input);
	}
	private static function importLineData($head, $data) {
		$input = array_only($data, ['qty', 'price', 'money', 'expense_code', 'account_code', 'memo']);

		$ent_id = GAuth::entId();
		$input = InputHelper::fillEntity($input, $data, [
			'group' => function ($value) use ($ent_id, $head) {
				if (empty($value)) {
					return false;
				}
				return Group::where('ent_id', $ent_id)->where('purpose_id', $head->purpose_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
		]);
		$input['init_id'] = $head->id;
		$input['ent_id'] = $ent_id;
		$input['is_init'] = 1;
		if (empty($input['profit'])) {
			$input['profit'] = 0;
		}
		return DataInitLine::create($input);
	}
	public static function fromImport($rows) {
		$grouped = $rows->reject(function ($item) {
			return empty($item['doc_no']);
		})->map(function ($item) {
			$item['doc_no'] = $item['doc_no'] . '';
			return $item;
		})->groupBy('doc_no')->toArray();

		foreach ($grouped as $gk => $items) {
			$head = static::importHeadData($items[0]);
			if (!$head) {
				continue;
			}
			$hasLines = false;
			foreach ($items as $ik => $item) {
				$line = collect($item)->filter(function ($v, $k) {
					return starts_with($k, 'line.');
				})->mapWithKeys(function ($item, $key) {
					return [str_after($key, 'line.') => $item];
				})->all();

				if (count($line) && !empty($item['profit'])) {
					static::importLineData($head, $line);
					$hasLines = true;
				}
			}
		}
		return true;
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'purpose_id', 'period_id', 'currency_id']);

			$tmpItem = false;
			if (!empty($builder->purpose)) {
				$tmpItem = Purpose::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->purpose)->orWhere('name', $builder->purpose);
				})->first();
			}
			if ($tmpItem) {
				$data['purpose_id'] = $tmpItem->id;
			}

			$tmpItem = false;
			if (!empty($builder->currency)) {
				$tmpItem = CboModels\Currency::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->currency)->orWhere('name', $builder->currency);
				})->first();
			}
			if ($tmpItem) {
				$data['currency_id'] = $tmpItem->id;
			}

			$tmpItem = false;
			if (!empty($builder->period)) {
				$tmpItem = CboModels\PeriodAccount::where('code', $builder->period)->where('ent_id', $builder->ent_id)->first();
			}
			if ($tmpItem) {
				$data['period_id'] = $tmpItem->id;
			}

			static::create($data);
		});
	}
}
