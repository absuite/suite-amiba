<?php

namespace Suite\Amiba\Models;
use Closure;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Suite\Cbo\Models as CboModels;
use Validator;

class Price extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_prices';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'purpose_id', 'period_id', 'group_id', 'code', 'name', 'memo', 'state_enum'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setPurposeIdAttribute($value) {
		$this->attributes['purpose_id'] = empty($value) ? null : $value;
	}
	public function setGroupIdAttribute($value) {
		$this->attributes['group_id'] = empty($value) ? null : $value;
	}
	public function setPeriodIdAttribute($value) {
		$this->attributes['period_id'] = empty($value) ? null : $value;
	}

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function period() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodAccount');
	}
	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function lines() {
		return $this->hasMany('Suite\Amiba\Models\PriceLine', 'price_id');
	}

	private static function importHeadData($data) {
		$input = array_only($data, ['code', 'name', 'memo']);

		$ent_id = GAuth::entId();
		$input = InputHelper::fillEntity($input, $data, [
			'purpose' => function ($value) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return Purpose::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'group' => function ($value, $input) use ($ent_id) {
				if (empty($value) || empty($input['purpose_id'])) {
					return false;
				}
				return Group::where('ent_id', $ent_id)->where('purpose_id', $input['purpose_id'])->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
		]);
		Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
			'purpose_id' => 'required',
		])->validate();
		$input['ent_id'] = $ent_id;
		$input['state_enum'] = 'opened';
		return static::create($input);
	}
	private static function importLineData($head, $data) {
		$input = array_only($data, ['cost_price']);

		$ent_id = GAuth::entId();
		$input = InputHelper::fillEntity($input, $data, [
			'group' => function ($value) use ($ent_id, $head) {
				if (empty($value)) {
					return false;
				}
				return Group::where('ent_id', $ent_id)->where('purpose_id', $head->purpose_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'item' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Item::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value);})->value('id');
			},
		]);
		$input = InputHelper::fillEnum($input, $data, [
			'type' => 'suite.amiba.price.type.enum',
		]);
		$input['price_id'] = $head->id;
		$input['ent_id'] = $ent_id;
		if (empty($input['cost_price'])) {
			$input['cost_price'] = 0;
		}

		return PriceLine::create($input);
	}
	public static function fromImport($rows) {
		$grouped = $rows->reject(function ($item) {
			return empty($item['code']);
		})->map(function ($item) {
			$item['code'] = $item['code'] . '';
			return $item;
		})->groupBy('code')->toArray();

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

				if (count($line) && !(empty($item['line.cost_price']))) {
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

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'purpose_id', 'period_id', 'group_id', 'code', 'name', 'memo']);

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
			if (!empty($builder->group)) {
				$tmpItem = Group::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->group)->orWhere('name', $builder->group);
				})->first();
			}
			if ($tmpItem) {
				$data['group_id'] = $tmpItem->id;
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
