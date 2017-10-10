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
        <md-part-toolbar-crumb>期初</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>编辑</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-layout md-gutter>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20"  md-flex-xlarge="20">
            <md-input-container>
              <label>核算目的</label>
              <md-input-ref required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20"  md-flex-xlarge="20">
            <md-input-container>
              <label>期间</label>
              <md-input-ref @init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period">
              </md-input-ref>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20"  md-flex-xlarge="20">
            <md-input-container>
              <label>币种</label>
              <md-input-ref required md-ref-id="suite.cbo.currency.ref" v-model="model.main.currency">
              </md-input-ref>
            </md-input-container>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="model.main.lines" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="阿米巴" width="300px">
              <template scope="row">
                {{ row.group&&row.group.name||'' }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" v-model="row.group"></md-input-ref>
                </md-input-container>
              </template>
            </md-grid-column>
            <md-grid-column label="利润" width="150px">
              <template scope="row">
                {{ row.profit }}
              </template>
              <template slot="editor" scope="row">
                <md-input-container>
                  <md-input v-model="row.profit"></md-input>
                </md-input-container>
              </template>
            </md-grid-column>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" ref="lineRef" @confirm="lineRefClose"></md-ref>
  </md-part>
</template>
<script>
  import model from '../../gmf-sys/core/mixin/model';
  export default {
    data() {
      return {
        selectedRows:[],
      };
    },
    mixins: [model],
    computed: {
      canSave() {
        return true;//this.validate(true);
      }
    },
    methods: {
      validate(notToast){
        var validator=this.$validate(this.model.main,{
          'purpose':'required',
          'period':'required',
          'currency':'required'
        });
        var fail=validator.fails();
        if(fail&&!notToast){
          this.$toast(validator.errors.all());
        }
        return !fail;
      },
      initModel(){
        return {
          main:{
            'purpose':this.$root.userConfig.purpose,
            'period':this.$root.userConfig.period,
            'currency':this.$root.userConfig.currency,
            'lines':[]
          }
        }
      },
      list() {
        this.$router.push({ name: 'module', params: { module: 'amiba.data.init.list' }});
      },
      onLineAdd(){
        this.$refs['lineRef'].open();
      },
      lineRefClose(datas){
        this._.forEach(datas,(v,k)=>{
          this.model.main.lines.push({group:v});
        });
      },
      init_group_ref(options){
        options.wheres.leaf={name:'is_leaf',value:'1'};
        if(this.model.main.purpose){
          options.wheres.purpose={name:'purpose_id',value:this.model.main.purpose.id};
        }else{
          options.wheres.purpose=false;
        }
      },
      init_period_ref(options){
        if(this.model.main.purpose&&this.model.main.purpose.calendar_id){
          options.wheres.calendar={name:'calendar_id',value:this.model.main.purpose.calendar_id};
        }else{
          options.wheres.calendar={name:'calendar_id',value:this.$root.userConfig.calendar.id};
        }
      },
    },
    created() {
      this.model.entity='suite.amiba.data.init';
      this.model.order="created_at";
      this.route='amiba/data-inits';
    },
  };
</script>
