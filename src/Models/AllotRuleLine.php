<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class AllotRuleLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_allot_rule_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'rule_id', 'group_id', 'element_id', 'rate'];
	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'rule_id', 'group_id', 'element_id', 'rate']);
			if (empty($data['rate']) || !$data['rate']) {
				$data['rate'] = 0;
			}
			$tmpItem = false;
			if (!empty($builder->rule)) {
				$tmpItem = AllotRule::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->rule)->orWhere('name', $builder->rule);
				})->first();
			}
			if ($tmpItem) {
				$data['rule_id'] = $tmpItem->id;
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
			static::create($data);
		});
	}
}
