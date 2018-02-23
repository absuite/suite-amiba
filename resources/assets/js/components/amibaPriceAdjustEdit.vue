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
        <md-button @click.native="approve" :disabled="!canApprove">审核</md-button>
        <md-button @click.native="unapprove" :disabled="!canUnapprove">弃审</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button @click.native="list">列表</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-pager @paging="paging" :options="model.pager"></md-part-toolbar-pager>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column md-form">
        <md-layout md-gutter md-row>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>编码</label>
              <md-input required :disabled="isApproved" v-model="model.main.code"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>名称</label>
              <md-input required :disabled="isApproved" v-model="model.main.name"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="核算目的" required :disabled="isApproved" md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="阿米巴" required :disabled="isApproved" md-ref-id="suite.amiba.group.ref" v-model="model.main.group" />
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>状态</label>
              <md-enum md-enum-id="suite.cbo.data.state.enum" disabled v-model="model.main.state_enum" />
            </md-field>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="fetchLineDatas" :readonly="isApproved" ref="grid" :row-focused="false" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="对方阿米巴" field="group" dataType="entity" ref-id="suite.amiba.group.ref" :ref-init="init_group_ref" width="300px" editable/>
            <md-grid-column label="价格类型" field="type_enum" dataType="enum" editable ref-id="suite.amiba.price.type.enum" />
            <md-grid-column label="料品" field="item" dataType="entity" ref-id="suite.cbo.item.ref" width="300px" editable/>
            <md-grid-column label="价格" field="cost_price" editable/>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref md-ref-id="suite.amiba.group.ref" ref="lineRef" @confirm="lineRefClose"></md-ref>
  </md-part>
</template>
<script>
import model from 'gmf/core/mixins/MdModel/MdModel';
import modelGrid from 'gmf/core/mixins/MdModel/MdModelGrid';
export default {
  mixins: [model, modelGrid],
  computed: {
    canSave() {
      return this.model && this.model.main && this.model.main.state_enum === 'opened' && this.validate(true);
    },
    canApprove() {
      return this.model && this.model.main && this.model.main.id && this.model.main.state_enum === 'opened' && this.validate(true);
    },
    canUnapprove() {
      return this.model && this.model.main && this.model.main.state_enum === 'approved';
    },
    isApproved() {
      return this.model && this.model.main && this.model.main.state_enum === 'approved';
    }
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, {
        'code': 'required',
        'name': 'required',
        'purpose': 'required',
        'group': 'required'
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
          purpose: this.$root.configs.purpose,
          group: null,
          'state_enum': 'opened'
        }
      }
    },
    approve() {
      const oldState = this.model.main.state_enum;
      this.model.main.state_enum = 'approved';
      if (!this.serverStore()) {
        this.model.main.state_enum = oldState;
      }
    },
    unapprove() {
      const oldState = this.model.main.state_enum;
      this.model.main.state_enum = 'opened';
      if (!this.serverStore()) {
        this.model.main.state_enum = oldState;
      }
    },
    afterCopy() {
      this.model.main.state_enum = 'opened';
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.price.adjust.list' } });
    },
    onLineAdd() {
      this.$refs.grid && this.$refs.grid.addDatas({});
    },
    lineRefClose(datas) {
      this._.forEach(datas, (v, k) => {
        this.model.main.lines.push({ group: v });
      });
    },
    init_group_ref(options) {
      options.wheres.$leaf = { 'is_leaf': '1' };
      if (this.model.main.purpose) {
        options.wheres.$purpose = {'purpose_id': this.model.main.purpose.id };
      } else {
        options.wheres.$purpose = false;
      }
    },
    init_period_ref(options) {
      if (this.model.main.purpose && this.model.main.purpose.calendar_id) {
        options.wheres.$calendar = { 'calendar_id': this.model.main.purpose.calendar_id };
      } else {
        options.wheres.$calendar = { 'calendar_id': this.$root.configs.calendar.id };
      }
    },
  },
  created() {
    this.model.entity = 'suite.amiba.price.adjust';
    this.model.order = "code";
    this.route = 'amiba/price-adjusts';
  },
};
</script>