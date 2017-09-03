<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-hide-xsmall md-flex-small="33" md-flex-medium="25" md-flex-large="20">
            <md-input-container class="md-inset">
              <label>目的</label>
              <md-input-ref required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="33" md-flex-medium="25" md-flex-large="20">
            <md-input-container class="md-inset">
              <label>期间</label>
              <md-input-ref required md-ref-id="suite.cbo.period.account.ref" v-model="model.period"></md-input-ref>
            </md-input-container>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>考核结果</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>查询</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-query @init="init_model_query" @select="select" ref="list" md-query-id="suite.amiba.result.account.list"></md-query>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
export default {
  data() {
    return {
      selectRows: [],
      loading: 0,
      model: { period: this.$root.userConfig.period, purpose: this.$root.userConfig.purpose }
    };
  },
  watch: {
    'model.purpose': function(value) {
      this.query();
    },
    'model.period': function(value) {
      this.query();
    },
  },
  methods: {
    select(items) {
      this.selectRows = items;
    },
    query() {
      this.$refs.list.pagination(1);
    },
    init_model_query(options) {
      options.wheres.purpose = false;
      options.wheres.period = false;

      if (this.model.purpose) {
        options.wheres.purpose = { name: 'purpose.id', value: this.model.purpose.id };
      }
      if (this.model.period) {
        options.wheres.period = { name: 'period.id', value: this.model.period.id };
      }


      options.orders[0] = { name: 'purpose_id' };
      options.orders[1] = { name: 'period_id' };
      options.orders[2] = { name: 'group_id' };
    },
  }
};
</script>