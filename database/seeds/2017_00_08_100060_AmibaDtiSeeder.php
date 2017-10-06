<?php
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;

class AmibaDtiSeeder extends Seeder {
	public $entId = '';

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		if (empty($this->entId)) {
			$this->entId = config('gmf.ent.id');
		}
		if (empty($this->entId)) {
			return;
		}

		//Models\Dti::where('ent_id', $this->entId)->delete();

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_ORG")->name("组织")
				->local('api/cbo/orgs/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_CURRENCY")->name("币种")
				->local('api/cbo/currencies/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_UOM")->name("计量单位")
				->local('api/cbo/units/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_DEPT")->name("部门")
				->local('api/cbo/depts/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_WORK")->name("工作中心")
				->local('api/cbo/works/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_WH")->name("存储地点")
				->local('api/cbo/whs/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_PERSON")->name("人员")
				->local('api/cbo/persons/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_PROJECT_CATEGORY")->name("项目分类")
				->local('api/cbo/project-categories/batch');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_PROJECT")->name("项目")
				->local('api/cbo/projects/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_ITEM_CATEGORY")->name("物料分类")
				->local('api/cbo/item-categories/batch');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_ITEM")->name("物料")
				->local('api/cbo/items/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_TRADER_CATEGORY")->name("客商分类")
				->local('api/cbo/trader-categories/batch');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_TRADER")->name("客商")
				->local('api/cbo/traders/batch');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_CBO_DOC_TYPE")->name("单据类型")
				->local('api/cbo/doc-types/batch');
		});

		//业务数据
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_MISCRCV")->name("业务-杂收")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_MISCSHIP")->name("业务-杂发")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_TRANSIN")->name("业务-库存调入")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_TRANSOUT")->name("业务-库存调出")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_MOCOMPLETE")->name("业务-生产完工")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_MOISSUE")->name("业务-生产领料")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_RCV")->name("业务-采购收货")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_RCVRTN")->name("业务-采购退货")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_SHIP")->name("业务-销售出货")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_SHIPRTN")->name("业务-销售退货")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_AP_APBILL")->name("业务-应付")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});

		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_ER_EXPENSEPLAN")->name("业务-费用预算")
				->local('api/amiba/doc-bizs/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});

		//财务数据
		Models\Dti::build(function (Builder $b) {
			$b->ent_id($this->entId)->category('u9')->code("P_ISV_TRANSDATA_TRADE_GL_VOUCHER")->name("财务-凭证")
				->local('api/amiba/doc-fis/batch'); $b->body('{"FromDate":"#{fm_date}#","toDate":"#{to_date}#"}');
		});
	}
}
