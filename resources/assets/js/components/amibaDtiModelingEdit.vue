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
        <md-ref-input md-label="期间" multiple :md-init="init_period_ref" md-ref-id="suite.cbo.period.account.ref"
          v-model="model.main.period">
        </md-ref-input>
        <md-ref-input md-label="经营模型(可选)" :md-init="init_modeling_ref" md-placeholder="添加经营模型，按经营模型进行建模" md-ref-id="suite.amiba.modeling.ref"
          v-model="model.main.modeling">
        </md-ref-input>
        <md-field>
          <label>备注</label>
          <md-input v-model="model.main.memo"></md-input>
        </md-field>
        <md-layout>
          <md-button class="md-raised md-accent" @click.native="save(false)" :disabled="!canSave">数据建模(同步执行)</md-button>
          <md-button class="md-raised md-warn" @click.native="save(true)" :disabled="!canSave">数据建模(异步执行)</md-button>
        </md-layout>
      </md-content>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
  import MdValidate from 'cbo/mixins/MdValidate/MdValidate';
  export default {
    mixins: [MdValidate],
    data() {
      return {
        selectedRows: [],
        model: {
          main: {
            purpose: null,
            period: null,
            memo: ''
          }
        },
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
        var validator = this.$validate(this.model.main, {
          'purpose': 'required',
          'period': 'required'
        });
        var fail = validator.fails();
        if (fail && !notToast) {
          this.$toast(validator.errors.all());
        }
        return !fail;
      },
      list() {
        this.$router.push({
          name: 'module',
          params: {
            module: 'amiba.dti.modeling.list'
          }
        });
      },
      save(run_in_job) {
        this.loading++;
        this.model.main.run_in_job = run_in_job ? true : false;
        this.$http.post('amiba/dti-modelings', this.model.main).then(response => {
          this.loading--;
          this.$toast(run_in_job ? '提交请求成功，请到列表查看执行状态' : '执行成功');
        }, response => {
          this.$toast(response);
          this.loading--;
        });
      },
      init_period_ref(options) {
        if (this.model.main.purpose && this.model.main.purpose.calendar_id) {
          options.wheres.$calendar = {
            'calendar_id': this.model.main.purpose.calendar_id
          };
        } else {
          options.wheres.$calendar = false;
        }
      },
      init_modeling_ref(options) {
        if (this.model.main.purpose && this.model.main.purpose.id) {
          options.wheres.$purpose = {
            'purpose_id': this.model.main.purpose.id
          };
        } else {
          options.wheres.$purpose = false;
        }
      },
    },
    created() {

    },
  };
</script>