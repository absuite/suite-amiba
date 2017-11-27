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
          <md-layout md-flex-xsmall="100" md-flex-small="33" md-flex-medium="25" md-flex-large="20"  md-flex-xlarge="20">
            <md-field class="md-inset">
              <label>期间</label>
              <md-input-ref required md-ref-id="suite.cbo.period.account.ref" v-model="model.period"></md-input-ref>
            </md-field>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>阿米巴排名分析</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>报表</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body class="md-no-scroll">
      <md-layout>
        <md-chart class="myChart" ref="myChart" :options="options"></md-chart>
      </md-layout>
      <md-layout class="flex">
        <md-content class="flex md-query">
          <md-table>
            <md-table-header>
              <md-table-row>
                <md-table-head>阿米巴</md-table-head>
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
    type: 'column',
    className: 'md-chart-default',
  },
  title: {
    text: '',
    className: 'md-chart-title',
  },
  plotOptions: {
    column: {
      dataLabels: {
        enabled: true,
        rotation: -90,
        align: 'right',
        y: 10,
        style: { textOutline: 'null' }
      }
    }
  },
  legend: { enabled: false, symbolRadius: 3 },
  tooltip: {
    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
      '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
    footerFormat: '</table>',
    shared: true,
    useHTML: true
  },
  xAxis: {
    type: 'category',
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
  }]
};
export default {
  data() {
    return {
      options: defaultOpts,
      dataDetail: [],
      model: {
        purpose: this.$root.userConfig.purpose,
        period: this.$root.userConfig.period,
      },
    };
  },
  watch: {
    'model.purpose': function(value) {
      this.loadData();
    },
    'model.period': function(value) {
      this.loadData();
    },
  },
  methods: {
    loadData() {
      var queryCase = { wheres: [] };
      if (!this.model.purpose || !this.model.period) {
        this.dataDetail = [];
        return;
      }
      if (this.model.purpose) {
        queryCase.wheres.push({ name: 'purpose_id', value: this.model.purpose.id });
      }
      if (this.model.period) {
        queryCase.wheres.push({ name: 'period_id', value: this.model.period.id });
      }
      this.$http.post('amiba/reports/group-rank-ans', queryCase).then(response => {
        this.updateOption(response.data.data);
        this.updateTableOptions(response.data.data);
      }, response => {
        this.$toast(response);
      });
    },
    updateOption(data) {
      var datas = [];
      var datas2 = [];
      var datas3 = [];
      this._.each(data, (value, key) => {
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

  },
  mounted() {
    this.loadData();
  },
};
</script>