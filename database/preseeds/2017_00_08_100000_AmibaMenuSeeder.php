<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmibaMenuSeeder extends Seeder {
	private $sequence = '18';
	private $tag = 'e1742e00420e11e79d1897dbf903cb38';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {

		$exception = DB::transaction(function () {
			$id = "78398ae00a1311e7bab8c7e04ea89a28";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->root_id($id)->code('amiba')->name('阿米巴核算')
					->uri('amiba')->sequence($this->sequence . '0000')->tag($this->tag);
			});
			//组织建模
			$id = "78398d200a1311e7b529e11d5f7aec05";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.setting')->name('组织建模')->parent('amiba')
					->uri('amiba.setting')->sequence($this->sequence . '0102')->tag($this->tag);
			});
			// $id = "78398dd00a1311e7af4ff3efa70e215a";
			// Menu::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->code('amiba.setting.category')->name('核算要素表')->parent('amiba.setting')
			// 		->uri('amiba.category.edit')->sequence($this->sequence . '0101')->tag($this->tag);
			// });

			$id = "78398eb00a1311e7b43b8b416f3b8014";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.setting.purpose')->name('核算目的')->parent('amiba.setting')
					->uri('amiba.purpose.edit')->sequence($this->sequence . '0104')->tag($this->tag);
			});

			$id = "78398e400a1311e7aa5e5ffa69dba592";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.setting.element')->name('核算要素')->parent('amiba.setting')
					->uri('amiba.element.edit')->sequence($this->sequence . '0106')->tag($this->tag);
			});

			$id = "78398f200a1311e7b5e9e7a7792a39c5";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.setting.group')->name('阿米巴单元')->parent('amiba.setting')
					->uri('amiba.group.edit')->sequence($this->sequence . '0108')->tag($this->tag);
			});
			$id = "5897395017e811e78afb9d06f8a0e2da";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.setting.modeling')->name('阿米巴经营模型')->parent('amiba.setting')
					->uri('amiba.modeling.edit')->sequence($this->sequence . '0110')->tag($this->tag);
			});
			//核算模型
			$id = "78398f800a1311e7b4afb965cc6db9c4";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.allot')->name('核算模型')->parent('amiba')
					->uri('amiba.allot')->sequence($this->sequence . '0200')->tag($this->tag);
			});
			$id = "afa0af600a1311e7bf809ba83b475909";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data.init')->name('经营台账初始化')->parent('amiba.allot')
					->uri('amiba.data.init.edit')->sequence($this->sequence . '0201')->tag($this->tag);
			});

			$id = "58973cf017e811e7a5b9637111cf6bd6";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.price')->name('交易价表')->parent('amiba.allot')
					->uri('amiba.price.list')->sequence($this->sequence . '0202')->tag($this->tag);
			});

			$id = "58973e3017e811e785733dcc713b8d8e";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.price.adjust')->name('调价单')->parent('amiba.allot')
					->uri('amiba.price.adjust.list')->sequence($this->sequence . '0204')->tag($this->tag);
			});

			$id = "01e8c7d86a541990a3f63159095cf4fa";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.price.query')->name('料价查询')->parent('amiba.allot')
					->uri('amiba.price.query')->sequence($this->sequence . '0205')->tag($this->tag);
			});

			$id = "99f8827017e911e7b51adb0893dd0bab";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data.target')->name('经营目标')->parent('amiba.allot')
					->uri('amiba.data.target.list')->sequence($this->sequence . '0206')->tag($this->tag);
			});

			// $id = "697f27c0129e11e785d351785edb2c8c";
			// Menu::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->code('amiba.allot.mark')->name('分配基数')->parent('amiba.allot')
			// 		->uri('amiba.allot.mark.edit')->sequence($this->sequence . '0208')->tag($this->tag);
			// });
			$id = "78398ff00a1311e7be05c74e786e7590";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.allot.method')->name('分配方法')->parent('amiba.allot')
					->uri('amiba.allot.method.edit')->sequence($this->sequence . '0210')->tag($this->tag);
			});
			$id = "783990600a1311e7a56c13a29f7f189c";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.allot.rule')->name('分配标准')->parent('amiba.allot')
					->uri('amiba.allot.rule.edit')->sequence($this->sequence . '0212')->tag($this->tag);
			});
			//接口设置
			$id = "9e7c20600a1311e7ad90a7b5b9fb6e00";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.dti')->name('接口设置')->parent('amiba')
					->uri('amiba.dti')->sequence($this->sequence . '0300')->tag($this->tag);
			});

			$id = "9e7c24800a1311e780c3352dacd3b79a";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.dti.run')->name('接口执行')->parent('amiba.dti')
					->uri('amiba.dti.run')->sequence($this->sequence . '0304')->tag($this->tag);
			});
			$id = "9e7c23d00a1311e79e74992ec21fdcdb";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.dti.log')->name('接口日志')->parent('amiba.dti')
					->uri('amiba.dti.log')->sequence($this->sequence . '0306')->tag($this->tag);
			});
			$id = "143ce9f021bc11e782fed341668d3553";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.dti.modeling.edit')->name('数据建模')->parent('amiba.dti')
					->uri('amiba.dti.modeling.edit')->sequence($this->sequence . '0308')->tag($this->tag);
			});
			//数据
			$id = "afa0ac000a1311e7ba3ccbf8faec4a9a";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data')->name('阿米巴核算')->parent('amiba')
					->uri('amiba.data')->sequence($this->sequence . '0400')->tag($this->tag);
			});
			$id = "afa0ae400a1311e7bc296dc704b34fbd";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data.time')->name('时间数据')->parent('amiba.data')
					->uri('amiba.data.time.edit')->sequence($this->sequence . '0402')->tag($this->tag);
			});

			$id = "afa0afd00a1311e78a3fe397c887211f";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data.doc')->name('核算数据表')->parent('amiba.data')
					->uri('amiba.data.doc.list')->sequence($this->sequence . '0406')->tag($this->tag);
			});

			$id = "8aa26d00185311e782f0637e65706dd8";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data.distribute')->name('间接费用分配')->parent('amiba.data')
					->uri('amiba.data.distribute.edit')->sequence($this->sequence . '0408')->tag($this->tag);
			});
			$id = "8aa26e00185311e79262c9ccf5ab13e4";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data.accounting')->name('经营会计核算')->parent('amiba.data')
					->uri('amiba.data.accounting.edit')->sequence($this->sequence . '0410')->tag($this->tag);
			});
			$id = "8aa26f20185311e794ec0908dba6e4fb";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data.adjust')->name('责任调整单')->parent('amiba.data')
					->uri('amiba.data.adjust.edit')->sequence($this->sequence . '0412')->tag($this->tag);
			});

			$id = "8aa26fd0185311e79c5b978f897b4fea";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.data.close')->name('期末关账')->parent('amiba.data')
					->uri('amiba.data.close.edit')->sequence($this->sequence . '0414')->tag($this->tag);
			});
			//查询
			$id = "c4440fc00a1311e7830151533f6344ed";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.query')->name('查询')->parent('amiba')
					->uri('amiba.query')->sequence($this->sequence . '0500')->tag($this->tag);
			});
			$id = "b8945340209611e7bb4c3d715b32c2ae";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.query.doc.biz')->name('业务数据查询')->parent('amiba.query')
					->uri('amiba.query.doc.biz')->sequence($this->sequence . '0504')->tag($this->tag);
			});
			$id = "b8945550209611e7a715631d27b01948";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.query.doc.fi')->name('财务数据查询')->parent('amiba.query')
					->uri('amiba.query.doc.fi')->sequence($this->sequence . '0508')->tag($this->tag);
			});

			$id = "c44411c00a1311e798fba7ab8050b95e";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.query.account')->name('考核结果表')->parent('amiba.query')
					->uri('amiba.query.account')->sequence($this->sequence . '0512')->tag($this->tag);
			});
			$id = "c44412b00a1311e7ad2bd7d76f18277a";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.query.profit')->name('经营台账')->parent('amiba.query')
					->uri('amiba.query.profit')->sequence($this->sequence . '0520')->tag($this->tag);
			});
			//报表
			$id = "dc697d800a1311e7b6c603d39b041846";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report')->name('报表')->parent('amiba')
					->uri('amiba.report')->sequence($this->sequence . '0600')->tag($this->tag);
			});

			$id = "68194a70714511e78f793158224a02fe";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.statement.function.ans')->name('职能式损益表')->parent('amiba.report')
					->uri('amiba.report.statement.function.ans')->sequence($this->sequence . '0611')->tag($this->tag);
			});
			// $id = "68194cf0714511e7a3975f423566282a";
			// Menu::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->code('amiba.report.statement.devote.ans')->name('贡献式损益表')->parent('amiba.report')
			// 		->uri('amiba.report.statement.devote.ans')->sequence($this->sequence . '0612')->tag($this->tag);
			// });

			$id = "d074ea80714511e79cb5b3e1cc21b0e9";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.statement.trend')->name('损益趋势分析')->parent('amiba.report')
					->uri('amiba.report.statement.trend')->sequence($this->sequence . '0613')->tag($this->tag);
			});
			$id = "d074eb40714511e7856d2d918b8a8b7d";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.statement.compare')->name('损益横比')->parent('amiba.report')
					->uri('amiba.report.statement.compare')->sequence($this->sequence . '0614')->tag($this->tag);
			});

			// $id = "3739b5e0714611e792384742a6765317";
			// Menu::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->code('amiba.report.statement.purpose')->name('损益目标达成')->parent('amiba.report')
			// 		->uri('amiba.report.statement.purpose')->sequence($this->sequence . '0615')->tag($this->tag);
			// });
			// $id = "51735ce0714611e7b432d70e87171b4b";
			// Menu::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->code('amiba.report.statement.periods')->name('月度损益汇总')->parent('amiba.report')
			// 		->uri('amiba.report.statement.periods')->sequence($this->sequence . '0616')->tag($this->tag);
			// });

			// $id = "51735d80714611e78b50639b2c05e66a";
			// Menu::build(function (Builder $builder) use ($id) {
			// 	$builder->id($id)->code('amiba.report.statement.detail')->name('损益明细')->parent('amiba.report')
			// 		->uri('amiba.report.statement.detail')->sequence($this->sequence . '0617')->tag($this->tag);
			// });

			$id = "dc697ff00a1311e780d15d15004f4521";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.group.trend.ans')->name('经营趋势分析')->parent('amiba.report')
					->uri('amibaReportGroupTrendAns')->sequence($this->sequence . '0621')->tag($this->tag);
			});
			$id = "dc6980c00a1311e7b2782de26f7d7405";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.group.compare.ans')->name('阿米巴比较分析')->parent('amiba.report')
					->uri('amibaReportGroupCompareAns')->sequence($this->sequence . '0623')->tag($this->tag);
			});
			$id = "3ce88a00341711e7808eb951a744312c";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.group.analogy.ans')->name('阿米巴类比分析')->parent('amiba.report')
					->uri('amibaReportGroupAnalogyAns')->sequence($this->sequence . '0626')->tag($this->tag);
			});
			$id = "dc6981400a1311e7b95e510955353769";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.group.rank.ans')->name('经营成果排名')->parent('amiba.report')
					->uri('amiba.report.group.rank.ans')->sequence($this->sequence . '0629')->tag($this->tag);
			});

			$id = "ff46f2900a1311e7ba5475166a947ed6";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.group.purpose.trend')->name('目标达成趋势')->parent('amiba.report')
					->uri('amiba.report.group.purpose.trend')->sequence($this->sequence . '0632')->tag($this->tag);
			});
			$id = "099c4d7035f011e793070d533d29f39a";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('amiba.report.group.purpose.compare')->name('目标达成分析')->parent('amiba.report')
					->uri('amiba.report.group.purpose.compare')->sequence($this->sequence . '0635')->tag($this->tag);
			});
		});
	}

}
