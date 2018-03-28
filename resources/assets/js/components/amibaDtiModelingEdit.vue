<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="list">列表</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-ref-input md-label="核算目的" md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
        </md-ref-input>
        <md-ref-input md-label="期间"  multiple md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period">
        </md-ref-input>
        <md-ref-input md-label="经营模型(可选)" multiple md-placeholder="添加经营模型，按经营模型进行建模"  md-ref-id="suite.amiba.modeling.ref" v-model="model.main.modeling">
        </md-ref-input>
        <md-field>
          <label>备注</label>
          <md-input v-model="model.main.memo"></md-input>
        </md-field>
        <md-layout>
          <md-button class="md-raised md-accent" @click.native="save" :disabled="!canSave">数据建模</md-button>
        </md-layout>
      </md-content>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
export default {
  data() {
    return {
      selectedRows: [],
      model: { main: { purpose: null, period: null, memo: '' } },
      loading: 0,
    };
  },
  computed: {
    canSave() {
      return this.validate(true);
    }
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, { 'purpose': 'required', 'period': 'required' });
      var fail = validator.fails();
      if (fail && !notToast) {
        this.$toast(validator.errors.all());
      }
      return !fail;
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.dti.modeling.list' } });
    },
    save() {
      this.loading++;
      this.$http.post('amiba/dti-modelings', this.model.main).then(response => {
        this.loading--;
        this.$toast(this.$lang.LANG_JOBSUCCESS);
      }, response => {
        this.$toast(response);
        this.loading--;
      });
    },
  },
  created() {

  },
};
</script>