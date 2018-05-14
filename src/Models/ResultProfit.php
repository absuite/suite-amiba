<?php

namespace Suite\Amiba\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class ResultProfit extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_result_profits';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'purpose_id', 'period_id', 'group_id', 'is_init', 'init_profit', 'income', 'cost', 'bal_profit', 'time_profit', 'time_output', 'time_total'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setPurposeIdAttribute($value) {
		$this->attributes['purpose_id'] = empty($value) ? null : $value;
	}
	public function setPeriodIdAttribute($value) {
		$this->attributes['period_id'] = empty($value) ? null : $value;
	}
	public function setGroupIdAttribute($value) {
		$this->attributes['group_id'] = empty($value) ? null : $value;
	}

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function period() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodAccount');
	}
	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
}
