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
        <md-part-toolbar-crumb>分配方法</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>编辑</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-layout md-gutter>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20"  md-flex-xlarge="20">
            <md-input-container>
              <label>编码</label>
              <md-input required v-model="model.main.code"></md-input>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20"  md-flex-xlarge="20">
            <md-input-container>
              <label>名称</label>
              <md-input required v-model="model.main.name"></md-input>
            </md-input-container>
          </md-layout>
          <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="20"  md-flex-xlarge="20">
            <md-input-container>
              <label>核算目的</label>
              <md-input-ref required md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
              </md-input-ref>
            </md-input-container>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-table-card class="flex">
            <md-table @select="onTableSelect" class="flex">
              <md-table-header>
                <md-table-row>
                  <md-table-head>阿米巴</md-table-head>
                  <md-table-head>分配基数</md-table-head>
                </md-table-row>
              </md-table-header>
              <md-table-body>
                <md-table-row v-for="(row, rowIndex) in model.main.lines" 
                  :key="rowIndex" 
                  :md-item="row" 
                  md-selection>
                  <md-table-cell>
                    {{ row.group.name}}
                  </md-table-cell>
                  <md-table-cell>
                    <md-input-container>
                      <md-input placeholder="基数" required maxlength="10" v-model="row.rate"></md-input>
                    </md-input-container>
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
    <md-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" ref="lineRef" @close="lineRefClose"></md-ref>
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
    methods: {
      validate(notToast){
        var validator=this.$validate(this.model.main,{
          'code':'required',
          'name':'required',
          purpose:'required',
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
            'code':'',
            'name':'',
            'memo':'',
            purpose:this.$root.userConfig.purpose,
            'lines':[]
          }
        }
      },
      list() {
        this.$router.push({ name: 'module', params: { module: 'amiba.allot.method.list' }});
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
        this._.forEach(this.selectedRows,(v,k)=>{
          var idx=this.model.main.lines.indexOf(v);
          if(idx>=0){
            this.model.main.lines.splice(idx,1);
          }
        });
      },
      lineRefClose(datas){
        this._.forEach(datas,(v,k)=>{
          this.model.main.lines.push({group:v,rate:0});
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
      
    },
    created() {
      this.model.entity='suite.amiba.allot.method';
      this.model.order="code";
      this.route='amiba/allot-methods';
    },
  };
</script>
