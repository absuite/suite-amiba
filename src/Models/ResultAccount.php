<?php

namespace Suite\Amiba\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class ResultAccount extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_result_accounts';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'purpose_id', 'period_id', 'group_id', 'element_id', 'type_enum', 'money', 'is_init', 'is_outside', 'src_id', 'src_no'];

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function period() {
		return $this->belongsTo('Suite\Cbo\Models\PeriodAccount');
	}
	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
}
