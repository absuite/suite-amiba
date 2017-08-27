<template>
  <md-part>
    <md-part-toolbar>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>接口</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>执行</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body class="md-transparent">
      <md-card class="md-small md-margin-top">
        <md-card-content>
          <md-layout md-gutter>
            <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="25" md-flex-large="20">
              <md-input-container>
                <label>日期</label>
                <md-date required v-model="model.date" ></md-date>
              </md-input-container>
            </md-layout>
          </md-layout>
        </md-card-content>
        <md-card-expand>
          <md-card-actions>
            <md-button class="md-primary md-raised" @click.native="runAll" :disabled="is_running>0"><md-icon>play_circle_filled</md-icon>执行</md-button>
            <span style="flex: 1"></span>
            <md-button class="md-icon-button" md-expand-trigger>
              <md-icon>keyboard_arrow_down</md-icon>
            </md-button>
          </md-card-actions>
          <md-card-content>
            <md-list class="md-dense">
              <md-list-item v-for="item in datas" :key="item.id">
                <md-avatar>
                  <md-icon>attach_file</md-icon>
                </md-avatar>
                <div class="md-list-text-container">
                  <span>{{item.name}}</span>
                  <p>{{item.category.name}}</p>
                  <p>{{item.memo}}</p>
                </div>
                <md-button class="md-list-action" @click.native="runItem(item)" :disabled="item.is_running">
                  <md-icon>play_arrow</md-icon>执行
                </md-button>
                <md-divider class="md-inset"></md-divider>
              </md-list-item>
            </md-list>
          </md-card-content>
        </md-card-expand>
      </md-card>
    </md-part-body>
  </md-part>
</template>
<script>
  export default {
    data() {
      return {
        model:{
          date:this.$root.userConfig.date,
        },
        loading: 0,
        is_running:0,
        datas:[]
      };
    },
    methods: {
      loadDatas(){
        this.$http.get('sys/dtis',{params:{date:this.model.date}}).then(response => {
            this.datas=response.data.data;
        });
      },
      runAll(){
        var dtis="";
        for (var i =this.datas.length-1; i >= 0; i--) {
          this.datas[i].is_running=true;
          if(dtis.length>0)dtis+=',';
          dtis+=this.datas[i].id;
        }
        this.doDtiRun(dtis,(error)=>{
          for (var i =this.datas.length-1; i >= 0; i--) {
            this.datas[i].is_running=false;
          }
        });
      },
      runItem(item){
        item.is_running=true;
        this.doDtiRun(item.id,(error)=> {
          item.is_running=false
        });
      },
      doDtiRun(dtis,callback){
        const datas={
          date:this.model.date,
          dtis:dtis
        };
        this.is_running++;
        this.$http.post('amiba/dtis/run',datas).then(response => {
          this.is_running--;
          if(callback)callback();
        }, response => {
          this.is_running--;
          this.$toast(response.message);
          if(callback)callback(response);
        });
      },
    },
    created() {
      
    },
    mounted() {
      this.loadDatas();
    },
  };
</script>
