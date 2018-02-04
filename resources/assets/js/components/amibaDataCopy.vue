<template>
  <md-dialog :md-active.sync="isActive" @md-closed="closeDia">
    <md-dialog-title>基础数据拷贝</md-dialog-title>
    <md-dialog-content>
      <md-layout md-gutter md-row>
        <md-layout md-flex="100">
          <md-ref-input md-label="来源核算目的" md-ref-id="suite.amiba.purpose.ref" v-model="mainData.fm_purpose"></md-ref-input>
        </md-layout>
        <md-layout md-flex-xs="100" md-flex="50">
          <md-list>
            <md-list-item>
              <md-checkbox v-model="mainData.datas" value="element"></md-checkbox>
              <span class="md-list-item-text">核算要素</span>
            </md-list-item>
            <md-list-item>
              <md-checkbox v-model="mainData.datas" value="group"></md-checkbox>
              <span class="md-list-item-text">阿米巴单元</span>
            </md-list-item>
            <md-list-item>
              <md-checkbox v-model="mainData.datas" value="modeling"></md-checkbox>
              <span class="md-list-item-text">经营模型</span>
            </md-list-item>
            <md-list-item>
              <md-checkbox v-model="mainData.datas" value="price"></md-checkbox>
              <span class="md-list-item-text">交易价表</span>
            </md-list-item>
          </md-list>
        </md-layout>
        <md-layout md-flex-xs="100" md-flex="50">
          <md-list>
            <md-list-item>
              <md-checkbox v-model="mainData.datas" value="target"></md-checkbox>
              <span class="md-list-item-text">经营目标</span>
            </md-list-item>
            <md-list-item>
              <md-checkbox v-model="mainData.datas" value="allot.method"></md-checkbox>
              <span class="md-list-item-text">分配方法</span>
            </md-list-item>
            <md-list-item>
              <md-checkbox v-model="mainData.datas" value="allot.rule"></md-checkbox>
              <span class="md-list-item-text">分配标准</span>
            </md-list-item>
          </md-list>
        </md-layout>
      </md-layout>
    </md-dialog-content>
    <md-dialog-actions>
      <md-button class="md-primary" @click="closeDia">取消</md-button>
      <md-button class="md-primary" @click="confirm">开始拷贝</md-button>
    </md-dialog-actions>
  </md-dialog>
</template>
<script>
export default {
  props: {
    mdId: String,
    mdActive: Boolean
  },
  data() {
    return {
      mainData: { fm_purpose: null, datas: [] },
      isActive: false
    };
  },
  watch: {
    async mdActive(isActive) {
      this.isActive = isActive;
      await this.$nextTick();
    },
  },
  methods: {
    closeDia() {
      this.isActive = false;
      this.$emit('update:mdActive', false);
    },
    confirm() {
      return;
      this.$http.post('amiba/purposes/copy', this.mainData).then(response => {

      }, err => {
        this.$toast(err);
      });
    },
  }
};
</script>
<style lang="scss" scoped>
.md-list {
  width: 100%;
}
.md-dialog{
  width: 600px;
}
</style>