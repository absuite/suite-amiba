<?php

namespace Suite\Amiba\Models;
use GAuth;
use Gmf\Sys\Database\Concerns\BatchImport;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Validator;

class DocFi extends Model {
	use Snapshotable, HasGuard, BatchImport;
	protected $table = 'suite_amiba_doc_fis';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id',
		'src_doc_id', 'src_doc_type', 'src_key_id', 'src_key_type',
		'doc_no', 'doc_date', 'memo', 'org', 'person',
		'biz_type', 'doc_type',
		'fm_org', 'fm_dept', 'fm_work', 'fm_team', 'fm_wh', 'fm_person',
		'trader', 'project', 'account', 'debit_money', 'credit_money',
		'factor1', 'factor2', 'factor3', 'factor4', 'factor5', 'data_src_identity'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function formatDefaultValue() {
		if (empty($this->debit_money)) {
			$this->debit_money = 0;
		}
		if (empty($this->credit_money)) {
			$this->credit_money = 0;
		}
	}
	public function validate() {
		Validator::make($this->toArray(), [
			'doc_no' => 'required',
			'doc_date' => ['required', 'date'],
			'biz_type' => ['required'],
			'debit_money' => ['numeric'],
			'credit_money' => ['numeric'],
		])->validate();
	}
	public static function fromImport($datas) {
		$datas = $datas->map(function ($row) {
			$row['data_src_identity'] = 'import';
			$row['ent_id'] = GAuth::entId();
			return $row;
		});
		static::BatchImport($datas);
	}
}
