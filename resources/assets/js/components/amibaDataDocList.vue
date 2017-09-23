<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="create">新增</md-button>
        <md-button @click.native="remove" :disabled="!(selectRows&&selectRows.length)">删除</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-layout md-gutter>
          <md-layout>
            <md-input-container class="md-inset">
              <md-input :fetch="doFetch" placeholder="search" @keyup.enter.native="load()"></md-input>
            </md-input-container>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>核算数据表</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>列表</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-query @select="select" @dblclick="edit" @init="initQuery" ref="list" md-query-id="suite.amiba.data.doc.list"></md-query>
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
      this.$router.push({ name: 'module', params: { module: 'amiba.data.doc.edit' } });
    },
    edit(item) {
      this.$router.push({ name: 'id', params: { module: 'amiba.data.doc.edit', id: item.id } });
    },
    doFetch(q) {
      if (this.currentQ != q) {
        this.load();
      }
      this.currentQ = q;
    },
    initQuery(options) {
      options.wheres.filter = false;
      if (this.currentQ) {
        options.wheres.filter = {
          "or": [
            { name: 'doc_no', operator: 'like', value: this.currentQ },
            { name: 'fm_group.name', operator: 'like', value: this.currentQ },
            { name: 'period.name', operator: 'like', value: this.currentQ }
          ]
        };
      }
    },
    remove() {
      if (!this.selectRows || !this.selectRows.length) {
        this.$toast(this.$lang.LANG_NODELETEDATA);
        return;
      }
      this.loading++;
      const ids = this._.map(this.selectRows, 'id').toString();
      this.$http.delete('amiba/data-docs/' + ids).then(response => {
        this.load();
        this.loading--;
        this.$toast(this.$lang.LANG_DELETESUCCESS);
      }, response => {
        this.$toast(this.$lang.LANG_DELETEFAIL);
        this.loading--;
      });
    },
    select(items) {
      this.selectRows = items;
    },
    load() {
      this.$refs.list.pagination(1);
    }
  }
};
</script>