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
        <md-part-toolbar-crumb>损益横比</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>报表</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body direction="row" class="md-no-scroll">
      <md-part-body-side md-left>
        <md-tree-view :nodes="groups" @focus="focusGroup" @select="selectGroups"></md-tree-view>
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
import common from 'gmf/core/utils/common';

export default {
  data() {
    return {
      dataDetail: [],
      dataCategories: [],
      model: {
        purpose: this.$root.userConfig.purpose,
        period: this.$root.userConfig.period,
        group: []
      },
      groups: [],
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
        for (var i = this.model.group.length - 1; i >= 0; i--) {
          ids.push(this.model.group[i].id);
        }
        queryCase.wheres.push({ name: 'group_id', operator: 'in', value: ids });
      }
      this.$http.post('amiba/reports/statement-compare', queryCase).then(response => {
        this.updateTableOptions(response.data);
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
    updateTableOptions(data) {
      this.dataDetail = data.data;
      this.dataCategories = data.categories;
    },
  },
  created() {},
  mounted() {
    this.loadGroups();
  },
};
</script>