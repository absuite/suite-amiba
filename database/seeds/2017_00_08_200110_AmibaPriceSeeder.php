<?php
use Faker\Factory as Faker;
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;
use Suite\Amiba\Models;

class AmibaPriceSeeder extends Seeder {
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
		if (Models\Price::where('ent_id', $this->entId)->count()) {
			return;
		}

		$faker = Faker::create();
		Models\Price::where('ent_id', $this->entId)->delete();

		Models\Price::build(function (Builder $b) {$b->ent_id($this->entId)->code("price_amb0103")->name("集团仓储价表")->purpose("ob01")->group("集团仓储")->period("")->id(md5($this->entId . "qwfkj8r98fy2354yrferqw0978"));});
		Models\Price::build(function (Builder $b) {$b->ent_id($this->entId)->code("price_amb020301")->name("车加工价表")->purpose("ob01")->group("车加工")->period("")->id(md5($this->entId . "qwfkj8r98fy2354yrferqw0979"));});
		Models\Price::build(function (Builder $b) {$b->ent_id($this->entId)->code("price_amb020302")->name("铣加工价表")->purpose("ob01")->group("铣加工")->period("")->id(md5($this->entId . "qwfkj8r98fy2354yrferqw0980"));});
		Models\Price::build(function (Builder $b) {$b->ent_id($this->entId)->code("price_amb0204")->name("热处理价表")->purpose("ob01")->group("热处理车间")->period("")->id(md5($this->entId . "qwfkj8r98fy2354yrferqw0981"));});
		Models\Price::build(function (Builder $b) {$b->ent_id($this->entId)->code("price_amb03")->name("新品公司价表")->purpose("ob01")->group("新品公司")->period("")->id(md5($this->entId . "qwfkj8r98fy2354yrferqw0982"));});
		Models\Price::build(function (Builder $b) {$b->ent_id($this->entId)->code("price_amb0201")->name("质检部价表")->purpose("ob01")->group("生产管理组")->period("")->id(md5($this->entId . "qwfkj8r98fy2354yrferqw0983"));});

		Models\PriceLine::where('ent_id', $this->entId)->delete();
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0978"))->type_name("采购价")->item("108地质无缝管")->unit("公斤")->cost_price("5.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0978"))->type_name("销售价")->item("108地质无缝管")->unit("公斤")->cost_price("6.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0979"))->type_name("销售价")->item("150钻管")->unit("件")->cost_price("1200.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0979"))->type_name("销售价")->item("120钻管")->unit("件")->cost_price("850.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0980"))->type_name("销售价")->item("150钻管")->unit("件")->cost_price("1600.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0980"))->type_name("销售价")->item("120钻管")->unit("件")->cost_price("1120.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0981"))->type_name("采购价")->item("150钻管")->unit("件")->cost_price("1600.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0981"))->type_name("采购价")->item("120钻管")->unit("件")->cost_price("1120.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0981"))->type_name("采购价")->item("150专用接头")->unit("件")->cost_price("150.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0981"))->type_name("采购价")->item("120专用接头")->unit("件")->cost_price("120.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0981"))->type_name("销售价")->item("150高风压潜孔钻杆")->unit("件")->cost_price("2000.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0981"))->type_name("销售价")->item("120船钻钻杆")->unit("件")->cost_price("1400.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0982"))->type_name("采购价")->item("导向套")->unit("件")->cost_price("70.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0982"))->type_name("采购价")->item("防护筒")->unit("件")->cost_price("180.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0982"))->type_name("销售价")->item("120船钻钻杆总成")->unit("件")->cost_price("2200.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0983"))->type_name("销售价")->item("机加质检")->unit("小时")->cost_price("350.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0983"))->type_name("销售价")->item("装配质检")->unit("小时")->cost_price("300.00 ");});
		Models\PriceLine::build(function (Builder $b) {$b->ent_id($this->entId)->price_id(md5($this->entId . "qwfkj8r98fy2354yrferqw0983"))->type_name("销售价")->item("采购质检")->unit("小时")->cost_price("400.00 ");});

	}
}
