<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class AllotLevel extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_allot_levels';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'purpose_id', 'period_id', 'group_id', 'bizkey','level'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setMethodIdAttribute($value) {
		$this->attributes['method_id'] = empty($value) ? null : $value;
	}
	public function setPurposeIdAttribute($value) {
		$this->attributes['purpose_id'] = empty($value) ? null : $value;
	}
	public function setGroupIdAttribute($value) {
		$this->attributes['group_id'] = empty($value) ? null : $value;
	}

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
}
