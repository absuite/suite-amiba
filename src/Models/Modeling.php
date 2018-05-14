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

class Modeling extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_modelings';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'purpose_id', 'group_id', 'code', 'name', 'memo'];
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

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function lines() {
		return $this->hasMany('Suite\Amiba\Models\ModelingLine', 'modeling_id');
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
			'group_id' => 'required',
		])->validate();
		$input['ent_id'] = $ent_id;
		$input['state_enum'] = 'opened';
		return static::create($input);
	}
	private static function importLineData($head, $data) {
		$input = array_only($data, ['factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'project_code', 'account_code', 'adjust']);

		$ent_id = GAuth::entId();

		$input = InputHelper::fillEnum($input, $data, [
			'match_direction' => 'suite.amiba.modeling.match.direction.enum',
			'biz_type' => 'suite.cbo.biz.type.enum',
			'value_type' => 'suite.amiba.value.type.enum',
		]);

		$input = InputHelper::fillEntity($input, $data, [
			'element' => function ($value) use ($ent_id, $head) {
				if (empty($value)) {
					return false;
				}
				return Element::where('ent_id', $ent_id)->where('purpose_id', $head->purpose_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'to_group' => function ($value) use ($ent_id, $head) {
				if (empty($value)) {
					return false;
				}
				return Group::where('ent_id', $ent_id)->where('purpose_id', $head->purpose_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'match_group' => function ($value) use ($ent_id, $head) {
				if (empty($value)) {
					return false;
				}
				return Group::where('ent_id', $ent_id)->where('purpose_id', $head->purpose_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'doc_type' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\DocType::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'item_category' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\ItemCategory::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'item' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Item::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'trader' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Trader::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
		]);

		$input['modeling_id'] = $head->id;
		$input['ent_id'] = $ent_id;
		if (empty($input['adjust'])) {
			$input['adjust'] = 100;
		}
		if (empty($input['match_group_id'])) {
			$input['match_group_id'] = $head->group_id;
		}
		if (empty($input['match_direction_enum'])) {
			$input['match_direction_enum'] = 'fm';
		}
		if (empty($input['value_type_enum'])) {
			$input['value_type_enum'] = 'amt';
		}
		return ModelingLine::create($input);
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

				if (count($line) && !(empty($item['line.element']))) {
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

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'purpose_id', 'group_id', 'code', 'name', 'memo']);

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

			static::create($data);
		});
	}
}
