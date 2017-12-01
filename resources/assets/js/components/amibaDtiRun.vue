<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-field class="md-inset">
          <label>日期</label>
          <md-date required v-model="model.date"></md-date>
        </md-field>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button class="md-primary md-raised" @click.native="runAll" :disabled="is_running>0">
          <md-icon>play_circle_filled</md-icon>执行</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body class="no-padding">
      <md-grid :datas="loadDatas" :pagerSize="50" ref="grid" :row-focused="false" :auto-load="true">
        <md-grid-column label="分类" field="category" dataType="entity" />
        <md-grid-column label="名称" field="name" />
        <md-grid-column label="开始时间" field="begin_date" />
        <md-grid-column label="结束时间" field="begin_date" />
        <md-grid-column label="状态">
          <template slot-scope="row">
            {{ row.is_running?'正在执行':'等待中'}}
          </template>
        </md-grid-column>
        <md-grid-column label="消息" field="msg" width="500px" multiple/>
      </md-grid>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
import common from 'gmf/core/utils/common';
export default {
  data() {
    return {
      model: {
        date: this.$root.userConfig.date,
      },
      loading: 0,
      is_running: 0
    };
  },
  methods: {
    async loadDatas({ pager }) {
      const params = this._.extend({}, pager, { date: this.model.date });
      return await this.$http.get('sys/dtis', { params: params })
    },
    runAll() {
      const rows = this.$refs.grid.getSelectedDatas(true);
      rows && rows.forEach(item => {
        this.runItem(item);
      });
    },
    runItem(item) {
      if (!item) return;
      item.is_running = true;
      const datas = {
        date: this.model.date,
        dtis: item.id
      };
      this.is_running++;
      item.begin_date = common.now();
      item.end_date = '';
      this.$http.post('amiba/dtis/run', datas).then(response => {
        this.is_running--;
        item.end_date = common.now();
        this.$toast(item.name + '执行成功!');
        item.msg = '执行成功';
        item.is_running = false
      }, response => {
        this.is_running--;
        item.end_date = common.now();

        if (response && response.response && response.response.data) {
          item.msg = response.response.data.msg;
        } else {
          item.msg = response.message;
        }
        this.$toast(response);
        item.is_running = false
      });
    },
  },
  created() {

  },
  mounted() {

  },
};
</script>