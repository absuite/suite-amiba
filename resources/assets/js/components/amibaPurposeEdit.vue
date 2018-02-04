<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="save" :disabled="!canSave">保存</md-button>
        <md-button @click.native="cancel">放弃</md-button>
        <md-button @click.native="create">新增</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button @click.native="copy" :disabled="!canCopy">复制</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button @click.native="list">列表</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-pager @paging="paging" :options="model.pager"></md-part-toolbar-pager>
      <md-part-toolbar-group>
        <md-button @click="showCopyDialog=true">数据拷贝</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body>
      <md-content>
        <md-field>
          <label>编码</label>
          <md-input required v-model="model.main.code"></md-input>
        </md-field>
        <md-field>
          <label>名称</label>
          <md-input required v-model="model.main.name"></md-input>
        </md-field>
        <md-ref-input md-label="日历" required md-ref-id="suite.cbo.period.calendar.ref" v-model="model.main.calendar" />
        <md-ref-input md-label="币种" required md-ref-id="suite.cbo.currency.ref" v-model="model.main.currency" />
        <md-field>
          <label>备注</label>
          <md-textarea v-model="model.main.memo"></md-textarea>
        </md-field>
      </md-content>
      <md-loading :loading="loading"></md-loading>
      <amiba-data-copy :md-active.sync="showCopyDialog"></amiba-data-copy>
    </md-part-body>
  </md-part>
</template>
<script>
import model from 'gmf/core/mixins/MdModel/MdModel';

import amibaDataCopy from './amibaDataCopy';
export default {
  components: {
    amibaDataCopy
  },
  data() {
    return {
      showCopyDialog: false
    };
  },
  mixins: [model],
  computed: {
    canSave() {
      return this.validate(true);
    }
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, {
        'code': 'required',
        'name': 'required',
        'calendar': 'required',
        'currency': 'required'
      });
      var fail = validator.fails();
      if (fail && !notToast) {
        this.$toast(validator.errors.all());
      }
      return !fail;
    },
    initModel() {
      return {
        main: {
          'code': '',
          'name': '',
          'memo': '',
          calendar: this.$root.userConfig.calendar,
          currency: this.$root.userConfig.currency
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.purpose.list' } });
    },
  },
  created() {
    this.model.entity = 'suite.amiba.purpose';
    this.model.order = "code";
    this.route = 'amiba/purposes';
  },
};
</script>