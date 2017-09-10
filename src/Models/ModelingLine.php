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
		'biz_type_enum', 'value_type_enum', 'doc_type_id', 'project_code', 'item_category_id', 'account_code', 'trader_id', 'item_id',
		'factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'adjust',
		'match_direction_enum', 'src_group_id', 'src_element_id'];
	public function element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
	public function src_element() {
		return $this->belongsTo('Suite\Amiba\Models\Element');
	}
	public function src_group() {
		return $this->belongsTo('Suite\Amiba\Models\Group');
	}

	public function doc_type() {
		return $this->belongsTo('Suite\Cbo\Models\DocType');
	}
	public function item_category() {
		return $this->belongsTo('Suite\Cbo\Models\ItemCategory');
	}
	public function item() {
		return $this->belongsTo('Suite\Cbo\Models\Item');
	}
	public function trader() {
		return $this->belongsTo('Suite\Cbo\Models\Trader');
	}
}
