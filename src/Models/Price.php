<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Suite\Cbo\Models as CboModels;

class Price extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_prices';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'purpose_id', 'period_id', 'group_id', 'code', 'name', 'memo', 'state_enum'];

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
