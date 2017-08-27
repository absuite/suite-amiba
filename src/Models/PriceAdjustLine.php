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

	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function item() {
		return $this->belongsTo('Suite\Cbo\Models\Item');
	}
}
