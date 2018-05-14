<?php

namespace Suite\Amiba\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class AllotMethod extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'suite_amiba_allot_methods';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'purpose_id'];

	//属性
	public function setEntIdAttribute($value) {
		$this->attributes['ent_id'] = empty($value) ? null : $value;
	}
	public function setPurposeIdAttribute($value) {
		$this->attributes['purpose_id'] = empty($value) ? null : $value;
	}

	public function purpose() {
		return $this->belongsTo('Suite\Amiba\Models\Purpose');
	}
	public function lines() {
		return $this->hasMany('Suite\Amiba\Models\AllotMethodLine', 'method_id');
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'memo', 'purpose_id']);

			$tmpItem = false;
			if (!empty($builder->purpose)) {
				$tmpItem = Purpose::where('ent_id', $builder->ent_id)->where(function ($query) use ($builder) {
					$query->where('code', $builder->purpose)->orWhere('name', $builder->purpose);
				})->first();
			}
			if ($tmpItem) {
				$data['purpose_id'] = $tmpItem->id;
			}
			$find = array_only($data, ['code', 'ent_id']);
			static::updateOrCreate($find, $data);
		});
	}
}
