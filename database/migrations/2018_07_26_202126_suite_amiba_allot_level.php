<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class SuiteAmibaAllotLevel extends Migration {
    public $mdID = "01e890ce6a9813508c29eb87c7e978c2";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $md = Metadata::create($this->mdID);
        $md->mdEntity('suite.amiba.allot.level')->comment('费用分配层级')->tableName('suite_amiba_allot_levels');

        $md->increments('id');
        $md->entity('ent', config('gmf.ent.entity'))->nullable()->comment('企业');
        $md->entity('purpose', 'suite.amiba.purpose')->nullable()->comment('核算目的');
        $md->entity('period', 'suite.cbo.period.account')->nullable()->comment('期间');
        $md->entity('group', 'suite.amiba.group')->nullable()->comment('阿米巴');
        $md->string('bizkey',250)->nullable()->comment('业务主键');
        $md->string('name',250)->nullable()->comment('备注');
        $md->integer('level')->nullable()->comment('层级');
        $md->text('memo')->nullable()->comment('备注');
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