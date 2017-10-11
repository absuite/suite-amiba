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
        <md-part-toolbar-crumb>核算数据表</md-part-toolbar-crumb>
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
              <md-input-ref md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>期间</label>
              <md-input-ref @init="init_period_ref" md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>核算要素</label>
              <md-input-ref @init="init_element_ref" md-ref-id="suite.amiba.element.ref" v-model="model.main.element" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>数据用途</label>
              <md-enum md-enum-id="suite.amiba.doc.use.type.enum" v-model="model.main.use_type_enum" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>阿米巴</label>
              <md-input-ref @init="init_fm_group_ref" md-ref-id="suite.amiba.group.ref" v-model="model.main.fm_group" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>对方阿米巴</label>
              <md-input-ref @init="init_to_group_ref" md-ref-id="suite.amiba.group.ref" v-model="model.main.to_group" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>考核金额</label>
              <md-input type="number" required v-model="model.main.money" />
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>单据状态</label>
              <md-enum md-enum-id="suite.amiba.doc.state.enum" v-model="model.main.state_enum" />
            </md-input-container>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="model.main.lines" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="客商" width="300px">
              <template scope="row">
                {{ row.trader&&row.trader.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.cbo.trader.ref" v-model="row.trader" />
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="料品分类" width="300px">
              <template scope="row">
                {{ row.item_category&&row.item_category.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.cbo.item.category.ref" v-model="row.item_category" />
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="料品" width="300px">
              <template scope="row">
                {{ row.item&&row.item.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.cbo.item.ref" v-model="row.item" />
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="描述" width="150px" editable field="memo" />
            <md-grid-column label="费用项目" width="150px" editable field="expense_code" />
            <md-grid-column label="科目" width="150px" editable field="account_code" />
            <md-grid-column label="计量单位" width="300px">
              <template scope="row">
                {{ row.unit&&row.unit.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.cbo.unit.ref" v-model="row.unit" />
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="数量" width="150px" editable field="qty" />
            <md-grid-column label="单价" width="150px" editable field="price" />
            <md-grid-column label="金额" width="150px" editable field="money" />
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
      modelLines: {
        datas: [],
        pager: { page: 1, size: 10 }
      }
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
          state_enum: 'opened',
          use_type_enum: '',
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.data.doc.list' } });
    },
    load_extend(id) {
      this.modelLines.pager.page = 1;
      this.onTablePagination(this.modelLines.pager);
    },
    copy_extend() {
      this.modelLines.datas = [];
    },
    onTablePagination(pager) {
      this.$http.get(this.route + '/' + this.model.main.id + '/lines', { params: { page: pager.page, size: pager.size } }).then(response => {
        this.modelLines.datas = response.data.data || [];
        this.modelLines.pager = response.data.pager;
      });
    },
    onTableSelect(items) {
      this.selectedRows = [];
      Object.keys(items).forEach((row, index) => {
        this.selectedRows[index] = items[row];
      });
    },
    onLineAdd() {
      this.modelLines.datas.push({ data_type_enum: '' });
    },
    onLineRemove() {
      this._.forEach(this.selectedRows, (v, k) => {
        var idx = this.modelLines.datas.indexOf(v);
        if (idx >= 0) {
          this.modelLines.datas.splice(idx, 1);
        }
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