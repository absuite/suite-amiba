<template>
  <md-part class="md-full">
    <md-part-toolbar>
      <md-part-toolbar-group class="layout-row">
        <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
        <md-ref-input md-label="从" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.fm_period"></md-ref-input>
        <md-ref-input md-label="到" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.to_period"></md-ref-input>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-group>
        <md-button @click.native="exportData" class="md-primary">
          <md-icon>cloud_download</md-icon><span>导出</span>
        </md-button>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body direction="row" class="no-padding no-margin">
      <md-part-body-side md-left>
        <md-tree-view :nodes="groups" :md-selection="false" @focus="focusGroup"></md-tree-view>
      </md-part-body-side>
      <div class="layout flex layout-column md-query">
        <md-table class="layout-fill md-header-multiple">
          <md-table-row>
            <md-table-head style="min-width: 200px;">收支项目</md-table-head>
            <template v-for="(g,k) in dataCategories">
              <md-table-head :key="k" style="width:180px;">{{g}}</md-table-head>
            </template>
          </md-table-row>
          <md-table-row v-for="(row, index) in dataDetail" :key="index">
            <md-table-cell>
              <div :class="['md-indent-'+row.indent]">{{row.itemName}}</div>
            </md-table-cell>
            <template v-for="(g,rk) in dataCategories">
              <md-table-cell md-numeric :key="rk">{{row["money_m_"+g]|mdThousand}}</md-table-cell>
            </template>
          </md-table-row>
        </md-table>
      </div>
    </md-part-body>
  </md-part>
</template>
<script>
  import Column from "gmf/components/MdGrid/classes/Column";
  import DataExport from "gmf/components/MdGrid/classes/DataExport";

  import common from "gmf/core/utils/common";
  import mdThousand from "gmf/filters/mdThousand";
  import _each from "lodash/each";
  import excelDwnload from "cbo/utils/excelDwnload ";
  export default {
    data() {
      return {
        model: {
          purpose: this.$root.configs.purpose,
          fm_period: null,
          to_period: this.$root.configs.period,
          group: null
        },
        dataDetail: [],
        dataCategories: [],
        groups: []
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
      'model.fm_period': function (value) {
        this.loadData();
      },
      'model.to_period': function (value) {
        this.loadData();
      },
      'model.group': function (value) {
        this.loadData();
      },
    },
    methods: {
      exportData() {
        const cols = [];
        cols.push(new Column({
          field: "itemName",
          label: "收支项目"
        }));

        this.dataCategories.forEach(function (v) {
          cols.push(new Column({
            field: "money_m_" + v,
            label: v
          }));
        });

        const datas = this.dataDetail.map(res => {
          var item = {
            itemName: Array(res.indent).join(" ") + res.itemName,
            month_value: res.month_value,
            month_ratio: res.month_ratio,
            year_value: res.year_value,
            year_ratio: res.year_ratio
          };
          this.dataCategories.forEach(function (v) {
            item["money_m_" + v] = res["money_m_" + v];
          });
          return item
        });

        const de = new DataExport(datas, cols);
        de.toExcel("报表-职能式损益表");
      },
      loadData() {
        var queryCase = {
          wheres: []
        };
        if (!this.model.purpose || !this.model.fm_period || !this.model.to_period || !this.model.group) {
          this.dataDetail = [];
          this.dataCategories = [];
          return;
        }
        if (this.model.purpose) {
          queryCase.wheres.push({
            'purpose_id': this.model.purpose.id
          });
        }
        if (this.model.fm_period) {
          queryCase.wheres.push({
            'gte': {
              'fm_period': this.model.fm_period.code
            }
          });
        }
        if (this.model.to_period) {
          queryCase.wheres.push({
            'lte': {
              'to_period': this.model.to_period.code
            }
          });
        }
        if (this.model.group) {
          queryCase.wheres.push({
            'group_id': this.model.group.id
          });
        }
        this.$http.post('amiba/reports/statement-trend', queryCase).then(response => {
          this.updateTableOptions(response.data);
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
      loadPeriodInfo() {
        this.$http.get('amiba/reports/period-info').then(response => {
          this.model.fm_period = response.data.data.yearFirstPeriod;
          this.model.to_period = response.data.data.period;
        });
      },
      updateTableOptions(data) {
        this.dataDetail = data.data;
        this.dataCategories = data.categories;
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
      this.loadGroups();
      this.loadPeriodInfo();
    },
    mounted() {

    },
  };
</script>
<style lang="scss" scoped>
  .md-query {
    overflow: hidden;
  }
</style>