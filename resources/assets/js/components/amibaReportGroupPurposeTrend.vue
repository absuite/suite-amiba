<template>
  <md-part class="md-full">
    <md-part-toolbar>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-hide-xsmall md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex-xlarge="20">
            <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xsmall="50" md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex-xlarge="20">
            <md-ref-input md-label="从" required md-ref-id="suite.cbo.period.account.ref" v-model="model.fm_period"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xsmall="50" md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex-xlarge="20">
            <md-ref-input md-label="到" required md-ref-id="suite.cbo.period.account.ref" v-model="model.to_period"></md-ref-input>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body direction="row" class="no-padding no-margin">
      <md-part-body-side md-left>
        <md-tree-view :nodes="groups" :md-selection="false" @focus="focusGroup"></md-tree-view>
      </md-part-body-side>
      <div class="layout layout-fill layout-column">
        <md-layout>
          <md-chart class="myChart" ref="myChart" :options="options"></md-chart>
        </md-layout>
        <md-layout class="flex">
          <md-content class="flex md-query">
            <md-table>
              <md-table-row>
                <md-table-head>期间</md-table-head>
                <md-table-head md-numeric>实际利润</md-table-head>
                <md-table-head md-numeric>目标利润</md-table-head>
              </md-table-row>
              <md-table-row v-for="(row, index) in dataDetail" :key="index">
                <md-table-cell>{{row.name}}</md-table-cell>
                <md-table-cell md-numeric>{{row.this_profit}}</md-table-cell>
                <md-table-cell md-numeric>{{row.plan_profit}}</md-table-cell>
              </md-table-row>
            </md-table>
          </md-content>
        </md-layout>
      </div>
    </md-part-body>
  </md-part>
</template>
<style scoped>
.myChart {
  min-height: 300px;
}
</style>
<script>
import common from 'gmf/core/utils/common';
var defaultOpts = {
  chart: {
    type: 'line',
    className: 'md-chart-default',
  },
  title: {
    text: '', //本年度累计销售额
    className: 'md-chart-title',
  },
  plotOptions: {
    series: {
      grouping: false
    }
  },
  legend: { enabled: true, symbolRadius: 3 },
  tooltip: { shared: true },
  xAxis: {
    categories: [],
    crosshair: true,
    className: 'md-chart-xaxis',
  },
  yAxis: {
    title: { text: '' },
    className: 'md-chart-yaxis',
    gridLineDashStyle: 'LongDash'
  },
  series: [{
    name: '实际',
    data: []
  }, {
    name: '目标',
    data: []
  }]
};
export default {
  data() {
    return {
      options: defaultOpts,
      categories: [],
      dataDetail: [],
      groups: [],
      model: {
        purpose: this.$root.configs.purpose,
        fm_period: null,
        to_period: this.$root.configs.period,
        group: null
      },
    };
  },
  watch: {
    'model.purpose': function(value) {
      this.loadData();
      this.loadGroups();
    },
    'model.fm_period': function(value) {
      this.loadData();
    },
    'model.to_period': function(value) {
      this.loadData();
    },
    'model.group': function(value) {
      this.loadData();
    },
  },
  methods: {
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
    loadData() {
      var queryCase = { wheres: [] };
      if (!this.model.purpose || !this.model.fm_period || !this.model.to_period || !this.model.group) {
        this.dataDetail = [];
        return;
      }
      if (this.model.purpose) {
        queryCase.wheres.push({ name: 'purpose_id', value: this.model.purpose.id });
      }
      if (this.model.fm_period) {
        queryCase.wheres.push({ name: 'fm_period', operator: 'greater_than_equal', value: this.model.fm_period.code });
      }
      if (this.model.to_period) {
        queryCase.wheres.push({ name: 'to_period', operator: 'less_than_equal', value: this.model.to_period.code });
      }
      if (this.model.group) {
        queryCase.wheres.push({ name: 'group_id', value: this.model.group.id });
      }
      this.$http.post('amiba/reports/group-purpose-trend', queryCase).then(response => {
        this.categories = response.data.categories;
        this.updateOption(response.data.data);
        this.updateTableOptions(response.data.data);
      }, response => {
        this.$toast(response);
      });
    },
    updateOption(data) {
      var datas = [];
      var datas2 = [];
      this._.each(data, (value, key) => {
        datas.push({
          name: value.name,
          y: value.this_profit
        });
        datas2.push({
          name: value.name,
          y: value.plan_profit
        });
      });
      var opts = {
        xAxis: { categories: this.categories },
        series: [{
            name: '实际',
            data: datas
          },
          {
            name: '目标',
            data: datas2
          }
        ]
      };
      this.$refs.myChart.mergeOption(opts);
    },
    updateTableOptions(data) {
      data = this._.reverse(data);
      this.dataDetail = [];
      this._.each(data, (value, key) => {
        this.dataDetail.push({
          name: value.name,
          this_profit: common.formatDecimal(value.this_profit),
          plan_profit: common.formatDecimal(value.plan_profit)
        });
      });
    },
  },
  created() {
    this.loadGroups();
    this.loadPeriodInfo();
  },
  mounted() {},
};
</script>