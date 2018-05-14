<?php

namespace Suite\Amiba\Models;
use GAuth;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Validator;

class DocBiz extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_doc_bizs';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id',
		'src_doc_id', 'src_doc_type', 'src_key_id', 'src_key_type',
		'doc_no', 'doc_date', 'memo', 'org', 'person',
		'biz_type', 'doc_type', 'direction',
		'fm_org', 'fm_dept', 'fm_work', 'fm_team', 'fm_wh', 'fm_person',
		'to_org', 'to_dept', 'to_work', 'to_team', 'to_wh', 'to_person',
		'trader', 'item', 'item_category', 'project', 'lot', 'mfc',
		'currency', 'uom', 'qty', 'price', 'money', 'tax',
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
					Rule::in(['ship', 'rcv',
						'miscRcv', 'miscShip',
						'transfer', 'moRcv', 'moIssue',
						'process', 'receivables', 'payment',
						'ar', 'ap', 'plan',
						'expense']),
				],
				'direction' => [
					'required',
					Rule::in(['rcv', 'ship']),
				],
				'qty' => ['numeric'],
				'price' => ['numeric'],
				'money' => ['numeric'],
				'tax' => ['numeric'],
			])->validate();
			$row['ent_id'] = GAuth::entId();
			static::create($row);
		});
	}
}
