<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Entity;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DataTargetLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_target_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'target_id', 'element_id', 'type_enum', 'rate', 'money'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setElementIdAttribute($value) {
		$this->attributes['element_id'] = empty($value) ? null : $value;
	}

	public function element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'target_id', 'element_id', 'type_enum', 'rate', 'money']);
			if (empty($data['rate']) || !$data['rate']) {
				$data['rate'] = 0;
			}
			if (empty($data['money']) || !$data['money']) {
				$data['money'] = 0;
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
			if (empty($builder->type_enum) && !empty($builder->type_name)) {
				$tmpItem = Entity::getEnumItem('suite.amiba.data.target.type.enum', $builder->type_name);
			}
			if ($tmpItem) {
				$data['type_enum'] = $tmpItem->name;
			}
			static::create($data);
		});
	}
}
