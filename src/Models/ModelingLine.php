<?php

namespace Suite\Amiba\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class ModelingLine extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_modeling_lines';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'modeling_id', 'element_id',
		'biz_type_enum', 'value_type_enum', 'doc_type', 'project', 'item_category', 'account', 'trader', 'item',
		'factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'adjust'];
	public function element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
}
