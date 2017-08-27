<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DataTimeLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_time_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'time_id', 'group_id', 'nor_time', 'over_time', 'total_time'];

	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'time_id', 'group_id', 'nor_time', 'over_time', 'total_time']);

			$tmpItem = false;
			if (!empty($builder->group)) {
				$tmpItem = Group::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->group)->orWhere('name', $builder->group);
				})->first();
			}
			if ($tmpItem) {
				$data['group_id'] = $tmpItem->id;
			}
			if (empty($data['nor_time'])) {
				$data['nor_time'] = 0;
			}
			if (empty($data['over_time'])) {
				$data['over_time'] = 0;
			}
			$data['total_time'] = $data['nor_time'] + $data['over_time'];

			static::create($data);
		});
	}
}
