<?php

namespace Suite\Amiba\Models;
use Closure;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models\Entity;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Suite\Amiba\Jobs;
use Suite\Cbo\Models as CboModels;
use Validator;

class DataDoc extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_docs';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'doc_no', 'doc_date', 'state_enum',
		'purpose_id', 'fm_group_id', 'to_group_id', 'period_id', 'element_id', 'currency_id',
		'qty', 'money', 'use_type_enum', 'created_by', 'src_type_enum', 'src_id', 'src_no', 'memo'];

	public function validate() {
		if (empty($this->money)) {
			$this->money = 0;
		}
	}
	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function fm_group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function to_group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function period() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodAccount');
	}
	public function currency() {
		return $this->belongsTo('Suite\Cbo\Models\Currency');
	}
	public function element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
	public function lines() {
		return $this->hasMany('Suite\Amiba\Models\DataDocLine', 'doc_id');
	}

	private static function importHeadData($data) {
		$input = array_only($data, ['doc_no', 'doc_date', 'money', 'src_id', 'src_no', 'memo']);

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
			'fm_group' => function ($value, $input) use ($ent_id) {
				if (empty($value) || empty($input['purpose_id'])) {
					return false;
				}
				return Group::where('ent_id', $ent_id)->where('purpose_id', $input['purpose_id'])->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'to_group' => function ($value, $input) use ($ent_id) {
				if (empty($value) || empty($input['purpose_id'])) {
					return false;
				}
				return Group::where('ent_id', $ent_id)->where('purpose_id', $input['purpose_id'])->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'period' => function ($value, $input) use ($ent_id) {
				if (empty($input['doc_date']) || empty($input['purpose_id'])) {
					return false;
				}
				$query = DB::table('suite_cbo_period_accounts as pa');
				$query->join('suite_amiba_purposes as p', 'pa.calendar_id', '=', 'p.calendar_id');
				$query->where('p.id', $input['purpose_id']);
				$query->where('p.ent_id', $ent_id);
				$query->whereRaw("'" . $input['doc_date'] . "' between pa.from_date and pa.to_date");
				return $query->value('pa.id');
			},
			'element' => function ($value, $input) use ($ent_id) {
				if (empty($value) || empty($input['purpose_id'])) {
					return false;
				}
				return Element::where('ent_id', $ent_id)->where('purpose_id', $input['purpose_id'])->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'currency' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Currency::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
		]);
		$validator = Validator::make($input, [
			'doc_no' => 'required',
			'doc_date' => 'required',
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = $ent_id;
		$input['state_enum'] = 'opened';
		return static::create($input);
	}
	private static function importLineData($head, $data) {
		$input = array_only($data, ['qty', 'price', 'money', 'expense_code', 'account_code', 'memo']);

		$ent_id = GAuth::entId();
		$input = InputHelper::fillEntity($input, $data, [
			'trader' => function ($value) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Trader::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
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
			'mfc' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Mfc::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'project' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Project::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
			'unit' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Unit::where('ent_id', $ent_id)->where(function ($query) use ($value) {$query->where('code', $value)->orWhere('name', $value);})->value('id');
			},
		]);
		$input['doc_id'] = $head->id;
		$input['ent_id'] = $ent_id;
		if (empty($input['money'])) {
			$input['money'] = 0;
		}
		if (empty($input['price'])) {
			$input['price'] = 0;
		}
		if (empty($input['qty'])) {
			$input['qty'] = 0;
		}
		return DataDocLine::create($input);
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

				if (count($line) && !(empty($item['qty']) && empty($item['money']))) {
					static::importLineData($head, $line);
					$hasLines = true;
				}
			}
			if ($hasLines) {
				$job = new Jobs\AmibaDataDocMoneyJob($head->id);
				$job->handle();
			}
		}
		return true;
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'doc_no', 'doc_date', 'state_enum',
				'purpose_id', 'fm_group_id', 'to_group_id', 'period_id', 'element_id', 'currency_id',
				'qty', 'money', 'use_type_enum', 'created_by', 'src_type_enum', 'src_id', 'src_no', 'memo']);

			if (empty($data['state_enum'])) {
				$data['state_enum'] = 'approved';
			}

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
			if (empty($builder->use_type_enum) && !empty($builder->use_type_name)) {
				$tmpItem = Entity::getEnumItem('suite.amiba.doc.use.type.enum', $builder->use_type_name);
			}
			if ($tmpItem) {
				$data['use_type_enum'] = $tmpItem->name;
			}

			$tmpItem = false;
			if (!empty($builder->fm_group)) {
				$tmpItem = Group::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->fm_group)->orWhere('name', $builder->fm_group);
				})->first();
			}
			if ($tmpItem) {
				$data['fm_group_id'] = $tmpItem->id;
			}

			$tmpItem = false;
			if (!empty($builder->to_group)) {
				$tmpItem = Group::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->to_group)->orWhere('name', $builder->to_group);
				})->first();
			}
			if ($tmpItem) {
				$data['to_group_id'] = $tmpItem->id;
			}

			$tmpItem = false;
			if (!empty($builder->element)) {
				$tmpItem = Element::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->element)->orWhere('name', $builder->element);
				})->first();
			}
			if ($tmpItem) {
				$data['element_id'] = $tmpItem->id;
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
