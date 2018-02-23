<template>
  <md-part class="md-full">
    <md-part-toolbar>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-hide-xsmall md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex-xlarge="20">
            <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex-xlarge="20">
            <md-ref-input md-label="期间" required md-ref-id="suite.cbo.period.account.ref" v-model="model.period"></md-ref-input>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body direction="row" class="no-padding no-margin">
      <md-part-body-side md-left>
        <md-tree-view :nodes="groups" :md-selection="false" @focus="focusGroup"></md-tree-view>
      </md-part-body-side>
      <div class="layout layout-fill layout-column md-query">
        <md-table class="flex">
          <md-table-row>
            <md-table-head>目标类型</md-table-head>
            <md-table-head md-numeric>目标值</md-table-head>
            <md-table-head md-numeric>实际值</md-table-head>
            <md-table-head md-numeric>差异</md-table-head>
          </md-table-row>
          <md-table-row v-for="(row, index) in dataDetail" :key="index">
            <md-table-cell>
              <div :class="['md-indent-'+row.indent]">{{row.itemName}}</div>
            </md-table-cell>
            <md-table-cell md-numeric>{{row.plan_value}}</md-table-cell>
            <md-table-cell md-numeric>{{row.month_value}}</md-table-cell>
            <md-table-cell md-numeric>{{row.diff_value}}</md-table-cell>
          </md-table-row>
        </md-table>
      </div>
    </md-part-body>
  </md-part>
</template>
<script>
import common from 'gmf/core/utils/common';

export default {
  data() {
    return {
      model: {
        purpose: this.$root.configs.purpose,
        period: this.$root.configs.period,
        group: null
      },
      groups: [],
      dataDetail: []
    };
  },
  watch: {
    'model.purpose': function(value) {
      this.loadData();
      this.loadGroups();
    },
    'model.period': function(value) {
      this.loadData();
    },
    'model.group': function(value) {
      this.loadData();
    },
  },
  methods: {
    loadData() {
      var queryCase = { wheres: [] };
      if (!this.model.purpose || !this.model.period || !this.model.group) {
        this.dataDetail = [];
        return;
      }
      if (this.model.purpose) {
        queryCase.wheres.push({ 'purpose_id': this.model.purpose.id });
      }
      if (this.model.period) {
        queryCase.wheres.push({ 'period_id' : this.model.period.id });
      }
      if (this.model.group) {
        queryCase.wheres.push({ 'group_id' : this.model.group.id });
      }
      this.$http.post('amiba/reports/statement-purpose', queryCase).then(response => {
        this.updateTableOptions(response.data.data);
      }, response => {
        this.$toast(response);
      });
    },
    focusGroup(group) {
      this.model.group = group;
    },
    loadGroups() {
      var params = {};
      if (this.model.purpose) {
        params.purpose_id = this.model.purpose.id;
      }
      this.groups = [];
      this.$http.get('amiba/groups/all', { params: params }).then(response => {
        this.groups = response.data.data;
        this.model.group = null;
      }, response => {
        this.$toast(response);
      });
    },
    loadPeriodInfo() {
      this.$http.get('amiba/reports/period-info').then(response => {
        this.model.fm_period = response.data.data.yearFirstPeriod;
        this.model.to_period = response.data.data.period;
      });
    },
    updateTableOptions(data) {
      this.dataDetail = [];
      this._.each(data, (value, key) => {
        this.dataDetail.push(value);
      });
    },
  },
  created() {},
  mounted() {
    this.loadGroups();
    this.loadPeriodInfo();
  },
};
</script>