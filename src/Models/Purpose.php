<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Suite\Cbo\Models as CboModels;

class Purpose extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_purposes';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'calendar_id', 'currency_id'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setCalendarIdAttribute($value) {
		$this->attributes['calendar_id'] = empty($value) ? null : $value;
	}

	public function calendar() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodCalendar');
	}
	public function currency() {
		return $this->belongsTo('Suite\Cbo\Models\Currency');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'memo', 'calendar_id', 'currency_id']);

			$tmpItem = false;
			if (!empty($builder->calendar)) {
				$tmpItem = CboModels\PeriodCalendar::where('code', $builder->calendar)->where('ent_id', $builder->ent_id)->first();
			}
			if ($tmpItem) {
				$data['calendar_id'] = $tmpItem->id;
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
			$find = array_only($data, ['code', 'ent_id']);

			static::updateOrCreate($find, $data);
		});
	}
}
