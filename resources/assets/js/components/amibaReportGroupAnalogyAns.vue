<template>
  <md-part class="md-full">
    <md-part-toolbar>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-flex-xs="100" md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex="20">
            <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex="20">
            <md-ref-input md-label="从" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.fm_period"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex="20">
            <md-ref-input md-label="到" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.to_period"></md-ref-input>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body direction="row" class="no-padding no-margin">
      <md-part-body-side md-left>
        <md-tree-view :nodes="groups" @focus="focusGroup" @select="selectGroups"></md-tree-view>
      </md-part-body-side>
      <div class="layout flex layout-column">
        <md-layout>
          <md-chart class="myChart" ref="myChart" :options="options"></md-chart>
        </md-layout>
        <md-layout class="flex">
          <md-content class="flex md-query">
            <md-table>
              <md-table-row>
                <md-table-head>阿米巴</md-table-head>
                <md-table-head md-numeric v-for="(cell, cind) in categories" :key="cind">{{cell}}</md-table-head>
              </md-table-row>
              <md-table-row v-for="(row, index) in dataDetail" :key="index">
                <md-table-cell>{{row.name}}</md-table-cell>
                <md-table-cell md-numeric v-for="(cell, cind) in categories" :key="cind">{{row.profit[cind] }}</md-table-cell>
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
import _each from 'lodash/each'
import common from 'gmf/core/utils/common';
var defaultOpts = {
  chart: {
    type: 'area',
    className: 'md-chart-default',
  },
  title: {
    text: '', //本年度累计销售额
    className: 'md-chart-title',
  },
  legend: { enabled: true, symbolRadius: 3 },
  tooltip: { split: true },
  xAxis: {
    categories: [],
    type: 'category',
    crosshair: true,
    className: 'md-chart-xaxis',
  },
  yAxis: {
    title: { text: '' },
    className: 'md-chart-yaxis',
    gridLineDashStyle: 'LongDash'
  },
  plotOptions: {
    area: {
      stacking: 'normal',
      lineColor: '#666666',
      lineWidth: 1,
      marker: {
        lineWidth: 1,
        lineColor: '#666666'
      }
    }
  },
  series: []
};
export default {
  data() {
    return {
      options: defaultOpts,
      categories: [],
      dataDetail: [],
      model: {
        purpose: this.$root.configs.purpose,
        fm_period: null,
        to_period: this.$root.configs.period,
        group: []
      },
      groups: []
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
    loadData() {
      var queryCase = { wheres: [] };
      if (!this.model.purpose || !this.model.fm_period || !this.model.to_period || !this.model.group) {
        this.dataDetail = [];
        return;
      }
      if (this.model.purpose) {
        queryCase.wheres.push({ 'purpose_id': this.model.purpose.id });
      }
      if (this.model.fm_period) {
        queryCase.wheres.push({ 'gte':{'fm_period': this.model.fm_period.code }});
      }
      if (this.model.to_period) {
        queryCase.wheres.push({ 'lte':{'to_period': this.model.to_period.code }});
      }
      if (this.model.group) {
        var ids = [];
        for (var i = 0; i < this.model.group.length; i++) {
          ids.push(this.model.group[i].id);
        }
        queryCase.wheres.push({ 'in':{'group_id': ids }});
      }
      this.$http.post('amiba/reports/group-analogy-ans', queryCase).then(response => {
        this.categories = response.data.categories;
        this.updateOption(response.data.data);
        this.updateTableOptions(response.data.data);
      }, response => {
        this.$toast(response);
      });
    },
    selectGroups(nodes) {
      this.model.group = nodes;
    },
    focusGroup(node) {},
    loadGroups() {
      var params = {};
      if (this.model.purpose) {
        params.purpose_id = this.model.purpose.id;
      }
      this.groups = [];
      this.$http.get('amiba/groups/all', { params: params }).then(response => {
        this.groups = response.data.data;
        this.model.group = [];
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
    updateOption(data) {
      var series = [];
      _each(data, (value, key) => {
        var item = {
          name: value.name,
          data: value.profit
        };
        series.push(item);
      });
      this.$refs.myChart.removeSeries();
      _each(series, (value, key) => {
        this.$refs.myChart.addSeries(value);
      });
    },
    updateTableOptions(data) {
      this.dataDetail = [];
      _each(data, (value, key) => {
        this.dataDetail.push(value);
      });
    },
    init_period_ref(options) {
      if (this.model.purpose && this.model.purpose.calendar_id) {
        options.wheres.$calendar = { 'calendar_id': this.model.purpose.calendar_id };
      } else {
        options.wheres.$calendar =false;
      }
    },
  },
  created() {},
  mounted() {
    this.loadGroups();
    this.loadPeriodInfo();
  },
};
</script>