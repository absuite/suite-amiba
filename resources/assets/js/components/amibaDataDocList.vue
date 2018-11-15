<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="create">新增</md-button>
        <md-button @click.native="remove" :disabled="!(selectRows&&selectRows.length)">删除</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-file-import md-entity="Suite\Amiba\Models\DataDoc" template="/assets/vendor/suite-cbo/files/suite.amiba.data.doc.xlsx"></md-file-import>
      </md-part-toolbar-group>
      <span class="flex"></span>
      <md-part-toolbar-group>
        <div class="layout-row">
          <md-ref-input md-label="目的" required md-ref-id="suite.amiba.purpose.ref" v-model="model.purpose"></md-ref-input>
          <md-ref-input md-label="期间" :md-init="init_period_ref" required md-ref-id="suite.cbo.period.account.ref" v-model="model.period"></md-ref-input>
          <md-ref-input md-label="模型" :md-init="init_modeling_ref" required md-ref-id="suite.amiba.modeling.ref" v-model="model.modeling"></md-ref-input>
          <md-ref-input md-label="要素" :md-init="init_element_ref" required md-ref-id="suite.amiba.element.ref" v-model="model.element"></md-ref-input>
          <md-fetch :fetch="doFetch"></md-fetch>
        </div>
      </md-part-toolbar-group>
    </md-part-toolbar>
    <md-part-body class="no-padding">
      <md-query @select="select" @dblclick="edit" :md-init="initQuery" ref="list" md-query-id="suite.amiba.data.doc.list"></md-query>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
import _map from "lodash/map";
export default {
  data() {
    return {
      selectRows: [],
      loading: 0,
      model: {
        purpose: this.$root.configs.purpose,
        period: this.$root.configs.period,
        modeling: null,
        element: null
      }
    };
  },
  watch: {
    "model.purpose"() {
      this.loadData(1);
    },
    "model.period"() {
      this.loadData(1);
    },
    "model.modeling"() {
      this.loadData(1);
    },
    "model.element"() {
      this.loadData(1);
    }
  },
  methods: {
    create() {
      this.$router.push({
        name: "module",
        params: {
          module: "amiba.data.doc.edit"
        }
      });
    },
    edit(item) {
      this.$router.push({
        name: "id",
        params: {
          module: "amiba.data.doc.edit",
          id: item.id
        }
      });
    },
    doFetch(q) {
      if (this.currentQ != q) {
        this.currentQ = q;
        this.loadData();
      }
      this.currentQ = q;
    },
    initQuery(options) {
      options.wheres.$purpose = this.model.purpose
        ? { purpose_id: this.model.purpose.id }
        : false;
      options.wheres.$period = this.model.period
        ? { period_id: this.model.period.id }
        : false;
      options.wheres.$modeling = this.model.modeling
        ? { modeling_id: this.model.modeling.id }
        : false;
        options.wheres.$element = this.model.element
        ? { element_id: this.model.element.id }
        : false;

      options.wheres.$filter = false;
      if (this.currentQ) {
        options.wheres.$filter = {
          or: [
            {
              like: {
                doc_no: this.currentQ
              }
            },
            {
              like: {
                "fm_group.name": this.currentQ
              }
            },
            {
              like: {
                "period.name": this.currentQ
              }
            }
          ]
        };
      }
    },
    remove() {
      if (!this.selectRows || !this.selectRows.length) {
        this.$toast(this.$lang.LANG_NODELETEDATA);
        return;
      }
      this.loading++;
      const ids = _map(this.selectRows, "id").toString();
      this.$http.delete("amiba/data-docs/" + ids).then(
        response => {
          this.loadData();
          this.loading--;
          this.$toast(this.$lang.LANG_DELETESUCCESS);
        },
        response => {
          this.$toast(response);
          this.loading--;
        }
      );
    },
    select(items) {
      this.selectRows = items;
    },
    loadData() {
      this.$refs.list.pagination(1);
    },
    init_period_ref(options) {
      if (this.model.purpose && this.model.purpose.calendar_id) {
        options.wheres.$calendar = {
          calendar_id: this.model.purpose.calendar_id
        };
      } else {
        options.wheres.$calendar = false;
      }
    },
    init_modeling_ref(options) {
      if (this.model.purpose) {
        options.wheres.$purpose = {
          purpose_id: this.model.purpose.id
        };
      } else {
        options.wheres.$purpose = false;
      }
    },
    init_element_ref(options) {
      if (this.model.purpose) {
        options.wheres.$purpose = { purpose_id: this.model.purpose.id };
      } else {
        options.wheres.$purpose = false;
      }
    }
  }
};
</script>