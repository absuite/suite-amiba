<?php

namespace Suite\Amiba\Models;
use GAuth;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Validator;

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

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}

	public static function fromImport($data) {
		$datas->each(function ($row, $key) {
			$row['data_src_identity'] = 'import';
			Validator::make($row, [
				'doc_no' => 'required',
				'doc_date' => ['required', 'date'],
				'biz_type' => [
					'required',
					Rule::in(['voucher']),
				],
				'debit_money' => ['numeric'],
				'credit_money' => ['numeric'],
			])->validate();
			$row['ent_id'] = GAuth::entId();
			static::create($row);
		});
	}
}
