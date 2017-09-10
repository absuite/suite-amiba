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
        <md-part-toolbar-crumb>阿米巴经营模型</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>编辑</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-layout md-gutter>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20">
            <md-input-container>
              <label>核算目的</label>
              <md-input-ref required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20">
            <md-input-container>
              <label>阿米巴</label>
              <md-input-ref required @init="init_group_ref" md-ref-id="suite.amiba.group.ref" v-model="model.main.group">
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20">
            <md-input-container>
              <label>备注</label>
              <md-input v-model="model.main.memo"></md-input>
            </md-input-container>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-table-card class="flex">
            <md-table @select="onTableSelect" class="flex">
              <md-table-header>
                <md-table-row>
                  <md-table-head width="150px">核算要素</md-table-head>
                  <md-table-head width="150px">匹配方向</md-table-head>
                  <md-table-head width="150px">业务类型</md-table-head>
                  <md-table-head width="150px">单据类型</md-table-head>
                  <md-table-head width="150px">料品分类</md-table-head>
                  <md-table-head width="150px">费用项目</md-table-head>
                  <md-table-head width="150px">会计科目</md-table-head>
                  <md-table-head width="150px">客商</md-table-head>
                  <md-table-head width="150px">物料</md-table-head>
                  <md-table-head width="150px">因素1</md-table-head>
                  <md-table-head width="150px">因素2</md-table-head>
                  <md-table-head width="150px">来源巴</md-table-head>
                  <md-table-head width="150px">取值类型</md-table-head>
                  <md-table-head md-tooltip="100">取值比例%</md-table-head>
                </md-table-row>
              </md-table-header>
              <md-table-body>
                <md-table-row v-for="(row, rowIndex) in model.main.lines" :key="rowIndex" :md-item="row" md-selection>
                  <md-table-cell>
                    <md-input-container>
                      <md-input-ref @init="init_element_ref" md-ref-id="suite.amiba.element.ref" v-model="row.element"></md-input-ref>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-enum md-enum-id="suite.amiba.modeling.match.direction.enum" v-model="row.match_direction_enum"></md-enum>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-enum md-enum-id="suite.cbo.biz.type.enum" v-model="row.biz_type_enum"></md-enum>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input-ref @init="init_doc_type_ref" md-ref-id="suite.cbo.doc.type.ref" v-model="row.doc_type"></md-input-ref>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input-ref md-ref-id="suite.cbo.item.category.ref" v-model="row.item_category"></md-input-ref>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input v-model="row.project_code"></md-input>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input v-model="row.account_code"></md-input>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input-ref md-ref-id="suite.cbo.trader.ref" v-model="row.trader"></md-input-ref>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input-ref md-ref-id="suite.cbo.item.ref" v-model="row.item"></md-input-ref>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input v-model="row.factor1"></md-input>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input v-model="row.factor2"></md-input>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input-ref md-ref-id="suite.amiba.group.ref" v-model="row.src_group"></md-input-ref>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-enum md-enum-id="suite.amiba.value.type.enum" v-model="row.value_type_enum"></md-enum>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input type="number" v-model="row.adjust"></md-input>
                    </md-input-container>
                  </md-table-cell>
                </md-table-row>
              </md-table-body>
            </md-table>
            <md-table-tool>
              <md-table-action md-insert @onAdd="onLineAdd" @onRemove="onLineRemove"></md-table-action>
              <md-layout class="flex"></md-layout>
              <md-table-pagination md-size="5" md-total="10" md-page="1" md-label="Rows" md-separator="of" :md-page-options="[5, 10, 25, 50]" @pagination="onTablePagination">
              </md-table-pagination>
            </md-table-tool>
          </md-table-card>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref md-ref-id="suite.amiba.element.ref" ref="lineRef" @close="lineRefClose"></md-ref>
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
      var validator = this.$validate(this.model.main, { 'purpose': 'required', 'group': 'required' });
      var fail = validator.fails();
      if (fail && !notToast) {
        this.$toast(validator.errors.all());
      }
      return !fail;
    },
    initModel() {
      return {
        main: {
          'lines': [],
          'purpose': this.$root.userConfig.purpose,
          'group': null,
          'memo': ''
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.modeling.list' } });
    },
    onTablePagination(page) {

    },
    onTableSelect(items) {
      this.selectedRows = [];
      Object.keys(items).forEach((row, index) => {
        this.selectedRows[index] = items[row];
      });
    },
    onLineAdd() {
      this.$refs['lineRef'].open();
    },
    onLineRemove() {
      this._.forEach(this.selectedRows, (v, k) => {
        var idx = this.model.main.lines.indexOf(v);
        if (idx >= 0) {
          this.model.main.lines.splice(idx, 1);
        }
      });
    },
    lineRefClose(datas) {
      this._.forEach(datas, (v, k) => {
        this.model.main.lines.push({ 'element': v, 'biz_type_enum': '', 'match_direction_enum': 'fm', 'src_element': null, 'src_group': null, 'value_type_enum': 'amt', adjust: '100' });
      });
    },
    init_group_ref(options) {
      options.wheres.leaf = { name: 'is_leaf', value: '1' };
      if (this.model.main.purpose) {
        options.wheres.purpose = { name: 'purpose_id', value: this.model.main.purpose.id };
      } else {
        options.wheres.purpose = false;
      }
    },
    init_element_ref(options) {
      if (this.model.main.purpose) {
        options.wheres.purpose = { name: 'purpose_id', value: this.model.main.purpose.id };
      } else {
        options.wheres.purpose = false;
      }
    },
    init_doc_type_ref(options) {

    }
  },
  created() {
    this.model.entity = 'suite.amiba.modeling';
    this.model.order = "created_at";
    this.route = 'amiba/modelings';
  },
};
</script>