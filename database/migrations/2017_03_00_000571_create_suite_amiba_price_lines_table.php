<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateSuiteAmibaPriceLinesTable extends Migration {
  public $mdID = "4bbd5e2018cf11e7aa525fbc9aee4922";
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    $md = Metadata::create($this->mdID);
    $md->mdEntity('suite.amiba.price.line')->comment('考核价表明细')->tableName('suite_amiba_price_lines');

    $md->string('id', 100)->primary();
    $md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
    $md->entity('price', 'suite.amiba.price')->nullable()->comment('考核价表');
    $md->enum('type', 'suite.amiba.price.type.enum')->nullable()->comment('价格类型');
    $md->entity('group', 'suite.amiba.group')->nullable()->comment('目的阿米巴');
    $md->entity('item', 'suite.cbo.item')->nullable()->comment('物料');
    $md->decimal('cost_price', 30, 2)->default(0)->comment('价格');
    $md->boolean('disabled')->nullable()->comment('失效的');
    $md->timestamps();

    $md->build();

  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Metadata::dropIfExists($this->mdID);
  }
}
