<?php

namespace Suite\Amiba\Http\Controllers\Rpt;
use DB;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Suite\Amiba\Libs\QueryHelper;

class DocBizController extends Controller {
  public function total(Request $request) {
		$size = $request->input('size', 10);
    $query = DB::table('suite_amiba_data_docs as l')
      ->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
			->join('suite_amiba_groups as g', 'l.fm_group_id', '=', 'g.id')
			->join('suite_amiba_elements as e', 'l.element_id', '=', 'e.id')
      ->leftJoin('suite_amiba_groups as tg', 'l.to_group_id', '=', 'tg.id');
		$query->addSelect('g.name as group_name');
		$query->addSelect('tg.name as to_group_name');
		$query->addSelect('p.name as period_name');
		$query->addSelect('l.doc_no');
		$query->addSelect('l.use_type_enum');
    $query->addSelect('l.money as money');

    if ($v = $request->input('purpose_id')) {
      $query->where('g.purpose_id', $v);
    }
    if ($v = $request->input('group')) {
      $query->where('g.code', $v);
    }
    if ($v = $request->input('period')) {
      $query->where('p.code', '=', $v);
    }
    if ($v = QueryHelper::geMyGroups()) {
      $query->whereIn('l.group_id', $v);
    }
		$query->orderBy('g.name');
		$query->orderBy('p.name');
		$query->orderBy('l.doc_date','desc');

    $pager = $query->paginate($size);
    $items = $pager->getCollection();
    $items = $items->each(function ($item) {
      $item->money = floatval($item->money);
    });
    $pager->setCollection($items);
    return $this->toJson($pager);
  }
}
