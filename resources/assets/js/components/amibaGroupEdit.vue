<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="save" :disabled="!canSave">保存</md-button>
        <md-button @click.native="cancel">放弃</md-button>
        <md-button @click.native="create">新增</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button @click.native="copy" :disabled="!canCopy">复制</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button @click.native="list">列表</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-pager @paging="paging" :options="model.pager"></md-part-toolbar-pager>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>阿米巴单元</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>编辑</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-layout md-gutter>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>编码</label>
              <md-input required v-model="model.main.code"></md-input>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>名称</label>
              <md-input required v-model="model.main.name"></md-input>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>核算目的</label>
              <md-input-ref required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>上级阿米巴单元</label>
              <md-input-ref @init="initParentGroupRef" md-ref-id="suite.amiba.group.ref" v-model="model.main.parent">
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>类型</label>
              <md-enum md-enum-id="suite.amiba.group.type.enum" v-model="model.main.type_enum"></md-enum>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>经营体类型</label>
              <md-enum md-enum-id="suite.amiba.group.factor.enum" v-model="model.main.factor_enum"></md-enum>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>员工人数</label>
              <md-input required type="number" v-model="model.main.employees"></md-input>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>备注</label>
              <md-textarea v-model="model.main.memo"></md-textarea>
            </md-input-container>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="model.main.lines" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="构成" width="300px">
              <template scope="row">
                {{ row.data&&row.data.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref :md-ref-id="lineRefID" v-model="row.data"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref :md-ref-id="lineRefID" ref="lineRef" @confirm="lineRefClose"></md-ref>
  </md-part>
</template>
<script>
import model from '../../gmf-sys/core/mixin/model';
import common from '../../gmf-sys/core/utils/common';
export default {
  data() {
    return {
      lineRefID: ''
    };
  },
  mixins: [model],
  computed: {
    canSave() {
      return this.validate(true);
    }
  },
  watch: {
    'model.main.type_enum': function(value, oldValue) {
      if (value === 'org') {
        this.lineRefID = 'suite.cbo.org.ref';
      }
      if (value === 'dept') {
        this.lineRefID = 'suite.cbo.dept.ref';
      }
      if (value === 'work') {
        this.lineRefID = 'suite.cbo.work.ref';
      }
      if (value === 'team') {
        this.lineRefID = 'suite.cbo.team.ref';
      }
      if (value === 'item') {
        this.lineRefID = 'suite.cbo.item.ref';
      }
      if (oldValue && value !== oldValue && this.model.main) {
        this.model.main.lines = [];
      }
    }
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, {
        'code': 'required',
        'name': 'required',
        'purpose': 'required',
        'type_enum': 'required',
        'factor_enum': 'required'
      });
      var fail = validator.fails();
      if (fail && !notToast) {
        this.$toast(validator.errors.all());
      }
      return !fail;
    },
    initModel() {
      return {
        main: {
          purpose: this.$root.userConfig.purpose,
          'code': '',
          'name': '',
          'memo': '',
          'lines': [],
          'type_enum': ''
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.group.list' } });
    },
    onLineAdd() {
      this.lineRefID && this.$refs['lineRef'].open();
    },
    lineRefClose(datas) {
      this._.forEach(datas, (v, k) => {
        this.model.main.lines.push({ data: v, id: v.id });
      });
    },
    initParentGroupRef(options) {
      if (this.model.main.purpose) {
        options.wheres.purpose = { name: 'purpose_id', value: this.model.main.purpose.id };
      } else {
        options.wheres.purpose = false;
      }
    },
  },
  created() {
    this.model.entity = 'suite.amiba.group';
    this.model.order = "code";
    this.route = 'amiba/groups';
  },
};
</script>