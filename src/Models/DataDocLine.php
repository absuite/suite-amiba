<?php

namespace Suite\Amiba\Models;
use Closure;
use Suite\Cbo\Models as CboModels;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DataDocLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_doc_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'doc_id', 'trader_id', 'item_category_id', 'item_id',
		'mfc_id', 'project_id', 'unit_id', 'qty', 'price', 'money',
		'expense_code', 'subject_code', 'memo'];

	public function trader() {
		return $this->belongsTo('Suite\Cbo\Models\Trader');
	}
	public function item_category() {
		return $this->belongsTo('Suite\Cbo\Models\ItemCategory');
	}
	public function item() {
		return $this->belongsTo('Suite\Cbo\Models\Item');
	}
	public function unit() {
		return $this->belongsTo('Suite\Cbo\Models\Unit');
	}
	public function mfc() {
		return $this->belongsTo('Suite\Cbo\Models\Mfc');
	}
	public function project() {
		return $this->belongsTo('Suite\Cbo\Models\Project');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'doc_id', 'trader_id', 'item_category_id', 'item_id',
				'mfc_id', 'project_id', 'unit_id', 'qty', 'price', 'money',
				'expense_code', 'account_code', 'memo']);

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
			if (!empty($builder->unit)) {
				$tmpItem = CboModels\Unit::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->unit)->orWhere('name', $builder->unit);
				})->first();
			}
			if ($tmpItem) {
				$data['unit_id'] = $tmpItem->id;
			}
			static::create($data);
		});
	}
}
