<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class AllotMethodLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_allot_method_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'method_id', 'group_id', 'rate'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setGroupIdAttribute($value) {
		$this->attributes['group_id'] = empty($value) ? null : $value;
	}
	public function setRateAttribute($value) {
		$this->attributes['rate'] = empty($value) ? 0 : $value;
	}

	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'method_id', 'group_id', 'rate']);
			if (empty($data['rate']) || !$data['rate']) {
				$data['rate'] = 0;
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
			if (!empty($builder->method)) {
				$tmpItem = AllotMethod::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->method)->orWhere('name', $builder->method);
				})->first();
			}
			if ($tmpItem) {
				$data['method_id'] = $tmpItem->id;
			}
			static::create($data);
		});
	}
}
