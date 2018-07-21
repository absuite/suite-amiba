<template>
  <div class="layout flex">
    <md-content>
      <div v-for="(item,ind) in mainDatas" :key="ind">
        <md-chip md-clickable @md-click="onClick(item.item)" @dblclick="onDblclick(item.item)">{{item.item}}</md-chip>
      </div>
    </md-content>
  </div>
</template>
<script>
  import common from "gmf/core/utils/common";
  import MdRefMixin from "gmf/core/mixins/MdRef/MdRef";
  export default {
    name: "SuiteAmibaModelingProjectCodeRef",
    mixins: [MdRefMixin],
    data() {
      return {
        mainDatas: []
      };
    },
    methods: {
      loadData(q) {
        const options = {
          params: {
            q: q
          }
        };
        this.$http
          .get("amiba/modelings/refs/project", options)
          .then(res => {
            this.mainDatas = res.data.data;
          });
      },
      onClick(data) {
        this.setData([data]);
      },
      onDblclick(data) {
        this.setData([data]);
        this.onConfirm();
      }
    }
  };
</script>