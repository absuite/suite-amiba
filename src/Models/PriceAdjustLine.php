<?php

namespace Suite\Amiba\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class PriceAdjustLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_price_adjust_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'adjust_id', 'group_id', 'item_id', 'type_enum', 'cost_price'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setGroupIdAttribute($value) {
		$this->attributes['group_id'] = empty($value) ? null : $value;
	}
	public function setItemIdAttribute($value) {
		$this->attributes['item_id'] = empty($value) ? null : $value;
	}
	public function setCostPriceAttribute($value) {
		$this->attributes['cost_price'] = empty($value) ? 0 : $value;
	}

	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function item() {
		return $this->belongsTo('Suite\Cbo\Models\Item');
	}
}
