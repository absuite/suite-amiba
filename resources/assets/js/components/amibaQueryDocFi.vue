<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="remove" :disabled="!(selectRows&&selectRows.length)">删除</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-flex-small="33" md-flex-medium="15" md-flex-large="15">
            <md-field class="md-inset">
              <label>期间</label>
              <md-input-ref md-ref-id="suite.cbo.period.account.ref" v-model="model.period"></md-input-ref>
            </md-field>
          </md-layout>
          <md-layout md-flex-small="33" md-flex-medium="15" md-flex-large="15">
            <md-field class="md-inset">
              <label>业务类型</label>
              <md-input v-model="model.biz_type"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-small="33" md-flex-medium="15" md-flex-large="15">
            <md-field class="md-inset">
              <label>单据类型</label>
              <md-input v-model="model.doc_type"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-small="33" md-flex-medium="15" md-flex-large="15">
            <md-field class="md-inset">
              <label>组织</label>
              <md-input v-model="model.org"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-small="33" md-flex-medium="15" md-flex-large="15">
            <md-field class="md-inset">
              <label>科目</label>
              <md-input v-model="model.account"></md-input>
            </md-field>
          </md-layout>
          <md-layout>
            <md-button @click.native="query">查询</md-button>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>财务数据</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>查询</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-query @select="select" @init="initQuery" ref="list" md-query-id="suite.amiba.doc.fi.list"></md-query>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
export default {
  data() {
    return {
      selectRows: [],
      loading: 0,
      model: { period: this.$root.userConfig.period, biz_type: '', doc_type: '', org: '', account: '' }
    };
  },
  methods: {
    select(items) {
      this.selectRows = items;
    },
    query() {
      this.loadData();
    },
    remove() {
      if (!this.selectRows || !this.selectRows.length) {
        this.$toast(this.$lang.LANG_NODELETEDATA);
        return;
      }
      this.loading++;
      const ids = this._.map(this.selectRows, 'id').toString();
      this.$http.delete('amiba/doc-fis/' + ids).then(response => {
        this.loadData();
        this.loading--;
        this.$toast(this.$lang.LANG_DELETESUCCESS);
      }, response => {
        this.$toast(response);
        this.loading--;
      });
    },
    initQuery(options) {
      options.wheres.fm_date = false;
      options.wheres.to_date = false;
      options.wheres.biz_type = false;
      options.wheres.doc_type = false;
      options.wheres.org = false;
      options.wheres.account = false;

      if (this.model.period) {
        options.wheres.fm_date = { name: 'doc_date', operator: 'greater_than_equal', value: this.model.period.from_date };
        options.wheres.to_date = { name: 'doc_date', operator: 'less_than_equal', value: this.model.period.to_date };
      }
      if (this.model.biz_type) {
        options.wheres.biz_type = { name: 'biz_type', operator: 'like', value: this.model.biz_type };
      }
      if (this.model.doc_type) {
        options.wheres.doc_type = { name: 'doc_type', operator: 'like', value: this.model.doc_type };
      }
      if (this.model.org) {
        options.wheres.org = { name: 'org', operator: 'like', value: this.model.org };
      }
      if (this.model.account) {
        options.wheres.account = { name: 'account', operator: 'like', value: this.model.account };
      }
    },
    loadData() {
      this.$refs.list.pagination(1);
    }
  }
};
</script>