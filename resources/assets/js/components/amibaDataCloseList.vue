<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="create">新增</md-button>
        <md-button @click.native="remove" :disabled="!(selectRows&&selectRows.length)">取消关账</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body class="no-padding">
      <md-query @select="select" ref="list" md-query-id="suite.amiba.data.close.list"></md-query>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
import _map from 'lodash/map'
  export default {
    data() {
      return {
        selectRows: [],
        loading:0
      };
    },
    methods: {
      create(){
        this.$router.push({ name: 'module', params: { module: 'amiba.data.close.edit' }});
      },
      remove(){
        if(!this.selectRows||!this.selectRows.length){
          this.$toast(this.$lang.LANG_NODELETEDATA);
          return;
        }
        this.loading++;
        const ids=_map(this.selectRows,'id').toString();
        this.$http.delete('amiba/data-closes/'+ids).then(response => {
          this.loadData();
          this.loading--;
          this.$toast(this.$lang.LANG_DELETESUCCESS);
        }, response => {
          this.$toast(response);
          this.loading--;
        });
      },
      select(items){
        this.selectRows=items;
      },
      loadData(){
        this.$refs.list.pagination(1);
      }
    }
  };
</script>
