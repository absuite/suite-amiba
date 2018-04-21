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
        <md-file-import md-entity="Suite\Amiba\Models\Modeling"  template="/assets/vendor/suite-cbo/files/suite.amiba.modeling.xlsx"></md-file-import>
      </md-part-toolbar-group>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column md-form">
        <md-layout md-gutter md-row>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>编码</label>
              <md-input v-model="model.main.code"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>名称</label>
              <md-input v-model="model.main.name"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="核算目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="阿米巴" :md-init="init_group_ref" md-ref-id="suite.amiba.group.ref" v-model="model.main.group">
            </md-ref-input>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="fetchLineDatas" ref="grid" :row-focused="false" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="核算要素" field="element" dataType="entity" ref-id="suite.amiba.element.ref" :ref-init="init_element_ref" editable/>
            <md-grid-column label="匹配方" field="match_group" dataType="entity" ref-id="suite.amiba.group.ref" :ref-init="init_group_ref" editable/>
            <md-grid-column label="匹配方向" field="match_direction_enum" dataType="enum" editable ref-id="suite.amiba.modeling.match.direction.enum" />
            <md-grid-column label="业务类型" field="biz_type_enum" dataType="enum" editable ref-id="suite.cbo.biz.type.enum" />
            <md-grid-column label="单据类型" field="doc_type" dataType="entity" ref-id="suite.cbo.doc.type.ref" :ref-init="init_doc_type_ref" editable/>
            <md-grid-column label="料品分类" field="item_category" dataType="entity" ref-id="suite.cbo.item.category.ref" editable/>
            <md-grid-column label="费用项目" field="project_code" ref-id="suite.amiba.modeling.project.code.ref" ref-type="text" editable/>
            <md-grid-column label="会计科目" field="account_code" ref-id="suite.amiba.modeling.account.code.ref" ref-type="text" editable/>
            <md-grid-column label="客商" field="trader" dataType="entity" ref-id="suite.cbo.trader.ref" editable/>
            <md-grid-column label="物料" field="item" dataType="entity" ref-id="suite.cbo.item.ref" editable/>
            <md-grid-column label="因素1" field="factor1" ref-id="suite.amiba.modeling.factor1.ref" ref-type="text" editable/>
            <md-grid-column label="因素2" field="factor2" ref-id="suite.amiba.modeling.factor2.ref" ref-type="text" editable/>
            <md-grid-column label="因素3" field="factor3" ref-id="suite.amiba.modeling.factor3.ref" ref-type="text" editable/>
            <md-grid-column label="因素4" field="factor4" ref-id="suite.amiba.modeling.factor4.ref" ref-type="text" editable/>
            <md-grid-column label="因素5" field="factor5" ref-id="suite.amiba.modeling.factor5.ref" ref-type="text" editable/>
            <md-grid-column label="取值类型" field="value_type_enum" dataType="enum" editable ref-id="suite.amiba.value.type.enum" />
            <md-grid-column label="取值比例%" field="adjust" editable/>
            <md-grid-column label="交易方" field="to_group" dataType="entity" ref-id="suite.amiba.group.ref" :ref-init="init_group_ref" editable/>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref md-ref-id="suite.amiba.element.ref" :md-init="init_element_ref" ref="lineRef" @confirm="lineRefClose"></md-ref>
  </md-part>
</template>
<script>
import model from 'cbo/mixins/MdModel/MdModel';
import modelGrid from 'cbo/mixins/MdModel/MdModelGrid';
import _forEach from 'lodash/forEach'
export default {
  mixins: [model, modelGrid],
  computed: {
    canSave() {
      return this.validate(true);
    }
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, { 'purpose': 'required' });
      var fail = validator.fails();
      if (fail && !notToast) {
        this.$toast(validator.errors.all());
      }
      return !fail;
    },
    initModel() {
      return {
        main: {
          'purpose': this.$root.configs.purpose,
          'group': null,
          'code': '',
          'name': ''
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.modeling.list' } });
    },
    onLineAdd() {
      this.$refs['lineRef'].open();
    },
    lineRefClose(datas) {
      _forEach(datas, (v, k) => {
        this.$refs.grid && this.$refs.grid.addDatas({
          'element': v,
          'biz_type_enum': '',
          'match_direction_enum': 'fm',
          'match_group': this.model.main.group,
          'to_group': null,
          'value_type_enum': 'amt',
          'adjust': '100'
        });
      });
    },
    init_group_ref(options) {
      options.wheres.$leaf = { 'is_leaf': '1' };
      if (this.model.main.purpose) {
        options.wheres.$purpose = { 'purpose_id': this.model.main.purpose.id };
      } else {
        options.wheres.$purpose = false;
      }
    },
    init_element_ref(options) {
      options.wheres.$leaf = { 'is_leaf': '1' };
      if (this.model.main.purpose) {
        options.wheres.$purpose = { 'purpose_id': this.model.main.purpose.id };
      } else {
        options.wheres.$purpose = false;
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