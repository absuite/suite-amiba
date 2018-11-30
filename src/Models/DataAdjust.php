<?php

namespace Suite\Amiba\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DataAdjust extends Model {
  use Snapshotable, HasGuard;
  protected $table = 'suite_amiba_data_adjusts';
  public $incrementing = false;
  protected $fillable = ['id', 'ent_id', 'doc_date', 'doc_no', 'purpose_id', 'period_id', 'memo'];

  //属性
  public function setEntIdAttribute($value) {
    $this->attributes['ent_id'] = empty($value) ? null : $value;
  }
  public function setPurposeIdAttribute($value) {
    $this->attributes['purpose_id'] = empty($value) ? null : $value;
  }
  public function setPeriodIdAttribute($value) {
    $this->attributes['period_id'] = empty($value) ? null : $value;
  }
  public function purpose() {
    return $this->belongsTo('Suite\Amiba\Models\Purpose');
  }
  public function period() {
    return $this->belongsTo('Suite\Cbo\Models\PeriodAccount', 'period_id');
  }
  public function lines() {
    return $this->hasMany('Suite\Amiba\Models\DataAdjustLine', 'adjust_id');
  }
}
