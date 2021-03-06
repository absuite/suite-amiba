<template>
  <md-part class="md-full">
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
        <md-ref-input md-label="期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref"
          v-model="model.period"></md-ref-input>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-group>
        <md-field>
          <label>显示单位</label>
          <md-select v-model="display.unit" md-dense>
            <md-option value="0">元</md-option>
            <md-option value="3">千</md-option>
            <md-option value="4">万</md-option>
            <md-option value="8">亿</md-option>
          </md-select>
        </md-field>
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
        <md-table class="layout-fill">
          <md-table-row>
            <md-table-head style="width: 200px;">收支项目</md-table-head>
            <md-table-head md-numeric style="width:180px;">发生额</md-table-head>
            <md-table-head md-numeric style="width: 180px;">结构比率</md-table-head>
            <md-table-head md-numeric style="width: 180px;">年累计</md-table-head>
            <md-table-head md-numeric style="width: 180px;">累计比率</md-table-head>
          </md-table-row>
          <md-table-row v-for="(row, index) in dataDetail" :key="index">
            <md-table-cell>
              <div :class="['md-indent-'+row.indent]">{{row.itemName}}</div>
            </md-table-cell>
            <md-table-cell md-numeric>{{row.month_value|formatDecimal({unit:display.unit})}}</md-table-cell>
            <md-table-cell md-numeric>{{row.month_ratio}}</md-table-cell>
            <md-table-cell md-numeric>{{row.year_value|formatDecimal({unit:display.unit})}}</md-table-cell>
            <md-table-cell md-numeric>{{row.year_ratio}}</md-table-cell>
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
  import formatDecimal from "../filters/formatDecimal";
  import _each from "lodash/each";
  import excelDwnload from "cbo/utils/excelDwnload ";
  export default {
    data() {
      return {
        display: {
          unit: 0,
        },
        model: {
          purpose: this.$root.configs.purpose,
          period: this.$root.configs.period,
          group: null
        },
        groups: [],
        dataDetail: []
      };
    },
    filters: {
      formatDecimal: formatDecimal
    },
    watch: {
      "model.purpose": function (value) {
        this.loadData();
        this.loadGroups();
      },
      "model.period": function (value) {
        this.loadData();
      },
      "model.group": function (value) {
        this.loadData();
      }
    },
    methods: {
      exportData() {
        const cols = [];
        cols.push(new Column({
          field: "itemName",
          label: "收支项目"
        }));
        cols.push(new Column({
          field: "month_value",
          label: "发生额"
        }));
        cols.push(new Column({
          field: "month_ratio",
          label: "结构比率"
        }));
        cols.push(new Column({
          field: "year_value",
          label: "年累计"
        }));
        cols.push(new Column({
          field: "year_ratio",
          label: "累计比率"
        }));

        const datas = this.dataDetail.map(res => {
          return {
            itemName: Array(res.indent).join(" ") + res.itemName,
            month_value: res.month_value,
            month_ratio: res.month_ratio,
            year_value: res.year_value,
            year_ratio: res.year_ratio
          };
        });

        const de = new DataExport(datas, cols);
        de.toExcel("报表-职能式损益表");
      },
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
            purpose_id: this.model.purpose.id
          });
        }
        if (this.model.period) {
          queryCase.wheres.push({
            period_id: this.model.period.id
          });
        }
        if (this.model.group) {
          queryCase.wheres.push({
            group_id: this.model.group.id
          });
        }
        this.$http.post("amiba/reports/statement-function-ans", queryCase).then(
          response => {
            this.updateTableOptions(response.data.data);
          },
          response => {
            this.$toast(response);
          }
        );
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
        this.$http
          .get("amiba/groups/all", {
            params: params
          })
          .then(
            response => {
              this.groups = response.data.data;
              this.model.group = null;
            },
            response => {
              this.$toast(response);
            }
          );
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
            calendar_id: this.model.purpose.calendar_id
          };
        } else {
          options.wheres.$calendar = false;
        }
      }
    },
    created() {},
    mounted() {
      this.loadGroups();
    }
  };
</script>