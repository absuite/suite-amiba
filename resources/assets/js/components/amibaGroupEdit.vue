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
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column md-form md-form">
        <md-layout md-gutter md-row>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>编码</label>
              <md-input required v-model="model.main.code"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>名称</label>
              <md-input required v-model="model.main.name"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="核算目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="上级阿米巴单元" :md-init="initParentGroupRef" md-ref-id="suite.amiba.group.ref" v-model="model.main.parent">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>类型</label>
              <md-enum md-enum-id="suite.amiba.group.type.enum" v-model="model.main.type_enum"></md-enum>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>经营体类型</label>
              <md-enum md-enum-id="suite.amiba.group.factor.enum" v-model="model.main.factor_enum"></md-enum>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>员工人数</label>
              <md-input required type="number" v-model="model.main.employees"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>备注</label>
              <md-input v-model="model.main.memo"></md-input>
            </md-field>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="fetchLineDatas" ref="grid" :row-focused="false" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="构成" field="data" dataType="entity" :ref-id="lineRefID" width="300px" editable/>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref :md-ref-id="lineRefID" ref="lineRef" @confirm="lineRefClose"></md-ref>
  </md-part>
</template>
<script>
import model from 'gmf/core/mixins/MdModel/MdModel';
import common from 'gmf/core/utils/common';
import modelGrid from 'gmf/core/mixins/MdModel/MdModelGrid';
export default {
  data() {
    return {
      lineRefID: ''
    };
  },
  mixins: [model, modelGrid],
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
          purpose: this.$root.configs.purpose,
          'code': '',
          'name': '',
          'memo': '',
          'parent': null,
          'type_enum': '',
          'factor_enum': ''
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
        this.$refs.grid && this.$refs.grid.addDatas({ data: v, id: v.id });
      });
    },
    initParentGroupRef(options) {
      if (this.model.main.purpose) {
        options.wheres.$purpose = { 'purpose_id': this.model.main.purpose.id };
      } else {
        options.wheres.$purpose = false;
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