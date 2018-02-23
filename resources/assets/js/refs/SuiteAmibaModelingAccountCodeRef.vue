<template>
  <div class="layout flex">
    <div v-for="item in mainDatas"></div>
  </div>
</template>
<script>
import common from 'core/utils/common';
import MdRefMixin from 'core/mixins/MdRef/MdRef';
export default {
  name: 'SuiteAmibaModelingAccountCodeRef',
  mixins: [MdRefMixin],
  data() {
    return {
      mainDatas: []
    };
  },
  methods: {
    loadData(q) {
      const options = { fields: ['account'], distinct: true, wheres: { ent_id: this.$root.configs.ent.id } };
      this.$http.post('sys/queries/query/suite.amiba.doc.fi', options).then(res => {
        this.mainDatas = res.data.data;
      });
    },
    dblclick({ data }) {
      this.selectedRows = [data];
      this.onConfirm();
    },
  },
};
</script>