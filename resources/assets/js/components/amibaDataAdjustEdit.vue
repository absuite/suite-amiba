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
            <md-field>
              <label>单据编号</label>
              <md-input required v-model="model.main.doc_no"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
              <md-datepicker required md-label="单据日期" v-model="model.main.doc_date"></md-datepicker>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="核算目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>备注</label>
              <md-input v-model="model.main.memo" />
            </md-field>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="fetchLineDatas" ref="grid" :row-focused="false" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="调出核算要素" field="fm_element" dataType="entity" ref-id="suite.amiba.element.ref" width="200px" editable/>
            <md-grid-column label="调入核算要素" field="to_element" dataType="entity" ref-id="suite.amiba.element.ref" width="200px" editable/>
            <md-grid-column label="调出阿米巴" field="fm_group" dataType="entity" ref-id="suite.amiba.group.ref" width="200px" editable/>
            <md-grid-column label="调入阿米巴" field="to_group" dataType="entity" ref-id="suite.amiba.group.ref" width="200px" editable/>
            <md-grid-column label="调整金额" field="money" editable/>
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
      var validator = this.$validate(this.model.main, { 'purpose': 'required', 'period': 'required' });
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
          'period': this.$root.configs.period,
          'memo': ''
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.data.adjust.list' } });
    },
    onLineAdd() {
      this.$refs.grid && this.$refs.grid.addDatas({});
    },
    init_period_ref(options) {
      if (this.model.main.purpose && this.model.main.purpose.calendar_id) {
        options.wheres.$calendar = { calendar_id: this.model.main.purpose.calendar_id };
      } else {
        options.wheres.$calendar = { calendar_id: this.$root.configs.calendar.id };
      }
    },
  },
  created() {
    this.model.entity = 'suite.amiba.data.adjust';
    this.model.order = "doc_no";
    this.route = 'amiba/data-adjusts';
  },
};
</script>