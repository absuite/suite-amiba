<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group style="width:400px">
        <md-layout md-gutter>
          <md-layout md-flex="50">
            <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex="50">
            <md-ref-input md-label="期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.period"></md-ref-input>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button class="md-primary" @click.native="runAll">
          <span>查询</span>
        </md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button class="md-primary" @click.native="onClear">
          <span>清除数据</span>
        </md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-group>
        <md-fetch :fetch="doFetch"></md-fetch>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body class="no-padding">
      <md-grid :datas="loadDatas" :pagerSize="50" ref="grid" :row-focused="false" :auto-load="true" show-download>
        <md-grid-column label="模型" width="180px" field="model_name">
          <template slot-scope="row">
            <div>{{ row.model_name}}</div>
            <div class="md-caption">{{ row.model_code}}</div>
          </template>
        </md-grid-column>
        <md-grid-column label="期间" width="180px" field="period_name">
          <template slot-scope="row">
            <div>{{ row.period_name}}</div>
            <div class="md-caption">{{ row.period_code}}</div>
          </template>
        </md-grid-column>
        <md-grid-column label="来源阿米巴" width="180px" field="fm_group_name">
          <template slot-scope="row">
            <div>{{ row.fm_group_name}}</div>
            <div class="md-caption">{{ row.fm_group_code}}</div>
          </template>
        </md-grid-column>
        <md-grid-column label="目标阿米巴" width="180px" field="to_group_name">
          <template slot-scope="row">
            <div>{{ row.to_group_name}}</div>
            <div class="md-caption">{{ row.to_group_code}}</div>
          </template>
        </md-grid-column>
        <md-grid-column label="料号" field="item_code" />
        <md-grid-column label="品名" field="item_name" />
        <md-grid-column label="单据日期" field="date" />
        <md-grid-column label="消息" field="msg" width="500px" multiple />
      </md-grid>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
import common from "gmf/core/utils/common";
import _extend from "lodash/extend";
export default {
  name: "AmibaDtiModelingPrice",
  data() {
    return {
      model: {
        purpose: this.$root.configs.purpose,
        period: this.$root.configs.period
      },
      currentQ: "",
      loading: 0
    };
  },
  watch: {
    "model.purpose"() {
      this.$refs.grid.refresh();
    },
    "model.period"() {
      this.$refs.grid.refresh();
    }
  },
  methods: {
    async loadDatas({ pager }) {
      const params = _extend({}, pager, {});
      if (this.model.purpose) {
        params.purpose_id = this.model.purpose.id;
      }
      if (this.model.period) {
        params.period_id = this.model.period.id;
      }
      if (!params.period_id || !params.purpose_id) {
        return [];
      }
      params.q = this.currentQ;
      return await this.$http.get("amiba/dti-modelings/prices", {
        params: params
      });
    },
    doFetch(q) {
      if (this.currentQ != q) {
        this.currentQ = q;
        this.runAll();
      }
      this.currentQ = q;
    },
    runAll() {
      this.$refs.grid.refresh();
    },
    onClear() {
      this.$http.delete("amiba/dti-modelings/prices").then(res => {
        this.runAll();
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
  mounted() {}
};
</script>