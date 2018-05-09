<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Suite\Cbo\Models as CboModels;

class DataTarget extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_targets';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'purpose_id', 'group_id', 'fm_period_id', 'to_period_id', 'memo'];

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function fm_period() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodAccount', 'fm_period_id');
	}
	public function to_period() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodAccount', 'to_period_id');
	}
	public function lines() {
		return $this->hasMany('Suite\Amiba\Models\DataTargetLine', 'target_id');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'purpose_id', 'group_id', 'fm_period_id', 'to_period_id', 'memo']);

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
			if (!empty($builder->fm_period)) {
				$tmpItem = CboModels\PeriodAccount::where('code', $builder->fm_period)->where('ent_id', $builder->ent_id)->first();
			}
			if ($tmpItem) {
				$data['fm_period_id'] = $tmpItem->id;
			}

			$tmpItem = false;
			if (!empty($builder->to_period)) {
				$tmpItem = CboModels\PeriodAccount::where('code', $builder->to_period)->where('ent_id', $builder->ent_id)->first();
			}
			if ($tmpItem) {
				$data['to_period_id'] = $tmpItem->id;
			}

			static::create($data);
		});
	}
}
