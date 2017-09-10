<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Entity;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Element extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_elements';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo',
		'purpose_id', 'parent_id', 'type_enum', 'direction_enum', 'factor_enum', 'scope_enum', 'is_manual',
	];
	protected $casts = [
		'is_manual' => 'integer',
	];
	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}

	public function parent() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'memo', 'purpose_id', 'parent_id', 'type_enum', 'direction_enum', 'factor_enum', 'scope_enum', 'is_manual']);

			if (empty($data['is_manual']) || !$data['is_manual']) {
				$data['is_manual'] = 0;
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
			if (empty($builder->type_enum) && !empty($builder->type_name)) {
				$tmpItem = Entity::getEnumItem('suite.amiba.element.type.enum', $builder->type_name);
			}
			if ($tmpItem) {
				$data['type_enum'] = $tmpItem->name;
			}

			$tmpItem = false;
			if (empty($builder->direction_enum) && !empty($builder->direction_name)) {
				$tmpItem = Entity::getEnumItem('suite.amiba.element.direction.enum', $builder->direction_name);
			}
			if ($tmpItem) {
				$data['direction_enum'] = $tmpItem->name;
			}

			$tmpItem = false;
			if (empty($builder->factor_enum) && !empty($builder->factor_name)) {
				$tmpItem = Entity::getEnumItem('suite.amiba.element.factor.enum', $builder->factor_name);
			}
			if ($tmpItem) {
				$data['factor_enum'] = $tmpItem->name;
			}

			$tmpItem = false;
			if (!empty($builder->parent)) {
				$tmpItem = Element::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->parent)->orWhere('name', $builder->parent);
				})->first();
			}
			if ($tmpItem) {
				$data['parent_id'] = $tmpItem->id;
			}
			static::create($data);
		});
	}
}
