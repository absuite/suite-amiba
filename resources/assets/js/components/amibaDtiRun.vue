<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-input-container class="md-inset">
          <label>日期</label>
          <md-date required v-model="model.date"></md-date>
        </md-input-container>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button class="md-primary md-raised" @click.native="runAll" :disabled="is_running>0">
          <md-icon>play_circle_filled</md-icon>执行</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>接口</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>执行</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-table-card class="flex md-query">
        <md-table @select="onTableSelect" class="flex">
          <md-table-header>
            <md-table-row>
              <md-table-head>分类</md-table-head>
              <md-table-head>名称</md-table-head>
              <md-table-head>开始时间</md-table-head>
              <md-table-head>结束时间</md-table-head>
              <md-table-head>状态</md-table-head>
              <md-table-head>消息</md-table-head>
            </md-table-row>
          </md-table-header>
          <md-table-body>
            <md-table-row v-for="item in datas" :key="item.id" :md-item="item" :md-selection="true">
              <md-table-cell>{{ item.category.name }}</md-table-cell>
              <md-table-cell>{{ item.name}}</md-table-cell>
              <md-table-cell>{{ item.begin_date}}</md-table-cell>
              <md-table-cell>{{ item.end_date}}</md-table-cell>
              <md-table-cell>{{ item.is_running?'正在执行':'等待中'}}</md-table-cell>
              <md-table-cell>{{ item.msg}}</md-table-cell>
            </md-table-row>
          </md-table-body>
        </md-table>
        <md-table-tool>
          <span class="flex"></span>
        </md-table-tool>
      </md-table-card>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
import common from '../../gmf-sys/core/utils/common';
export default {
  data() {
    return {
      model: {
        date: this.$root.userConfig.date,
      },
      loading: 0,
      is_running: 0,
      datas: [],
      selectItems: []
    };
  },
  methods: {
    loadDatas() {
      this.$http.get('sys/dtis', { params: { date: this.model.date } }).then(response => {
        this.datas = response.data.data;
      });
    },
    onTableSelect(items) {
      this.selectItems = [];
      Object.keys(items).forEach((row, index) => {
        this.selectItems[index] = items[row];
      });
    },
    runAll() {
      for (var i = 0; i < this.selectItems.length; i++) {
        this.runItem(this.selectItems[i]);
      }
    },
    runItem(item) {
      item.is_running = true;
      const datas = {
        date: this.model.date,
        dtis: item.id
      };
      this.is_running++;
      item.begin_date=common.now();
      item.end_date='';
      this.$http.post('amiba/dtis/run', datas).then(response => {
        this.is_running--;
        item.end_date=common.now();
        this.$toast(item.name + '执行成功!');
        item.msg='执行成功';
        item.is_running = false
      }, response => {
        this.is_running--;
        item.end_date=common.now();

        if (response && response.response && response.response.data) {
          this.$toast(response.response.data.msg);
          item.msg=response.response.data.msg;
        } else {
          this.$toast(response.message);
          item.msg=response.message;
        }
        item.is_running = false
      });
    },
  },
  created() {

  },
  mounted() {
    this.loadDatas();
  },
};
</script>