<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Entity;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DataAdjustLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_data_adjust_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'adjust_id', 'fm_group_id', 'to_group_id', 'to_element_id','fm_element_id', 'money'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function fm_element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
	public function to_element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
	public function fm_group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
	public function to_group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}
}
