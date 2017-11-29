<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="create">接口</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-group>
        <md-fetch :fetch="doFetch"></md-fetch>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body>
      <md-query ref="list" @init="initQuery" md-query-id="gmf.sys.dti.log.list"></md-query>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
export default {
  data() {
    return {
      selectRows: [],
      loading: 0
    };
  },
  methods: {
    create() {
      this.$router.push({ name: 'module', params: { module: 'amiba.dti.run' } });
    },
    doFetch(q) {
      if (this.currentQ != q) {
        this.currentQ = q;
        this.loadData();
      }
      this.currentQ = q;
    },
    initQuery(options) {
      options.wheres.filter = false;
      if (this.currentQ) {
        options.wheres.filter = {
          "or": [
            { name: 'dti.name', operator: 'like', value: this.currentQ },
            { name: 'dti.category.name', operator: 'like', value: this.currentQ }
          ]
        };
      }
      options.orders[0] = { name: 'created_at', direction: 'desc' };
      options.orders[1] = { name: 'session', direction: 'desc' };
    },
    select(items) {
      this.selectRows = items;
    },
    loadData() {
      this.$refs.list.pagination(1);
    }
  }
};
</script>