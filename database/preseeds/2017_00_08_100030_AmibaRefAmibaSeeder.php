<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmibaRefAmibaSeeder extends Seeder {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function run() {
    $exception = DB::transaction(function () {
      // $id = "52e84c70110111e7802a993ddf50c88a";
      // Models\Query::build(function (Builder $builder) use ($id) {
      //   $builder->id($id)->name('suite.amiba.category.ref')->entity('suite.amiba.category')
      //     ->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
      // });
      $id = "52e84f80110111e78072d3f4b232c83f";
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->id($id)->name('suite.amiba.element.ref')->entity('suite.amiba.element')->matchs('code;name')
          ->fields(['id', 'code', 'name', 'memo', 'type_enum', 'direction_enum', 'parent.name', 'purpose.name']);
        $builder->orders(['code', 'created_at' => 'desc']);
        $builder->filter('a0.ent_id=#{entId}#');
      });

      $id = "52e850a0110111e7b55ff5148cfd2ab5";
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->id($id)->name('suite.amiba.purpose.ref')->entity('suite.amiba.purpose')->matchs('code;name')
          ->fields(['id', 'code', 'name', 'memo', 'calendar_id' => ['hide' => 1]]);
        $builder->orders(['code', 'created_at' => 'desc']);
        $builder->filter('a0.ent_id=#{entId}#');
      });

      $id = "52e85170110111e79ab1336ee3e6bcd5";
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->id($id)->name('suite.amiba.group.ref')->entity('suite.amiba.group')->matchs('code;name')
          ->fields(['id', 'code', 'name', 'purpose.name', 'parent.name', 'employees']);
        $builder->orders(['purpose.code' => 'asc', 'code' => 'asc', 'created_at' => 'desc']);
        $builder->filter('a0.ent_id=#{entId}#');
      });

      $id = "9784376017f911e7b88a199bb2283b45";
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->id($id)->name('suite.amiba.modeling.ref')->entity('suite.amiba.modeling')
          ->fields(['id', 'code', 'name', 'purpose.name', 'group.name']);
        $builder->orders(['code', 'created_at' => 'desc']);
        $builder->filter('a0.ent_id=#{entId}#');
      });

      $id = "01e8c7de589318c0ac319744bdb79b41";
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->id($id)->name('suite.amiba.price.ref')->entity('suite.amiba.price')->matchs('code;name')
          ->fields(['id', 'code', 'name', 'group.name']);
        $builder->orders(['code', 'created_at' => 'desc']);
        $builder->filter('a0.ent_id=#{entId}#');
      });

      $id = "1fc56380129f11e79b5533ff7b5a42d5";
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->id($id)->name('suite.amiba.allot.method.ref')->entity('suite.amiba.allot.method')->matchs('code;name')
          ->fields(['id', 'code' => '编码', 'name' => '名称']);
        $builder->orders(['code', 'created_at' => 'desc']);
        $builder->filter('a0.ent_id=#{entId}#');
      });

      $id = "1fc56420129f11e7b1a1855e942d9500";
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->id($id)->name('suite.amiba.allot.rule.ref')->entity('suite.amiba.allot.rule')->matchs('code;name')
          ->fields(['id', 'code' => '编码', 'name' => '名称']);
        $builder->orders(['code', 'created_at' => 'desc']);
        $builder->filter('a0.ent_id=#{entId}#');
      });

      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->name('suite.amiba.modeling.project.code.ref')->component('suite.amiba.modeling.project.code.ref');
      });
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->name('suite.amiba.modeling.account.code.ref')->component('suite.amiba.modeling.account.code.ref');
      });
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->name('suite.amiba.modeling.factor1.ref')->component('suite.amiba.modeling.factor1.ref');
      });
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->name('suite.amiba.modeling.factor2.ref')->component('suite.amiba.modeling.factor2.ref');
      });
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->name('suite.amiba.modeling.factor3.ref')->component('suite.amiba.modeling.factor3.ref');
      });
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->name('suite.amiba.modeling.factor4.ref')->component('suite.amiba.modeling.factor4.ref');
      });
      Models\Query::build(function (Builder $builder) use ($id) {
        $builder->name('suite.amiba.modeling.factor5.ref')->component('suite.amiba.modeling.factor5.ref');
      });
    });
  }
}
