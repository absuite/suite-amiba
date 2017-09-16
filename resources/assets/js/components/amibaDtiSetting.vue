<template>
  <md-part>
    <md-part-toolbar>
      <span class="flex"></span>
      <md-part-toolbar-crumbs>
        <md-part-toolbar-crumb>接口</md-part-toolbar-crumb>
        <md-part-toolbar-crumb>设置</md-part-toolbar-crumb>
      </md-part-toolbar-crumbs>
    </md-part-toolbar>
    <md-part-body class="md-transparent">
      <md-layout :md-gutter="true" class="md-padding">
        <md-layout v-for="item in datas" :key="item.id" v-if="!item.is_revoked" md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33" md-flex-large="25">
          <div class="layout flex">
            <md-card>
              <md-card-header>
                <md-card-header-text>
                  <div class="md-title">{{item.name}}</div>
                  <div class="md-subhead">地址:{{item.host||'未设置'}}</div>
                </md-card-header-text>
                <md-menu md-direction="bottom left">
                  <md-button class="md-icon-button" md-menu-trigger>
                    <md-icon>more_vert</md-icon>
                  </md-button>
                  <md-menu-content>
                    <md-menu-item @click.native="editorDti(item)">
                      <span>设置地址</span>
                      <md-icon>edit</md-icon>
                    </md-menu-item>
                  </md-menu-content>
                </md-menu>
              </md-card-header>
              <md-divider></md-divider>
              <md-card-actions>
                <span>参数变量</span>
                <span style="flex: 1"></span>
                <md-button class="md-icon-button" @click="addParam(item)">
                  <md-icon>add</md-icon>
                </md-button>
              </md-card-actions>
              <md-card-content class="no-padding">
                <md-list class="md-double-line">
                  <md-list-item v-for="p in item.params" :key="p.id">
                    <div class="md-list-text-container">
                      <span>{{p.name}}:{{p.code}}</span>
                      <p v-if="p.type_enum=='fixed'">固定值:{{p.value}}</p>
                      <p v-else-if="p.type_enum=='expression'">表达式:{{p.value}}</p>
                      <p v-else-if="p.type_enum=='input'">值来源于参数:{{p.value}}</p>
                      <p v-else>固定值:{{p.value}}</p>
                    </div>
                    <md-menu class="md-list-action" md-direction="bottom left">
                      <md-button class="md-icon-button" md-menu-trigger>
                        <md-icon>more_vert</md-icon>
                      </md-button>
                      <md-menu-content>
                        <md-menu-item @click.native="editorParam(item,p)">
                          <span>编辑</span>
                          <md-icon>edit</md-icon>
                        </md-menu-item>
                        <md-menu-item @click.native="deleteParam(item,p)">
                          <span>删除</span>
                          <md-icon>clear</md-icon>
                        </md-menu-item>
                      </md-menu-content>
                    </md-menu>
                    <md-divider class="md-inset"></md-divider>
                  </md-list-item>
                </md-list>
              </md-card-content>
            </md-card>
          </div>
        </md-layout>
      </md-layout>
      <md-dialog ref="dtiDialog">
        <md-dialog-title>接口设置</md-dialog-title>
        <md-dialog-content>
          <form>
            <md-input-container>
              <label>接口名称</label>
              <md-input v-model="currentDti.name"></md-input>
            </md-input-container>
            <md-input-container>
              <label>服务地址</label>
              <md-input v-model="currentDti.host"></md-input>
            </md-input-container>
          </form>
        </md-dialog-content>
        <md-dialog-actions>
          <md-button @click="closeDialog('dtiDialog')">取消</md-button>
          <md-button class="md-primary" @click="saveDti()">保存</md-button>
        </md-dialog-actions>
      </md-dialog>
      <md-dialog ref="paramDialog">
        <md-dialog-title>参数设置</md-dialog-title>
        <md-dialog-content>
          <form>
            <md-input-container>
              <label>编码</label>
              <md-input v-model="currentParam.code"></md-input>
            </md-input-container>
            <md-input-container>
              <label>名称</label>
              <md-input v-model="currentParam.name"></md-input>
            </md-input-container>
            <md-input-container>
              <label>类型</label>
              <md-enum md-enum-id="gmf.sys.dti.param.type.enum" v-model="currentParam.type_enum" />
            </md-input-container>
            <md-input-container>
              <label>值</label>
              <md-input v-model="currentParam.value"></md-input>
            </md-input-container>
          </form>
        </md-dialog-content>
        <md-dialog-actions>
          <md-button @click="closeDialog('paramDialog')">取消</md-button>
          <md-button class="md-primary" @click="saveParam()">保存</md-button>
        </md-dialog-actions>
      </md-dialog>
    </md-part-body>
  </md-part>
</template>
<script>
export default {
  data() {
    return {
      loading: 0,
      datas: [],
      currentDti: {},
      currentParam: {}
    };
  },
  methods: {
    loadDatas() {
      this.$http.get('sys/dti-categories', { params: { is_revoked: 0 } }).then(response => {
        this.datas = response.data.data;
      }, response => {

      });
    },
    openDialog(ref) {
      this.$refs[ref].open();
    },
    closeDialog(ref) {
      this.$refs[ref].close();
    },
    editorDti(dti) {
      this.currentDti = { id: dti.id, code: dti.code, name: dti.name, host: dti.host };
      this.$refs['dtiDialog'].open();
    },
    deleteDti(dti) {
      dti.is_revoked = true;
      this.$http.put('sys/dti-categories', dti).then(response => {}, response => {
        dti.is_revoked = false;
      });
    },
    saveDti() {
      this.$http.put('sys/dti-categories', this.currentDti).then(response => {
        this.loadDatas();
        this.$toast(this.$lang.LANG_SAVESUCCESS);
        this.closeDialog('dtiDialog');
      }, response => {

      });
    },
    addParam(dti) {
      this.currentDti = { id: dti.id, code: dti.code, name: dti.name, host: dti.host };
      this.currentParam = { code: '', name: '', value: '', type_enum: 'fixed', category_id: this.currentDti.id };
      this.$refs['paramDialog'].open();
    },
    editorParam(dti, param) {
      this.currentDti = { id: dti.id, code: dti.code, name: dti.name, host: dti.host };
      this.currentParam = { id: param.id, code: param.code, name: param.name, type_enum: param.type_enum, value: param.value, category_id: this.currentDti.id };
      this.$refs['paramDialog'].open();
    },
    deleteParam(dti, param) {
      this.$http.delete('sys/dti-params/'+param.id).then(response => {
        this.loadDatas();
        this.closeDialog('paramDialog');
        this.$toast(this.$lang.LANG_DELETESUCCESS);
      }, response => {

      });
    },
    saveParam() {
      this.$http.post('sys/dti-params', this.currentParam).then(response => {
        this.loadDatas();
        this.$toast(this.$lang.LANG_SAVESUCCESS);
        this.closeDialog('paramDialog');
      }, response => {

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