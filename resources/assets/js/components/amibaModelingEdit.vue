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
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>编码</label>
              <md-input v-model="model.main.code"></md-input>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20" md-flex-xlarge="20">
            <md-input-container>
              <label>名称</label>
              <md-input v-model="model.main.name"></md-input>
            </md-input-container>
          </md-layout>
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
              <md-input-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" v-model="model.main.group">
              </md-input-ref>
            </md-input-container>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="model.main.lines" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="核算要素" width="150px">
              <template scope="row">
                {{ row.element&&row.element.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref @init="init_element_ref" md-ref-id="suite.amiba.element.ref" v-model="row.element"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="匹配方" width="150px">
              <template scope="row">
                {{ row.match_group&&row.match_group.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.amiba.group.ref" v-model="row.match_group"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="匹配方向" width="150px">
              <template scope="row">
                {{ row.match_direction_enum}}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-enum md-enum-id="suite.amiba.modeling.match.direction.enum" v-model="row.match_direction_enum"></md-enum>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="业务类型" width="150px">
              <template scope="row">
                {{ row.biz_type_enum }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-enum md-enum-id="suite.cbo.biz.type.enum" v-model="row.biz_type_enum"></md-enum>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="单据类型" width="150px">
              <template scope="row">
                {{ row.doc_type&&row.doc_type.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref @init="init_doc_type_ref" md-ref-id="suite.cbo.doc.type.ref" v-model="row.doc_type"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="料品分类" width="150px">
              <template scope="row">
                {{ row.item_category&&row.item_category.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.cbo.item.category.ref" v-model="row.item_category"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="费用项目" width="150px">
              <template scope="row">
                {{ row.project_code }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input v-model="row.project_code"></md-input>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="会计科目" width="150px">
              <template scope="row">
                {{ row.account_code}}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input v-model="row.account_code"></md-input>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="客商" width="150px">
              <template scope="row">
                {{ row.trader&&row.trader.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.cbo.trader.ref" v-model="row.trader"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="物料" width="150px">
              <template scope="row">
                {{ row.item&&row.item.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.cbo.item.ref" v-model="row.item"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="因素" width="150px">
              <template scope="row">
                {{ row.factor1 }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input v-model="row.factor1"></md-input>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="取值类型" width="150px">
              <template scope="row">
                {{ row.value_type_enum }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-enum md-enum-id="suite.amiba.value.type.enum" v-model="row.value_type_enum"></md-enum>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="取值比例%" width="100px">
              <template scope="row">
                {{ row.adjust}}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input type="number" v-model="row.adjust"></md-input>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="交易方" width="150px">
              <template scope="row">
                {{ row.to_group&&row.to_group.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref md-ref-id="suite.amiba.group.ref" v-model="row.to_group"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref md-ref-id="suite.amiba.element.ref" @init="init_element_ref" ref="lineRef" @confirm="lineRefClose"></md-ref>
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
          'lines': [],
          'purpose': this.$root.userConfig.purpose,
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
      this._.forEach(datas, (v, k) => {
        this.model.main.lines.push({
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