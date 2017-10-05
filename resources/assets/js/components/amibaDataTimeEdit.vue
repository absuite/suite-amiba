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
        <md-part-toolbar-crumb>时间数据</md-part-toolbar-crumb>
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
                  <md-table-head>阿米巴</md-table-head>
                  <md-table-head>正常工作时间</md-table-head>
                  <md-table-head>加班时间</md-table-head>
                  <md-table-head>总劳动时间</md-table-head>
                </md-table-row>
              </md-table-header>
              <md-table-body>
                <md-table-row v-for="(row, rowIndex) in model.main.lines" 
                  :key="rowIndex" 
                  :md-item="row" 
                  md-selection>
                  <md-table-cell>
                    <md-input-container>
                      <md-input-ref md-ref-id="suite.amiba.group.ref" v-model="row.group"></md-input-ref>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input v-model="row.nor_time"></md-input>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input v-model="row.over_time"></md-input>
                    </md-input-container>
                  </md-table-cell>
                  <md-table-cell>
                    {{ row.total_time }}
                  </md-table-cell>
                </md-table-row>
              </md-table-body>
            </md-table>
            <md-table-tool>
              <md-table-action 
                md-insert
                @onAdd="onLineAdd"
                @onRemove="onLineRemove"
                ></md-table-action>
              <md-layout class="flex"></md-layout>
              <md-table-pagination
                  md-size="5"
                  md-total="10"
                  md-page="1"
                  md-label="Rows"
                  md-separator="of"
                  :md-page-options="[5, 10, 25, 50]"
                  @pagination="onTablePagination">
              </md-table-pagination>
            </md-table-tool>
          </md-table-card>
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
        return this.validate(true);
      }
    },
    watch:{
      'model.main.lines':{
        handler:function(nowVal,oldVal){},
        deep:true
      },
    },
    methods: {
      validate(notToast){
        var validator=this.$validate(this.model.main,{'purpose':'required','period':'required'});
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
            'memo':'',
            'lines':[]
          }
        }
      },
      list() {
        this.$router.push({ name: 'module', params: { module: 'amiba.data.time.list' }});
      },
      onTablePagination(page){
         
      },
      onTableSelect(items){
        this.selectedRows=[];
        Object.keys(items).forEach((row, index) =>{
          this.selectedRows[index]=items[row];
        });
      },
      onLineAdd(){
        this.$refs['lineRef'].open();
      },
      onLineRemove(){
        this.model.main.lines=this.model.main.lines||[];
        this._.forEach(this.selectedRows,(v,k)=>{
          var idx=this.model.main.lines.indexOf(v);
          if(idx>=0){
            this.model.main.lines.splice(idx,1);
          }
        });
      },
      lineRefClose(datas){
        this.model.main.lines=this.model.main.lines||[];
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
      this.model.entity='suite.amiba.data.time';
      this.model.order="created_at";
      this.route='amiba/data-times';
    },
  };
</script>
