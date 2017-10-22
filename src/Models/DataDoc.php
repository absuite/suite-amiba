<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Entity;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Suite\Cbo\Models as CboModels;

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
