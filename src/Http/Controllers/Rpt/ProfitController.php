<?php

namespace Suite\Amiba\Http\Controllers\Rpt;
use DB;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Suite\Amiba\Libs\QueryHelper;

class ProfitController extends Controller {
	public function rank(Request $request) {
        $size = $request->input('size', 10);
	
		$query = DB::table('suite_amiba_result_profits as l')
			->join('suite_cbo_period_accounts as p', 'l.period_id', '=', 'p.id')
			->join('suite_amiba_groups as g', 'l.group_id', '=', 'g.id');
		$query->addSelect('g.name as name');
		$query->addSelect(DB::raw("SUM(l.cost) AS this_cost"));
		$query->addSelect(DB::raw("SUM(l.income) AS this_income"));
        $query->addSelect(DB::raw("SUM(l.income-l.cost) AS this_profit"));
        	
		$query->groupBy('g.name', 'p.name');
        $query->orderBy(DB::raw("SUM(l.income-l.cost)"));
        
       $pager= $query->paginate($size);
       $items=$pager->getCollection();
       $items=$items->each(function ($item) {
        $item->this_cost = floatval($item->this_cost);
        $item->this_income = floatval($item->this_income);
        $item->this_profit = floatval($item->this_profit);
    });
       $pager->setCollection($items);
        
        
		return $this->toJson($pager);
	}
}
