<?php
$ns = 'Suite\Amiba\Http\Controllers';
Route::prefix('api/amiba')->middleware(['api', 'auth:api'])->namespace($ns)->group(function () {
	//Route::resource('docs', 'DocController', ['only' => ['index']]);

	Route::get('/elements/all', 'ElementController@all');
	Route::resource('elements', 'ElementController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::resource('purposes', 'PurposeController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/groups/{id}/lines', 'GroupController@showLines');
	Route::get('/groups/all', 'GroupController@all');
	Route::resource('groups', 'GroupController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/modelings/refs/project', 'ModelingController@get_ref_project');
	Route::get('/modelings/refs/account', 'ModelingController@get_ref_account');
	Route::get('/modelings/refs/factor1', 'ModelingController@get_ref_factor1');
	Route::get('/modelings/refs/factor2', 'ModelingController@get_ref_factor2');
	Route::get('/modelings/refs/factor3', 'ModelingController@get_ref_factor3');
	Route::get('/modelings/refs/factor4', 'ModelingController@get_ref_factor4');
	Route::get('/modelings/refs/factor5', 'ModelingController@get_ref_factor5');
	Route::get('/modelings/{id}/lines', 'ModelingController@showLines');
	Route::resource('modelings', 'ModelingController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::post('dtis/run', 'DtiController@run');
	Route::get('dtis/log', 'DtiController@log');
	
	Route::get('dti-modelings/prices', 'DtiModelingController@GetPriceError');
	Route::resource('dti-modelings', 'DtiModelingController', ['only' => ['index', 'store', 'destroy']]);

	Route::get('/allot-methods/{id}/lines', 'AllotMethodController@showLines');
	Route::resource('allot-methods', 'AllotMethodController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/allot-rules/relations', 'AllotRuleController@relations');
	Route::get('/allot-rules/{id}/lines', 'AllotRuleController@showLines');
	Route::resource('allot-rules', 'AllotRuleController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/data-inits/{id}/lines', 'DataInitController@showLines');
	Route::resource('data-inits', 'DataInitController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/data-docs/{id}/lines', 'DataDocController@showLines');
	Route::resource('data-docs', 'DataDocController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/data-times/{id}/lines', 'DataTimeController@showLines');
	Route::resource('data-times', 'DataTimeController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::resource('data-closes', 'DataCloseController', ['only' => ['index', 'store', 'destroy']]);

	Route::resource('data-distributes', 'DataDistributeController', ['only' => ['index', 'store', 'destroy']]);
	Route::resource('data-accountings', 'DataAccountingController', ['only' => ['index', 'store', 'destroy']]);

	Route::get('/data-adjusts/{id}/lines', 'DataAdjustController@showLines');
	Route::resource('data-adjusts', 'DataAdjustController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/data-targets/{id}/lines', 'DataTargetController@showLines');
	Route::resource('data-targets', 'DataTargetController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/prices/{id}/lines', 'PriceController@showLines');
	Route::resource('prices', 'PriceController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::get('/price-adjusts/{id}/lines', 'PriceAdjustController@showLines');
	Route::resource('price-adjusts', 'PriceAdjustController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::resource('result-accounts', 'ResultAccountController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::resource('result-profits', 'ResultProfitController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::post('/doc-bizs/batch', 'DocBizController@batchStore');
	Route::resource('doc-bizs', 'DocBizController', ['only' => ['destroy']]);

	Route::post('/doc-fis/batch', 'DocFiController@batchStore');
	Route::resource('doc-fis', 'DocFiController', ['only' => ['destroy']]);

	//reports
	Route::get('/reports/period-info', 'ReportController@getPeriodInfo');
	//阿米巴趋势分析
	Route::post('/reports/group-trend-ans', 'ReportGroupTrendAns@index');
	//阿米巴比较分析
	Route::post('/reports/group-compare-ans', 'ReportGroupCompareAns@index');
	//阿米巴类比分析
	Route::post('/reports/group-analogy-ans', 'ReportGroupAnalogyAns@index');
	//阿米巴要互素结构分析
	Route::post('/reports/group-structure-ans', 'ReportGroupStructureAns@index');
	//阿米巴排名分析
	Route::post('/reports/group-rank-ans', 'ReportGroupRankAns@index');
	//阿米巴目标达成趋势分析
	Route::post('/reports/group-purpose-trend', 'ReportGroupPurposeTrend@index');
	//阿米巴目标达成比较分析
	Route::post('/reports/group-purpose-compare', 'ReportGroupPurposeCompare@index');
	//职能式损益表
	Route::post('/reports/statement-function-ans', 'ReportStatementFunctionAns@index');
	//贡献式损益表
	Route::post('/reports/statement-devote-ans', 'ReportStatementFunctionAns@index');
	//损益趋势分析
	Route::post('/reports/statement-trend', 'ReportStatementTrend@index');
	//损益横比
	Route::post('/reports/statement-compare', 'ReportStatementCompare@index');
	//损益目标达成
	Route::post('/reports/statement-purpose', 'ReportStatementPurpose@index');
	//业务明细
	Route::post('/reports/biz/total', 'Rpt\DocBizController@total');
	//利润排名
	Route::post('/reports/profit/rank', 'Rpt\ProfitController@rank');
	Route::post('/reports/profit/total', 'Rpt\ProfitController@total');
	Route::post('/reports/profit/trend', 'Rpt\ProfitController@trend');

	Route::post('/reports/expend/analy', 'Rpt\ExpendController@analy');
	Route::post('/reports/income/analy', 'Rpt\IncomeController@analy');

	Route::post('/reports/statement/ans', 'Rpt\StatementController@ans');
});