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
        <md-part-toolbar-crumb>经营目标</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>编辑</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-layout md-gutter>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>核算目的</label>
              <md-input-ref required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>阿米巴</label>
              <md-input-ref @init="init_group_ref" required md-ref-id="suite.amiba.group.ref" v-model="model.main.group"></md-input-ref>
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>开始期间</label>
              <md-input-ref @init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.fm_period">
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>结束期间</label>
              <md-input-ref @init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.to_period">
              </md-input-ref>
            </md-input-container>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="loadLineDatas" ref="grid" :row-focused="false" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="指标类型" field="type_enum" dataType="enum" editable ref-id="suite.amiba.data.target.type.enum" />
            <md-grid-column label="基准核算要素" field="element" dataType="entity" ref-id="suite.amiba.element.ref" width="200px" editable/>
            <md-grid-column label="目标额度" field="money" editable/>
            <md-grid-column label="目标比率" field="rate" editable/>
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
      var validator = this.$validate(this.model.main, {
        'purpose': 'required',
        'group': 'required',
        'fm_period': 'required',
        'to_period': 'required',
        'lines': 'required|min:1'
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
          'purpose': this.$root.userConfig.purpose,
          'group': null,
          fm_period: this.$root.userConfig.period,
          to_period: this.$root.userConfig.period
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.data.target.list' } });
    },
    onLineAdd() {
      this.$refs.grid && this.$refs.grid.addDatas({ element: null });
    },
    async loadLineDatas({ pager }) {
      if (!this.model.main.id) {
        return [];
      }
      return await this.$http.get(this.route + '/' + this.model.main.id + '/lines', { params: pager });
    },
    beforeSave() {
      if (this.$refs.grid) {
        this.$refs.grid.endEdit();
        this.model.main.lines = this.$refs.grid.getPostDatas();
      }
    },
    afterLoadData() {
      this.$refs.grid && this.$refs.grid.refresh();
    },
    afterCreate() {
      this.$refs.grid && this.$refs.grid.refresh();
    },
    afterCopy() {
      this.$refs.grid && this.$refs.grid.refresh();
    },
    init_group_ref(options) {
      options.wheres.leaf = { name: 'is_leaf', value: '1' };
      if (this.model.main.purpose) {
        options.wheres.purpose = { name: 'purpose_id', value: this.model.main.purpose.id };
      } else {
        options.wheres.purpose = false;
      }
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
    this.model.entity = 'suite.amiba.data.target';
    this.model.order = "created_at";
    this.route = 'amiba/data-targets';
  },
};
</script>