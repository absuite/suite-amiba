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
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>责任调整单</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>编辑</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-layout md-gutter>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>单据编号</label>
              <md-input required v-model="model.main.doc_no" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>单据日期</label>
              <md-date required v-model="model.main.doc_date" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>核算目的</label>
              <md-input-ref required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>期间</label>
              <md-input-ref @init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>备注</label>
              <md-input v-model="model.main.memo" />
            </md-input-container>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="model.main.lines" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="调出核算要素" width="300px">
              <template scope="row">
                {{ row.fm_element&&row.fm_element.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.amiba.element.ref" v-model="row.fm_element" />
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="调入核算要素" width="300px">
              <template scope="row">
                {{ row.to_element&&row.to_element.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.amiba.element.ref" v-model="row.to_element" />
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="调出阿米巴" width="300px">
              <template scope="row">
                {{ row.fm_gGroup&&row.fm_gGroup.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.amiba.group.ref" v-model="row.fm_gGroup" />
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="调入阿米巴" width="300px">
              <template scope="row">
                {{ row.to_group&&row.to_group.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.amiba.group.ref" v-model="row.to_group" />
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="调整金额" width="150px" editable field="money">
            </md-grid-column>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
  </md-part>
</template>
<script>
import model from '../../gmf-sys/core/mixin/model';
export default {
  data() {
    return {
      selectedRows: [],
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
      var validator = this.$validate(this.model.main, { 'purpose': 'required', 'period': 'required' });
      var fail = validator.fails();
      if (fail && !notToast) {
        this.$toast(validator.errors.all());
      }
      return !fail;
    },
    initModel() {
      return {
        main: {
          'purpose': this.$root.userConfig.purpose,
          'period': this.$root.userConfig.period,
          'memo': '',
          'lines': []
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.data.adjust.list' } });
    },
    onLineAdd() {
      this.model.main.lines.push({});
    },
    init_period_ref(options) {
      if (this.model.main.purpose && this.model.main.purpose.calendar_id) {
        options.wheres.calendar = { name: 'calendar_id', value: this.model.main.purpose.calendar_id };
      } else {
        options.wheres.calendar = { name: 'calendar_id', value: this.$root.userConfig.calendar.id };
      }
    },
  },
  created() {
    this.model.entity = 'suite.amiba.data.adjust';
    this.model.order = "doc_no";
    this.route = 'amiba/data-adjusts';
  },
};
</script>