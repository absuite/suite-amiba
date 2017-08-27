<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="query">查询</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>财务数据</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>查询</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-query @select="select" ref="list" md-query-id="suite.amiba.doc.fi.list"></md-query>
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
      }
    }
  };
</script>
