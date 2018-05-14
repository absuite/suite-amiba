<?php

namespace Suite\Amiba\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DataAccounting extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_accountings';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'purpose_id', 'period_id', 'memo', 'revoked', 'succeed', 'start_time', 'end_time', 'msg'];

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

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function period() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodAccount');
	}
}
