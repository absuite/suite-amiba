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
          <md-layout md-flex-xsmall="50" md-flex-small="33" md-flex-medium="25" md-flex-large="20">
            <md-input-container class="md-inset">
              <label>从</label>
              <md-input-ref required md-ref-id="suite.cbo.period.account.ref" v-model="model.fm_period"></md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="50" md-flex-small="33" md-flex-medium="25" md-flex-large="20">
            <md-input-container class="md-inset">
              <label>到</label>
              <md-input-ref required md-ref-id="suite.cbo.period.account.ref" v-model="model.to_period"></md-input-ref>
            </md-input-container>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>损益趋势分析</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>报表</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body direction="row" class="md-no-scroll">
      <md-part-body-side md-left>
        <md-tree-view :nodes="groups" :md-selection="false" @focus="focusGroup"></md-tree-view>
      </md-part-body-side>
      <div class="layout layout-fill layout-column md-query">
        <md-table class="flex md-header-multiple">
          <md-table-header>
            <md-table-row>
              <md-table-head style="min-width:2rem" rowspan="2">收支项目</md-table-head>
              <template v-for="g in dataCategories">
                <md-table-head colspan="3">{{g}}</md-table-head>
              </template>
            </md-table-row>
            <md-table-row>
              <template v-for="g in dataCategories">
                <md-table-head style="min-width:1rem">发生额</md-table-head>
                <md-table-head style="min-width:1rem">结构比率</md-table-head>
                <md-table-head style="min-width:1rem">年累计</md-table-head>
              </template>
            </md-table-row>
          </md-table-header>
          <md-table-body>
            <md-table-row v-for="(row, index) in dataDetail" :key="index">
              <md-table-cell>
                <div :class="['md-indent-'+row.indent]">{{row.itemName}}</div>
              </md-table-cell>
              <template v-for="cItem in row.categories">
                <md-table-cell md-numeric>{{cItem.money_month}}</md-table-cell>
                <md-table-cell md-numeric>{{cItem.money_month_ratio}}</md-table-cell>
                <md-table-cell md-numeric>{{cItem.money_year}}</md-table-cell>
              </template>
            </md-table-row>
          </md-table-body>
        </md-table>
      </div>
    </md-part-body>
  </md-part>
</template>
<script>
import common from '../../gmf-sys/core/utils/common';

export default {
  data() {
    return {
      model: {
        purpose: this.$root.userConfig.purpose,
        fm_period: null,
        to_period: this.$root.userConfig.period,
        group: null
      },
      dataDetail: [],
      dataCategories: [],
      groups: []
    };
  },
  watch: {
    'model.purpose': function(value) {
      this.loadData();
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
        this.dataCategories = [];
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
      this.$http.post('amiba/reports/statement-trend', queryCase).then(response => {
        this.updateTableOptions(response.data);
      }, response => {
        console.log(response);
      });
    },
    focusGroup(group) {
      this.model.group = group;
    },
    loadGroups() {
      this.$http.get('amiba/groups/all', { params: {} }).then(response => {
        this.groups = response.data.data;
      }, response => {
        console.log(response);
      });
      this.$http.get('amiba/reports/period-info').then(response => {
        this.model.fm_period = response.data.data.yearFirstPeriod;
        this.model.to_period = response.data.data.period;
      });
    },
    updateTableOptions(data) {
      this.dataDetail = data.data;
      this.dataCategories = data.categories;
    },
  },
  created() {
    this.loadGroups();
  },
  mounted() {

  },
};
</script>