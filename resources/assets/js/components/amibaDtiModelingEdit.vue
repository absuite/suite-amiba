<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group style="width:400px">
        <md-layout md-gutter>
          <md-layout md-flex="50">
            <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex="50">
            <md-ref-input md-label="期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref"
              v-model="model.period"></md-ref-input>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button class="md-primary" @click.native="runAll" :disabled="is_running>0">
          <md-icon>play_circle_filled</md-icon>
          <span>开始建模</span>
        </md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button @click.native="refresh">
          <span>刷新</span>
        </md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-group>
        <md-button @click.native="priceError">
          <span>单价异常</span>
        </md-button>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body class="no-padding">
      <md-grid :datas="loadDatas" :pagerSize="50" ref="grid" :row-focused="false" :auto-load="true">
        <md-grid-column label="模型" width="180px">
          <template slot-scope="row">
            <div>{{ row.name}}</div>
            <div class="md-caption">{{ row.code}}</div>
          </template>
        </md-grid-column>
        <md-grid-column label="阿米巴" width="180px">
          <template slot-scope="row">
            <div>{{ row.group.name}}</div>
            <div class="md-caption">{{ row.group.code}}</div>
          </template>
        </md-grid-column>
        <md-grid-column label="开始时间" field="start_time" />
        <md-grid-column label="结束时间" field="end_time" />
        <md-grid-column label="状态">
          <template slot-scope="row">
            <span v-if="row.status==0">等待执行</span>
            <span v-else-if="row.status==1">正在执行</span>
            <span v-if="row.status==2&&row.succeed==1">执行成功</span>
            <span v-if="row.status==2&&row.succeed==0">执行失败</span>
          </template>
        </md-grid-column>
        <md-grid-column label="消息" field="msg" width="500px" multiple />
      </md-grid>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
  import common from 'gmf/core/utils/common';
  import _extend from 'lodash/extend'
  export default {
    name: "AmibaDtiModelingEdit",
    data() {
      return {
        model: {
          purpose: this.$root.configs.purpose,
          period: this.$root.configs.period
        },
        loading: 0,
        is_running: 0
      };
    },
    watch: {
      "model.purpose"() {
        this.$refs.grid.refresh()
      },
      "model.period"() {
        this.$refs.grid.refresh()
      }
    },
    methods: {
      refresh() {
        this.$refs.grid.refresh()
      },
      priceError() {
        this.$goModule("AmibaDtiModelingPrice")
      },
      async loadDatas({
        pager
      }) {
        const params = _extend({}, pager, {});
        if (this.model.purpose) {
          params.purpose_id = this.model.purpose.id;
        }
        if (this.model.period) {
          params.period_id = this.model.period.id
        }
        if (!params.period_id || !params.purpose_id) {
          return []
        }
        return await this.$http.get('amiba/dti-modelings', {
          params: params
        })
      },
      runAll() {
        const datas = {
          purpose_id: this.model.purpose.id,
          period_id: this.model.period.id
        };
        this.$http.post('amiba/dti-modelings/cache', datas);
        const rows = this.$refs.grid.getSelectedDatas(true);
        rows && rows.forEach(item => {
          this.runItem(item);
        });
      },
      runItem(item) {
        if (!item) return;
        item.status = 1;
        const datas = {
          model_id: item.id,
          period_id: this.model.period.id
        };
        this.is_running++;
        item.start_time = common.now();
        item.end_time = '';
        this.$http.post('amiba/dti-modelings', datas).then(response => {
          this.$toast(item.name + '成功提交请求!');
          item.msg = '成功提交请求';
          this.is_running--;
        }, err => {
          this.is_running--;
          this.$toast(err);
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

    },
  };
</script>