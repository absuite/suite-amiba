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
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-layout md-gutter>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-field>
              <label>编码</label>
              <md-input required v-model="model.main.code"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-field>
              <label>名称</label>
              <md-input required v-model="model.main.name"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-ref-input md-label="核算目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-ref-input md-label="上级核算要素" @init="initParentElementRef" md-ref-id="suite.amiba.element.ref" v-model="model.main.parent"></md-ref-input>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-field>
              <label>类型</label>
              <md-enum required md-enum-id="suite.amiba.element.type.enum" :disabled="!!model.main.parent" v-model="model.main.type_enum"></md-enum>
            </md-field>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-field>
              <label>方向</label>
              <md-enum required md-enum-id="suite.amiba.element.direction.enum" :disabled="!!model.main.parent" v-model="model.main.direction_enum"></md-enum>
            </md-field>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-field>
              <label>性质</label>
              <md-enum required md-enum-id="suite.amiba.element.factor.enum" v-model="model.main.factor_enum"></md-enum>
            </md-field>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-field>
              <label>范围</label>
              <md-enum required md-enum-id="suite.amiba.element.scope.enum" v-model="model.main.scope_enum"></md-enum>
            </md-field>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20" md-flex-xlarge="20">
            <md-checkbox required v-model="model.main.is_manual">是否人工</md-checkbox>
          </md-layout>
        </md-layout>
      </md-content>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
import model from 'gmf/core/mixins/MdModel/MdModel';
export default {
  data() {
    return {};
  },
  mixins: [model],
  computed: {
    canSave() {
      return this.validate(true);
    }
  },
  watch: {
    'model.main.parent': function(newV, oldV) {
      if (newV) {
        this.model.main.direction_enum = newV.direction_enum;
        this.model.main.type_enum = newV.type_enum;
      } else {
        this.model.main.direction_enum = '';
        this.model.main.type_enum = '';
      }
    }
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, {
        'code': 'required',
        'name': 'required',
        'purpose': 'required',
        'type_enum': 'required',
        'direction_enum': 'required',
        'factor_enum': 'required'
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
          'purpose': this.$root.userConfig.purpose,
          'parent': null,
          'type_enum': '',
          'direction_enum': '',
          'factor_enum': '',
          'scope_enum': ''
        }
      }
    },
    list() {
      this.$router.push({ name: 'module', params: { module: 'amiba.element.list' } });
    },
    initParentElementRef(options) {
      if (this.model.main.purpose) {
        options.wheres.purpose = { name: 'purpose_id', value: this.model.main.purpose.id };
      } else {
        options.wheres.purpose = false;
      }
    },
  },
  created() {
    this.model.entity = 'suite.amiba.element';
    this.model.order = "code";
    this.route = 'amiba/elements';
  },
};
</script>