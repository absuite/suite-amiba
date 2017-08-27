<?php

namespace Suite\Amiba\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DocFi extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_doc_fis';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id',
		'src_doc_id', 'src_doc_type', 'src_key_id', 'src_key_type',
		'doc_no', 'doc_date', 'memo', 'org', 'person',
		'biz_type', 'doc_type',
		'fm_org', 'fm_dept', 'fm_work', 'fm_team', 'fm_wh', 'fm_person',
		'trader', 'project', 'account', 'debit_money', 'credit_money',
		'factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'data_src_identity'];

}
