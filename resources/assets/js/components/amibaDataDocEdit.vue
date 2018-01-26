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
      <md-part-toolbar-group>
        <md-file-import md-entity="Suite\Amiba\Models\DataDoc"  template="/assets/vendor/suite-cbo/files/suite.amiba.doc.xlsx"></md-file-import>
      </md-part-toolbar-group>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column md-form">
        <md-layout md-gutter md-row>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>单据编号</label>
              <md-input required :disabled="isApproved" v-model="model.main.doc_no" />
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
              <md-datepicker md-label="单据日期" required :disabled="isApproved" v-model="model.main.doc_date" />
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="核算目的" :disabled="isApproved" md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose" />
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="期间" @init="init_period_ref" :disabled="isApproved" md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period" />
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="核算要素" @init="init_element_ref" :disabled="isApproved" md-ref-id="suite.amiba.element.ref" v-model="model.main.element" />
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>数据用途</label>
              <md-enum :disabled="isApproved" md-enum-id="suite.amiba.doc.use.type.enum" v-model="model.main.use_type_enum" />
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="阿米巴" @init="init_fm_group_ref" :disabled="isApproved" md-ref-id="suite.amiba.group.ref" v-model="model.main.fm_group" />
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="对方阿米巴" @init="init_to_group_ref" :disabled="isApproved" md-ref-id="suite.amiba.group.ref" v-model="model.main.to_group" />
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>考核金额</label>
              <md-input type="number" required :disabled="isApproved" v-model="model.main.money" />
            </md-field>
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
            <md-grid-column label="客商" field="trader" dataType="entity" ref-id="suite.cbo.trader.ref" width="200px" editable/>
            <md-grid-column label="料品分类" field="item_category" dataType="entity" ref-id="suite.cbo.item.category.ref" width="200px" editable/>
            <md-grid-column label="料品" field="item" dataType="entity" ref-id="suite.cbo.item.ref" width="200px" editable/>
            <md-grid-column label="描述" editable field="memo" />
            <md-grid-column label="费用项目" editable field="expense_code" />
            <md-grid-column label="科目" editable field="account_code" />
            <md-grid-column label="计量单位" field="unit" dataType="entity" ref-id="suite.cbo.unit.ref" width="200px" editable/>
            <md-grid-column label="数量" field="qty" editable/>
            <md-grid-column label="单价" field="price" editable/>
            <md-grid-column label="金额" field="money" editable/>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref :md-ref-id="lineRefID" ref="lineRef" @confirm="lineRefClose"></md-ref>
  </md-part>
</template>
<script>
import model from 'gmf/core/mixins/MdModel/MdModel';
import modelGrid from 'gmf/core/mixins/MdModel/MdModelGrid';
export default {
  mixins: [model, modelGrid],
  data() {
    return {
      lineRefID: '',
      lineRefField: ''
    };
  },
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
        'purpose': 'required',
        'period': 'required',
        'element': 'required',
        'use_type_enum': 'required'
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
          doc_date: this.$root.userConfig.date,
          purpose: this.$root.userConfig.purpose,
          period: this.$root.userConfig.period,
          memo: '',
          currency: this.$root.userConfig.currency,
          element: null,
          fm_group: null,
          to_group: null,
          'state_enum': 'opened',
          'use_type_enum': '',
          'doc_no': '',
        }
      }
    },
    async afterInitData() {
      this.model.main.doc_no = await this.$root.issueSn('suite.amiba.data.doc');
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
      this.$router.push({ name: 'module', params: { module: 'amiba.data.doc.list' } });
    },
    onLineAdd() {
      if (this.$refs.grid && this.$refs.grid.focusCell && this.$refs.grid.focusCell.column) {
        this.lineRefField = this.$refs.grid.focusCell.column.field;
        this.lineRefID = this.$refs.grid.focusCell.column.refId;
        if (this.lineRefField == 'item' ||
          this.lineRefField == 'trader' ||
          this.lineRefField == 'item_category') {
          this.$nextTick(() => {
            this.$refs['lineRef'].open();
          });
          return;
        }
      }
      this.$refs.grid && this.$refs.grid.addDatas({ data_type_enum: '' });
    },
    lineRefClose(datas) {
      if (!datas || !datas.length || !this.lineRefField ||
        !this.$refs.grid || !this.$refs.grid.focusCell ||
        !this.$refs.grid.focusCell.row ||
        !this.$refs.grid.focusCell.row.data) {
        return;
      }
      if (!this.$refs.grid.focusCell.getValue()) {
        this.$refs.grid.focusCell.setValue(datas[0]);
        datas.splice(0, 1);
      }
      this._.forEach(datas, (v, k) => {
        let row = {};
        row[this.lineRefField] = v;
        row['data_type_enum'] = '';
        this.$refs.grid.addDatas(row);
      });
    },
    init_fm_group_ref(options) {
      options.wheres.leaf = { name: 'is_leaf', value: '1' };
      if (this.model.main.purpose) {
        options.wheres.purpose = { name: 'purpose_id', value: this.model.main.purpose.id };
      } else {
        options.wheres.purpose = false;
      }
    },
    init_to_group_ref(options) {
      options.wheres.leaf = { name: 'is_leaf', value: '1' };
      if (this.model.main.purpose) {
        options.wheres.purpose = { name: 'purpose_id', value: this.model.main.purpose.id };
      } else {
        options.wheres.purpose = false;
      }
    },
    init_element_ref(options) {
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
    this.model.entity = 'suite.amiba.data.doc';
    this.model.order = "doc_no";
    this.route = 'amiba/data-docs';
  },
};
</script>