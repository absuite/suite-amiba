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
      <md-content class="flex layout-column md-form">
        <md-layout md-gutter md-row>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="核算目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="阿米巴" :md-init="init_group_ref" required md-ref-id="suite.amiba.group.ref" v-model="model.main.group"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="开始期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.fm_period">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="结束期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.to_period">
            </md-ref-input>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="fetchLineDatas" ref="grid" :row-focused="false" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="指标类型" field="type_enum" dataType="enum" editable ref-id="suite.amiba.data.target.type.enum" />
            <md-grid-column label="基准核算要素" field="element" dataType="entity" ref-id="suite.amiba.element.ref" width="200px" editable/>
            <md-grid-column label="目标额度" field="money" editable/>
            <md-grid-column label="目标比率" field="rate" editable/>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
  </md-part>
</template>
<script>
import model from 'cbo/mixins/MdModel/MdModel';
import modelGrid from 'cbo/mixins/MdModel/MdModelGrid';
export default {
  mixins: [model, modelGrid],
  computed: {
    canSave() {
      return this.validate(true);
    }
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, {
        'purpose': 'required',
        'group': 'required',
        'fm_period': 'required',
        'to_period': 'required',
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
          'purpose': this.$root.configs.purpose,
          'group': null,
          fm_period: this.$root.configs.period,
          to_period: this.$root.configs.period
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.data.target.list' } });
    },
    onLineAdd() {
      this.$refs.grid && this.$refs.grid.addDatas({ element: null });
    },
    init_group_ref(options) {
      options.wheres.$leaf = { 'is_leaf': '1' };
      if (this.model.main.purpose) {
        options.wheres.$purpose = { 'purpose_id': this.model.main.purpose.id };
      } else {
        options.wheres.$purpose = false;
      }
    },
    init_period_ref(options) {
      if (this.model.main.purpose && this.model.main.purpose.calendar_id) {
        options.wheres.$calendar = { 'calendar_id': this.model.main.purpose.calendar_id };
      } else {
        options.wheres.$calendar = { 'calendar_id': this.$root.configs.calendar.id };
      }
    },
  },
  created() {
    this.model.entity = 'suite.amiba.data.target';
    this.model.order = "created_at";
    this.route = 'amiba/data-targets';
  },
};
</script>