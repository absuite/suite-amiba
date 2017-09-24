<?php
use Faker\Factory as Faker;
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaGroupSeeder extends Seeder {
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

		$faker = Faker::create();
		Models\Group::where('ent_id', $this->entId)->delete();
		Models\GroupLine::where('ent_id', $this->entId)->delete();

		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("amb01")->name("集团公司")->type_name("")->factor_name("")->employees("")->is_leaf("0");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb01")->code("amb0101")->name("集团财务")->type_name("部门")->factor_name("服务")->employees("8")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb01")->code("amb0102")->name("集团研发")->type_name("部门")->factor_name("研发")->employees("20")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb01")->code("amb0103")->name("集团仓储")->type_name("部门")->factor_name("采购")->employees("9")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("amb02")->name("生产公司")->type_name("")->factor_name("")->employees("")->is_leaf("0");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb02")->code("amb0201")->name("生产管理组")->type_name("部门")->factor_name("服务")->employees("6")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb02")->code("amb0203")->name("机加车间")->type_name("")->factor_name("")->employees("")->is_leaf("0");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb0203")->code("amb020301")->name("车加工")->type_name("工作中心")->factor_name("生产")->employees("30")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb0203")->code("amb020302")->name("铣加工")->type_name("工作中心")->factor_name("生产")->employees("24")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb02")->code("amb0204")->name("热处理车间")->type_name("部门")->factor_name("生产")->employees("15")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("amb03")->name("新品公司")->type_name("组织")->factor_name("生产")->employees("18")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("")->code("amb04")->name("销售")->type_name("")->factor_name("")->employees("")->is_leaf("0");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb04")->code("amb0402")->name("销售一部")->type_name("部门")->factor_name("销售")->employees("15")->is_leaf("1");});
		Models\Group::build(function (Builder $b) {$b->ent_id($this->entId)->purpose("ob01")->parent("amb04")->code("amb0403")->name("销售二部")->type_name("部门")->factor_name("销售")->employees("25")->is_leaf("1");});

		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0101")->data("dp80001");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0102")->data("dp80002");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0103")->data("dp80003");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0103")->data("dp80004");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0201")->data("dp81002");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0201")->data("dp81003");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0201")->data("dp81004");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0203")->data("dp81005");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb020301")->data("wc8100501");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb020302")->data("wc8100502");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0204")->data("dp81006");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb03")->data("org820");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0402")->data("dp83001");});
		Models\GroupLine::build(function (Builder $b) {$b->ent_id($this->entId)->group("amb0403")->data("dp83002");});
	}
}
