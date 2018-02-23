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
      <md-part-toolbar-group>
        <md-file-import md-entity="Suite\Amiba\Models\DataInit"  template="/assets/vendor/suite-cbo/files/suite.amiba.data.init.xlsx"></md-file-import>
      </md-part-toolbar-group>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column md-form">
        <md-layout md-gutter md-row>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>单据编号</label>
              <md-input required v-model="model.main.doc_no" />
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="核算目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="币种" required md-ref-id="suite.cbo.currency.ref" v-model="model.main.currency">
            </md-ref-input>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="fetchLineDatas" ref="grid" :row-focused="false" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="阿米巴" field="group" dataType="entity" ref-id="suite.amiba.group.ref" :ref-init="init_group_ref" editable/>
            <md-grid-column label="利润" field="profit" editable/>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref :md-init="init_group_ref" md-ref-id="suite.amiba.group.ref" ref="lineRef" @confirm="lineRefClose"></md-ref>
  </md-part>
</template>
<script>
import model from 'gmf/core/mixins/MdModel/MdModel';
import modelGrid from 'gmf/core/mixins/MdModel/MdModelGrid';
export default {
  mixins: [model, modelGrid],
  computed: {
    canSave() {
      return true; //this.validate(true);
    }
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, {
        'purpose': 'required',
        'period': 'required',
        'currency': 'required'
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
          'period': this.$root.configs.period,
          'currency': this.$root.configs.currency,
          'lines': []
        }
      }
    },
    async afterInitData() {
      this.model.main.doc_no = await this.$root.issueSn('suite.amiba.data.init');
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.data.init.list' } });
    },
    onLineAdd() {
      this.$refs['lineRef'].open();
    },
    lineRefClose(datas) {
      this._.forEach(datas, (v, k) => {
        this.$refs.grid && this.$refs.grid.addDatas({ group: v });
      });
    },
    init_group_ref(options) {
      options.wheres.$leaf = {'is_leaf': '1' };
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
    this.model.entity = 'suite.amiba.data.init';
    this.model.order = "created_at";
    this.route = 'amiba/data-inits';
  },
};
</script>