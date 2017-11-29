<!--
单个阿米巴，多个期间的分析（包括，成本，收入、利润）
-->
<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-hide-xsmall md-flex-small="33" md-flex-medium="25" md-flex-large="20"  md-flex-xlarge="20">
            <md-field class="md-inset">
              <label>目的</label>
              <md-input-ref required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-input-ref>
            </md-field>
          </md-layout>
          <md-layout md-flex-xsmall="50" md-flex-small="33" md-flex-medium="25" md-flex-large="20"  md-flex-xlarge="20">
            <md-field class="md-inset">
              <label>从</label>
              <md-input-ref required md-ref-id="suite.cbo.period.account.ref" v-model="model.fm_period"></md-input-ref>
            </md-field>
          </md-layout>
          <md-layout md-flex-xsmall="50" md-flex-small="33" md-flex-medium="25" md-flex-large="20"  md-flex-xlarge="20">
            <md-field class="md-inset">
              <label>到</label>
              <md-input-ref required md-ref-id="suite.cbo.period.account.ref" v-model="model.to_period"></md-input-ref>
            </md-field>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body direction="row" class="md-no-scroll">
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
              <md-table-header>
                <md-table-row>
                  <md-table-head>期间</md-table-head>
                  <md-table-head md-numeric>实际收入</md-table-head>
                  <md-table-head md-numeric>实际支出</md-table-head>
                  <md-table-head md-numeric>实际利润</md-table-head>
                </md-table-row>
              </md-table-header>
              <md-table-body>
                <md-table-row v-for="(row, index) in dataDetail" :key="index">
                  <md-table-cell>{{row.name}}</md-table-cell>
                  <md-table-cell md-numeric>{{row.this_income}}</md-table-cell>
                  <md-table-cell md-numeric>{{row.this_cost}}</md-table-cell>
                  <md-table-cell md-numeric>{{row.this_profit}}</md-table-cell>
                </md-table-row>
              </md-table-body>
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
import queryCase from 'gmf/core/mixins/MdQuery/MdQueryCase';
var defaultOpts = {
  chart: {
    type: 'line',
    className: 'md-chart-default',
  },
  title: {
    text: '趋势分析',
    className: 'md-chart-title',
  },
  legend: { enabled: true, symbolRadius: 3 },
  tooltip: {},
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
    name: '利润',
    data: []
  }, {
    name: '收入',
    data: []
  }, {
    name: '成本',
    data: []
  }]
};
export default {
  data() {
    return {
      options: defaultOpts,
      dataDetail: [],
      groups: [],
      model: {
        purpose: this.$root.userConfig.purpose,
        fm_period: null,
        to_period: this.$root.userConfig.period,
        group: null
      },
    };
  },
  mixins: [queryCase],
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
  methods: {
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
      this.$http.post('amiba/reports/group-trend-ans', queryCase).then(response => {
        this.updateOption(response.data.data);
        this.updateTableOptions(response.data.data);
      }, response => {
        this.$toast(response);
      });
    },
    focusGroup(group) {
      this.model.group = group;
      this.loadData();
    },
    loadGroups() {
      var params={};
      if(this.model.purpose){
        params.purpose_id=this.model.purpose.id;
      }
      this.groups=[];
      this.$http.get('amiba/groups/all', { params: params }).then(response => {
        this.groups = response.data.data;
        this.model.group=null;
      }, response => {
        this.$toast(response);
      });
    },
    loadPeriodInfo(){
      this.$http.get('amiba/reports/period-info').then(response => {
        this.model.fm_period = response.data.data.yearFirstPeriod;
        this.model.to_period = response.data.data.period;
      });
    },
    updateOption(data) {
      var categories = [];
      var datas = [];
      var datas2 = [];
      var datas3 = [];
      this._.each(data, (value, key) => {
        categories.push(value.name);
        datas.push({
          name: value.name,
          y: value.this_profit
        });
        datas2.push({
          name: value.name,
          y: value.this_income
        });
        datas3.push({
          name: value.name,
          y: value.this_cost
        });
      });

      var opts = {
        title: {
          text: this.model.group.name + '-趋势分析',
        },
        xAxis: {
          categories: categories,
        },
        series: [{
            name: '利润',
            data: datas
          },
          {
            name: '收入',
            data: datas2
          }, {
            name: '成本',
            data: datas3
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
          this_cost: common.formatDecimal(value.this_cost),
          this_profit: common.formatDecimal(value.this_profit),
          this_income: common.formatDecimal(value.this_income)
        });
      });
    },
  },
  created() {
    this.loadGroups();
    this.loadPeriodInfo();
    this.queryId = 'suite.amiba.group.periods.ans.report';
  },
  mounted() {

  },
};
</script>