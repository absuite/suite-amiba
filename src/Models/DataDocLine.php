<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Suite\Cbo\Models as CboModels;

class DataDocLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_doc_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'doc_id', 'modeling_id', 'modeling_line_id',
		'trader_id', 'item_category_id', 'item_id',
		'mfc_id', 'project_id', 'unit_id', 'qty', 'price', 'money',
		'expense_code', 'subject_code', 'memo'];

	//属性
	public function setModelingIdAttribute($value) {
		$this->attributes['modeling_id'] = empty($value) ? null : $value;
	}
	public function setModelingLineIdAttribute($value) {
		$this->attributes['modeling_line_id'] = empty($value) ? null : $value;
	}
	public function setTraderIdAttribute($value) {
		$this->attributes['trader_id'] = empty($value) ? null : $value;
	}
	public function setItemCategoryIdAttribute($value) {
		$this->attributes['item_category_id'] = empty($value) ? null : $value;
	}
	public function setItemIdAttribute($value) {
		$this->attributes['item_id'] = empty($value) ? null : $value;
	}
	public function setMfcIdAttribute($value) {
		$this->attributes['mfc_id'] = empty($value) ? null : $value;
	}
	public function setProjectIdAttribute($value) {
		$this->attributes['project_id'] = empty($value) ? null : $value;
	}
	public function setUnitIdAttribute($value) {
		$this->attributes['unit_id'] = empty($value) ? null : $value;
	}
	public function setQtyAttribute($value) {
		$this->attributes['qty'] = empty($value) ? 0 : $value;
	}
	public function setPriceAttribute($value) {
		$this->attributes['price'] = empty($value) ? 0 : $value;
	}
	public function setMoneyAttribute($value) {
		$this->attributes['money'] = empty($value) ? 0 : $value;
	}

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
