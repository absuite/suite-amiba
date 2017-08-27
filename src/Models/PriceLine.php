<?php

namespace Suite\Amiba\Models;
use Closure;
use Suite\Cbo\Models as CboModels;
use Gmf\Sys\Builder;
use Gmf\Sys\Models as SysModels;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class PriceLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_price_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'price_id', 'group_id', 'item_id', 'type_enum', 'cost_price'];

	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function item() {
		return $this->belongsTo('Suite\Cbo\Models\Item');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'price_id', 'group_id', 'item_id', 'type_enum', 'cost_price']);

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
			if (!empty($builder->item)) {
				$tmpItem = CboModels\Item::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->item)->orWhere('name', $builder->item);
				})->first();
			}
			if ($tmpItem) {
				$data['item_id'] = $tmpItem->id;
			}

			$tmpItem = false;
			if (empty($builder->type_enum) && !empty($builder->type_name)) {
				$tmpItem = SysModels\Entity::getEnumItem('suite.amiba.price.type.enum', $builder->type_name);
			}
			if ($tmpItem) {
				$data['type_enum'] = $tmpItem->name;
			}

			static::create($data);
		});
	}

}
