<template>
  <md-part class="md-full">
    <md-part-toolbar>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-flex-xs="100" md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex="20">
            <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="33" md-flex-md="25" md-flex-lg="20" md-flex="20">
            <md-ref-input md-label="期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref"
              v-model="model.period"></md-ref-input>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body direction="row" class="no-padding no-margin">
      <md-part-body-side md-left>
        <md-tree-view :nodes="groups" :md-selection="false" @focus="focusGroup"></md-tree-view>
      </md-part-body-side>
      <div class="layout flex layout-column md-query">
        <md-table class="layout-fill">
          <md-table-row>
            <md-table-head>收支项目</md-table-head>
            <md-table-head md-numeric>发生额</md-table-head>
            <md-table-head md-numeric>结构比率</md-table-head>
            <md-table-head md-numeric>年累计</md-table-head>
            <md-table-head md-numeric>累计比率</md-table-head>
          </md-table-row>
          <md-table-row v-for="(row, index) in dataDetail" :key="index">
            <md-table-cell>
              <div :class="['md-indent-'+row.indent]">{{row.itemName}}</div>
            </md-table-cell>
            <md-table-cell md-numeric>{{row.month_value|mdThousand}}</md-table-cell>
            <md-table-cell md-numeric>{{row.month_ratio}}</md-table-cell>
            <md-table-cell md-numeric>{{row.year_value|mdThousand}}</md-table-cell>
            <md-table-cell md-numeric>{{row.year_ratio}}</md-table-cell>
          </md-table-row>
        </md-table>
      </div>
    </md-part-body>
  </md-part>
</template>
<script>
  import common from 'gmf/core/utils/common';
  import mdThousand from 'gmf/filters/mdThousand';
  import _each from 'lodash/each'
  export default {
    data() {
      return {
        model: {
          purpose: this.$root.configs.purpose,
          period: this.$root.configs.period,
          group: null
        },
        groups: [],
        dataDetail: [],

      };
    },
    filters: {
      mdThousand: mdThousand
    },
    watch: {
      'model.purpose': function (value) {
        this.loadData();
        this.loadGroups();
      },
      'model.period': function (value) {
        this.loadData();
      },
      'model.group': function (value) {
        this.loadData();
      },
    },
    methods: {
      loadData() {
        var queryCase = {
          wheres: []
        };
        if (!this.model.purpose || !this.model.period || !this.model.group) {
          this.dataDetail = [];
          return;
        }
        if (this.model.purpose) {
          queryCase.wheres.push({
            'purpose_id': this.model.purpose.id
          });
        }
        if (this.model.period) {
          queryCase.wheres.push({
            'period_id': this.model.period.id
          });
        }
        if (this.model.group) {
          queryCase.wheres.push({
            'group_id': this.model.group.id
          });
        }
        this.$http.post('amiba/reports/statement-function-ans', queryCase).then(response => {
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
        this.$http.get('amiba/groups/all', {
          params: params
        }).then(response => {
          this.groups = response.data.data;
          this.model.group = null;
        }, response => {
          this.$toast(response);
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
          options.wheres.$calendar = {
            'calendar_id': this.model.purpose.calendar_id
          };
        } else {
          options.wheres.$calendar = false;
        }
      },
    },
    created() {

    },
    mounted() {
      this.loadGroups();
    },
  };
</script>