<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Entity;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Group extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_groups';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'purpose_id', 'parent_id'
		, 'type_enum', 'factor_enum', 'employees', 'is_leaf'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setPurposeIdAttribute($value) {
		$this->attributes['purpose_id'] = empty($value) ? null : $value;
	}
	public function setParentIdAttribute($value) {
		$this->attributes['parent_id'] = empty($value) ? null : $value;
	}

	public function parent() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function lines() {
		return $this->hasMany('Suite\Amiba\Models\GroupLine');
	}
	public static function getDataTypeName($groupType) {
		$dataType = 'Suite\Cbo\Models\Org';
		if ($groupType == 'org') {
			$dataType = 'Suite\Cbo\Models\Org';
		}
		if ($groupType == 'dept') {
			$dataType = 'Suite\Cbo\Models\Dept';
		}
		if ($groupType == 'work') {
			$dataType = 'Suite\Cbo\Models\Work';
		}
		if ($groupType == 'team') {
			$dataType = 'Suite\Cbo\Models\Team';
		}
		if ($groupType == 'item') {
			$dataType = 'Suite\Cbo\Models\Item';
		}
		if ($groupType == 'person') {
			$dataType = 'Suite\Cbo\Models\Person';
		}
		return $dataType;
	}
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'memo', 'purpose_id', 'parent_id'
				, 'type_enum', 'factor_enum', 'employees', 'is_leaf']);

			if (empty($data['employees']) || !$data['employees']) {
				$data['employees'] = 0;
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
			if (!empty($builder->parent)) {
				$tmpItem = Group::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->parent)->orWhere('name', $builder->parent);
				})->first();
			}
			if ($tmpItem) {
				$data['parent_id'] = $tmpItem->id;
			}

			$tmpItem = false;
			if (empty($builder->type_enum) && !empty($builder->type_name)) {
				$tmpItem = Entity::getEnumItem('suite.amiba.group.type.enum', $builder->type_name);
			}
			if ($tmpItem) {
				$data['type_enum'] = $tmpItem->name;
			}

			$tmpItem = false;
			if (empty($builder->factor_enum) && !empty($builder->factor_name)) {
				$tmpItem = Entity::getEnumItem('suite.amiba.group.factor.enum', $builder->factor_name);
			}
			if ($tmpItem) {
				$data['factor_enum'] = $tmpItem->name;
			}

			$find = array_only($data, ['code', 'ent_id']);

			static::updateOrCreate($find, $data);
		});
	}
}
