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
    </md-part-toolbar>
    <md-part-body direction="row" class="md-no-scroll">
      <md-part-body-side md-left>
        <md-tree-view :nodes="groups" @focus="focusGroup" @select="selectGroups"></md-tree-view>
      </md-part-body-side>
      <div class="layout  layout-fill layout-column">
        <md-layout>
          <md-chart class="myChart" ref="myChart" :options="options"></md-chart>
        </md-layout>
        <md-layout class="flex">
          <md-content class="flex md-query">
            <md-table>
              <md-table-header>
                <md-table-row>
                  <md-table-head>阿米巴</md-table-head>
                  <md-table-head md-numeric>实际利润</md-table-head>
                  <md-table-head md-numeric>目标利润</md-table-head>
                </md-table-row>
              </md-table-header>
              <md-table-body>
                <md-table-row v-for="(row, index) in dataDetail" :key="index">
                  <md-table-cell>{{row.name}}</md-table-cell>
                  <md-table-cell md-numeric>{{row.this_profit}}</md-table-cell>
                  <md-table-cell md-numeric>{{row.plan_profit}}</md-table-cell>
                </md-table-row>
              </md-table-body>
            </md-table>
          </md-content>
        </md-layout>
      </div>
    </md-part-body>
    </div>
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
    text: '', //本年度累计销售额
    className: 'md-chart-title',
  },
  plotOptions: {
    column: {
      grouping: false
    }
  },
  legend: { enabled: true, symbolRadius: 3 },
  tooltip: { shared: true },
  xAxis: {
    type: 'category',
    crosshair: true,
    className: 'md-chart-xaxis',
  },
  yAxis: [{
    className: 'md-chart-yaxis',
    title: { text: '实际值' }
  }, {
    className: 'md-chart-yaxis',
    title: { text: '目标值' },
    opposite: true
  }],
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
      dataDetail: [],
      model: {
        purpose: this.$root.userConfig.purpose,
        period: this.$root.userConfig.period,
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
        queryCase.wheres.push({ name: 'purpose_id', value: this.model.purpose.id });
      }
      if (this.model.period) {
        queryCase.wheres.push({ name: 'period_id', value: this.model.period.id });
      }
      if (this.model.group) {
        var ids = [];
        for (var i = 0; i < this.model.group.length; i++) {
          ids.push(this.model.group[i].id);
        }
        queryCase.wheres.push({ name: 'group_id', operator: 'in', value: ids });
      }
      this.$http.post('amiba/reports/group-purpose-compare', queryCase).then(response => {
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
      var params={};
      if(this.model.purpose){
        params.purpose_id=this.model.purpose.id;
      }
      this.groups=[];
      this.$http.get('amiba/groups/all', { params: params }).then(response => {
        this.groups = response.data.data;
        this.model.group=[];
      }, response => {
        this.$toast(response);
      });
    },
    updateOption(data) {
      var datas = [];
      var datas2 = [];
      this._.each(data, (value, key) => {
        key < 10 && datas.push({
          name: value.name,
          y: value.this_profit
        });
        key < 10 && datas2.push({
          name: value.name,
          y: value.plan_profit
        });
      });
      var opts = {
        series: [{
            name: '实际',
            pointPadding: 0.3,
            pointPlacement: -0.2,
            data: datas
          },
          {
            name: '目标',
            pointPadding: 0.4,
            pointPlacement: -0.2,
            data: datas2
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
          this_profit: common.formatDecimal(value.this_profit),
          plan_profit: common.formatDecimal(value.plan_profit)
        });
      });
    },
  },
  created() {
    this.loadGroups();
  },
  mounted() {

  },
};
</script>