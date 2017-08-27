<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="query">查询</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>考核结果</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>查询</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-query @init="init_model_query" @select="select" ref="list" md-query-id="suite.amiba.result.account.list"></md-query>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
  export default {
    data() {
      return {
        selectRows: [],
        loading:0
      };
    },
    methods: {
      select(items){
        this.selectRows=items;
      },
      query(){

      },
      load(){
        this.$refs.list.pagination(1);
      },
      init_model_query(options){
        options.orders[0]={name:'purpose_id'};
        options.orders[1]={name:'period_id'};
        options.orders[2]={name:'group_id'};
      },
    }
  };
</script>
