<?php

use Faker\Factory as Faker;
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaElementSeeder extends Seeder {
	private $entId = '';

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->entId = config('gmf.ent.id');

		$faker = Faker::create();
		Models\Element::where('ent_id', $this->entId)->delete();

		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("101")->name("总收入")->direction_name("增项")->type_name("收入")->factor_name("")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("101")->code("10101")->name("外部收入")->direction_name("增项")->type_name("收入")->factor_name("")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("101")->code("10102")->name("内部收入")->direction_name("增项")->type_name("收入")->factor_name("")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("201")->name("总成本")->direction_name("增项")->type_name("成本")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("201")->code("20101")->name("内部采购成本")->direction_name("增项")->type_name("成本")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("201")->code("20102")->name("外部材料成本")->direction_name("增项")->type_name("成本")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("201")->code("20103")->name("人工成本")->direction_name("增项")->type_name("成本")->factor_name("变动")->is_manual("1");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("201")->code("20104")->name("制造成本")->direction_name("增项")->type_name("成本")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("301")->name("销售费用")->direction_name("增项")->type_name("费用")->factor_name("")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("301")->code("30101")->name("招待费用")->direction_name("增项")->type_name("费用")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("301")->code("30104")->name("差旅费用")->direction_name("增项")->type_name("费用")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("301")->code("30105")->name("佣金")->direction_name("增项")->type_name("费用")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("302")->name("管理费用")->direction_name("增项")->type_name("费用")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("303")->name("财务费用")->direction_name("增项")->type_name("费用")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("304")->name("提成")->direction_name("增项")->type_name("费用")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("305")->name("质检费用")->direction_name("增项")->type_name("费用")->factor_name("变动")->is_manual("");});
		Models\Element::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("306")->name("研发费用")->direction_name("增项")->type_name("费用")->factor_name("固定")->is_manual("");});

	}
}
