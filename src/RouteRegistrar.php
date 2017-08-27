<?php

namespace Suite\Amiba;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar {
	/**
	 * The router implementation.
	 *
	 * @var Router
	 */
	protected $router;

	/**
	 * Create a new route registrar instance.
	 *
	 * @param  Router  $router
	 * @return void
	 */
	public function __construct(Router $router) {
		$this->router = $router;
	}

	/**
	 * Register routes for transient tokens, clients, and personal access tokens.
	 *
	 * @return void
	 */
	public function all() {
		$this->router->group(['prefix' => 'amiba', 'middleware' => ['api', 'auth:api', 'visitor', 'ent_check']], function ($router) {

			$router->resource('docs', 'DocController', ['only' => ['index']]);

			$router->get('/elements/all', ['uses' => 'ElementController@all']);
			$router->resource('elements', 'ElementController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->resource('purposes', 'PurposeController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->get('/groups/all', ['uses' => 'GroupController@all']);
			$router->resource('groups', 'GroupController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('modelings', 'ModelingController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->post('dtis/run', ['uses' => 'DtiController@run']);
			$router->get('dtis/log', ['uses' => 'DtiController@log']);

			$router->resource('dti-modelings', 'DtiModelingController', ['only' => ['index', 'store', 'destroy']]);

			$router->resource('allot-methods', 'AllotMethodController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('allot-rules', 'AllotRuleController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->resource('data-inits', 'DataInitController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('data-docs', 'DataDocController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('data-times', 'DataTimeController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('data-closes', 'DataCloseController', ['only' => ['index', 'store', 'destroy']]);

			$router->resource('data-distributes', 'DataDistributeController', ['only' => ['index', 'store', 'destroy']]);
			$router->resource('data-accountings', 'DataAccountingController', ['only' => ['index', 'store', 'destroy']]);
			$router->resource('data-adjusts', 'DataAdjustController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('data-targets', 'DataTargetController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->resource('prices', 'PriceController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('price-adjusts', 'PriceAdjustController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->resource('result-accounts', 'ResultAccountController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->resource('result-profits', 'ResultProfitController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->post('/doc-bizs/batch', ['uses' => 'DocBizController@batchStore']);
			$router->post('/doc-fis/batch', ['uses' => 'DocFiController@batchStore']);

			//reports
			$router->get('/reports/period-info', ['uses' => 'ReportController@getPeriodInfo']);
			//阿米巴趋势分析
			$router->post('/reports/group-trend-ans', ['uses' => 'ReportGroupTrendAns@index']);
			//阿米巴比较分析
			$router->post('/reports/group-compare-ans', ['uses' => 'ReportGroupCompareAns@index']);
			//阿米巴类比分析
			$router->post('/reports/group-analogy-ans', ['uses' => 'ReportGroupAnalogyAns@index']);
			//阿米巴要互素结构分析
			$router->post('/reports/group-structure-ans', ['uses' => 'ReportGroupStructureAns@index']);
			//阿米巴排名分析
			$router->post('/reports/group-rank-ans', ['uses' => 'ReportGroupRankAns@index']);
			//阿米巴目标达成趋势分析
			$router->post('/reports/group-purpose-trend', ['uses' => 'ReportGroupPurposeTrend@index']);
			//阿米巴目标达成比较分析
			$router->post('/reports/group-purpose-compare', ['uses' => 'ReportGroupPurposeCompare@index']);
			//职能式损益表
			$router->post('/reports/statement-function-ans', ['uses' => 'ReportStatementFunctionAns@index']);
			//贡献式损益表
			$router->post('/reports/statement-devote-ans', ['uses' => 'ReportStatementFunctionAns@index']);
			//损益趋势分析
			$router->post('/reports/statement-trend', ['uses' => 'ReportStatementTrend@index']);
			//损益横比
			$router->post('/reports/statement-compare', ['uses' => 'ReportStatementCompare@index']);
			//损益目标达成
			$router->post('/reports/statement-purpose', ['uses' => 'ReportStatementPurpose@index']);

		});
	}
}
