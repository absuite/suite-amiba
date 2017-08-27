<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class GroupLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_group_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'group_id', 'data_id', 'data_type'];

	public function data() {
		return $this->morphTo();
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'group_id', 'data_id', 'data_type']);

			$tmpItem = false;
			if (!empty($builder->group)) {
				$tmpItem = Group::where('code', $builder->group)->where('ent_id', $builder->ent_id)->first();
			}
			if ($tmpItem) {
				$data['group_id'] = $tmpItem->id;
			}
			if (empty($data['group_id'])) {
				return;
			}

			$header = Group::find($data['group_id']);
			if (empty($header)) {
				return;
			}
			$data['data_type'] = Group::getDataTypeName($header->type_enum);

			$tmpItem = false;
			if (!empty($builder->data)) {
				$tmpItem = $data['data_type']::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->data)->orWhere('name', $builder->data);
				})->first();
			}
			if ($tmpItem) {
				$data['data_id'] = $tmpItem->id;
			}
			static::create($data);
		});
	}
}
