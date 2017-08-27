<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="create">接口</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>接口</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>日志</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-query @init="init_model_query" ref="list" md-query-id="gmf.sys.dti.log.list"></md-query>
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
      create(){
        this.$router.push({ name: 'module', params: { module: 'amiba.dti.run' }});
      },
      select(items){
        this.selectRows=items;
      },
      load(){
        this.$refs.list.pagination(1);
      },
      init_model_query(options){
        options.orders[0]={name:'session',direction:'desc'};
        options.orders[1]={name:'created_at',direction:'desc'};
      },
    }
  };
</script>
