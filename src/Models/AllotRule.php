<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class AllotRule extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_allot_rules';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'purpose_id', 'method_id', 'group_id', 'element_id'];

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function method() {
		return $this->belongsTo('Suite\Amiba\Models\AllotMethod');
	}
	public function element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function lines() {
		return $this->hasMany('Suite\Amiba\Models\AllotRuleLine', 'rule_id');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'memo', 'purpose_id', 'method_id', 'group_id', 'element_id']);

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
			if (!empty($builder->method)) {
				$tmpItem = AllotMethod::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->method)->orWhere('name', $builder->method);
				})->first();
			}
			if ($tmpItem) {
				$data['method_id'] = $tmpItem->id;
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
			if (!empty($builder->element)) {
				$tmpItem = Element::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->element)->orWhere('name', $builder->element);
				})->first();
			}
			if ($tmpItem) {
				$data['element_id'] = $tmpItem->id;
			}

			$find = array_only($data, ['code', 'ent_id']);

			static::updateOrCreate($find, $data);
		});
	}
}
