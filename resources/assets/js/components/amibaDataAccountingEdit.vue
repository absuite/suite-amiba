<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="list">列表</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>经营会计核算</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>编辑</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column">
        <md-field>
          <label>核算目的</label>
          <md-input-ref md-ref-id="suite.amiba.purpose.ref" v-model="model.main.purpose">
          </md-input-ref>
        </md-field>
        <md-field>
          <label>期间</label>
          <md-input-ref multiple @init="init_period_ref" md-ref-id="suite.cbo.period.account.ref" v-model="model.main.period">
          </md-input-ref>
        </md-field> 
        <md-field>
          <label>备注</label>
          <md-input v-model="model.main.memo"></md-input>
        </md-field>
        <md-layout>
          <md-button class="md-raised md-accent" @click.native="save" :disabled="!canSave">核算</md-button>
        </md-layout>
      </md-content>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
  export default {
    data() {
      return {
        selectedRows:[],
        model:{main:{purpose:null,period:null,memo:''}},
        loading: 0,
      };
    },
    computed: {
      canSave() {
        return this.validate(true);
      }
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
      save(){
        this.loading++;
        this.$http.post('amiba/data-accountings', this.model.main).then(response => {
            this.loading--;
            this.$toast(this.$lang.LANG_JOBSUCCESS);
        }, response => {
            this.$toast(response);
            this.loading--;
        });
      },
      list() {
        this.$router.push({ name: 'module', params: { module: 'amiba.data.accounting.list' }});
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
      
    },
  };
</script>
