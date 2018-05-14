<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DataInitLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_init_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'init_id', 'is_init', 'profit', 'group_id'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setGroupIdAttribute($value) {
		$this->attributes['group_id'] = empty($value) ? null : $value;
	}

	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'init_id', 'is_init', 'profit', 'group_id']);

			$tmpItem = false;
			if (!empty($builder->group)) {
				$tmpItem = Group::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->group)->orWhere('name', $builder->group);
				})->first();
			}
			if ($tmpItem) {
				$data['group_id'] = $tmpItem->id;
			}
			$main = static::create($data);
		});
	}
}
