<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="remove" :disabled="!(selectRows&&selectRows.length)">删除</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-file-import md-entity="Suite\Amiba\Models\DocBiz" template="/assets/vendor/suite-cbo/files/suite.amiba.doc.biz.xlsx"></md-file-import>
      </md-part-toolbar-group>
      <md-part-toolbar-group class="flex">
        <md-layout md-gutter>
          <md-layout md-flex-sm="20" md-flex-md="15" md-flex-lg="15">
            <md-ref-input md-ref-id="suite.cbo.period.account.ref" md-label="期间" v-model="model.period"></md-ref-input>
          </md-layout>
          <md-layout md-flex-sm="20" md-flex-md="15" md-flex-lg="15">
            <md-field class="md-inset">
              <label>业务类型</label>
              <md-enum md-enum-id="suite.cbo.biz.type.enum" v-model="model.biz_type"></md-enum>
            </md-field>
          </md-layout>
          <md-layout md-flex-sm="20" md-flex-md="15" md-flex-lg="15">
            <md-field class="md-inset">
              <label>单据类型</label>
              <md-input v-model="model.doc_type"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-sm="20" md-flex-md="15" md-flex-lg="15">
            <md-field class="md-inset">
              <label>组织</label>
              <md-input v-model="model.org"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-sm="20" md-flex-md="15" md-flex-lg="15">
            <md-field class="md-inset">
              <label>部门</label>
              <md-input v-model="model.dept"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-sm="20" md-flex-md="15" md-flex-lg="15">
            <md-field class="md-inset">
              <label>料品</label>
              <md-input v-model="model.item"></md-input>
            </md-field>
          </md-layout>
          <md-layout>
            <md-button class="md-primary" @click.native="query">查询</md-button>
          </md-layout>
        </md-layout>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body class="no-padding">
      <md-query @select="select" :md-init="initQuery" ref="list" md-query-id="suite.amiba.doc.biz.list"></md-query>
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
      loading: 0,
      model: { period: this.$root.configs.period, biz_type: '', doc_type: '', org: '', item: '' }
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
      const ids = _map(this.selectRows, 'id').toString();
      this.$http.delete('amiba/doc-bizs/' + ids).then(response => {
        this.loadData();
        this.loading--;
        this.$toast(this.$lang.LANG_DELETESUCCESS);
      }, response => {
        this.$toast(response);
        this.loading--;
      });
    },
    initQuery(options) {
      options.wheres.$fm_date = false;
      options.wheres.$to_date = false;
      options.wheres.$biz_type = false;
      options.wheres.$doc_type = false;
      options.wheres.$org = false;
      options.wheres.$item = false;

      if (this.model.period) {
        options.wheres.$fm_date = { 'gte': { 'doc_date': this.model.period.from_date } };
        options.wheres.$to_date = { 'lte': { 'doc_date': this.model.period.to_date } };
      }
      if (this.model.biz_type) {
        options.wheres.$biz_type = { 'like': { 'biz_type': this.model.biz_type } };
      }
      if (this.model.doc_type) {
        options.wheres.$doc_type = { 'like': { 'doc_type': this.model.doc_type } };
      }
      if (this.model.org) {
        options.wheres.$org = { 'like': { 'org': this.model.org } };
      }
      if (this.model.dept) {
        options.wheres.$dept = { 'or': [{ 'like': { 'fm_dept': this.model.dept } }, { 'like': { 'to_dept': this.model.dept } }] };
      }
      if (this.model.item) {
        options.wheres.$item = { 'like': { 'item': this.model.item } };
      }
    },
    loadData() {
      this.$refs.list.pagination(1);
    }
  }
};
</script>