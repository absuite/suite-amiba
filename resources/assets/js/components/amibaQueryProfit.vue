<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-hide-xsmall md-flex-small="33" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="33" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-ref-input md-label="期间" required md-ref-id="suite.cbo.period.account.ref" v-model="model.period"></md-ref-input>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body class="no-padding">
      <md-query @init="init_model_query" @select="select" ref="list" md-query-id="suite.amiba.result.profit.list"></md-query>
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