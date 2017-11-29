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
      <md-content class="flex layout-column">
        <md-layout md-gutter>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-ref-input md-label="核算目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-ref-input md-label="期间" @init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
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
    <md-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" ref="lineRef" @confirm="lineRefClose"></md-ref>
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
          'purpose': this.$root.userConfig.purpose,
          'period': this.$root.userConfig.period,
          'currency': this.$root.userConfig.currency,
          'lines': []
        }
      }
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
      options.wheres.leaf = { name: 'is_leaf', value: '1' };
      if (this.model.main.purpose) {
        options.wheres.purpose = { name: 'purpose_id', value: this.model.main.purpose.id };
      } else {
        options.wheres.purpose = false;
      }
    },
    init_period_ref(options) {
      if (this.model.main.purpose && this.model.main.purpose.calendar_id) {
        options.wheres.calendar = { name: 'calendar_id', value: this.model.main.purpose.calendar_id };
      } else {
        options.wheres.calendar = { name: 'calendar_id', value: this.$root.userConfig.calendar.id };
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