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
        <md-button @click.native="showChart=true">关系图</md-button>
      </md-part-toolbar-group>
      <span class="flex"></span>
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex layout-column md-form">
        <md-layout md-gutter md-row>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>编码</label>
              <md-input required v-model="model.main.code"></md-input>
            </md-field>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-field>
              <label>名称</label>
              <md-input required v-model="model.main.name"></md-input>
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
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="分配方法" :md-init="init_method_ref" required md-ref-id="suite.amiba.allot.method.ref" v-model="model.main.method">
            </md-ref-input>
          </md-layout>
          <md-layout md-flex-xs="100" md-flex-sm="50" md-flex-md="33" md-flex="20">
            <md-ref-input md-label="来源核算要素" :md-init="init_element_ref" required md-ref-id="suite.amiba.element.ref" v-model="model.main.element">
            </md-ref-input>
          </md-layout>
        </md-layout>
        <md-layout class="flex">
          <md-grid :datas="fetchLineDatas" ref="grid" :row-focused="false" :auto-load="true" @onAdd="onLineAdd" :showAdd="true" :showRemove="true">
            <md-grid-column label="目的阿米巴" field="group" dataType="entity" ref-id="suite.amiba.group.ref" :ref-init="init_group_ref" width="200px"
              editable/>
            <md-grid-column label="目的核算要素" field="element" dataType="entity" ref-id="suite.amiba.element.ref" :ref-init="init_element_ref"
              width="200px" editable/>
          </md-grid>
        </md-layout>
      </md-content>
    </md-part-body>
    <md-ref :md-init="init_group_ref" md-ref-id="suite.amiba.group.ref" ref="lineRef" @confirm="lineRefClose"></md-ref>
    <md-drawer class="md-right" :md-active.sync="showChart">
      <md-toolbar class="md-transparent" md-elevation="0">
        <h3 class="md-title">分配标准关系</h3>
        <div class="flex flex-offset-5">
          <md-ref-input md-label="核算目的" required md-ref-id="suite.amiba.purpose.ref" v-model="defaultPurpose" />
        </div>
        <md-button @click.native="showChart=false" class="md-icon-button"><md-icon>close</md-icon></md-button>
      </md-toolbar>
      <md-layout>
        <md-chart ref="myChart"></md-chart>
      </md-layout>
    </md-drawer>
  </md-part>
</template>
<script>
import model from "cbo/mixins/MdModel/MdModel";
import modelGrid from "cbo/mixins/MdModel/MdModelGrid";
import _forEach from "lodash/forEach";
import Highcharts from "highcharts";
import HighchartsSankey from "highcharts/modules/sankey";
HighchartsSankey(Highcharts);

export default {
  mixins: [model, modelGrid],
  computed: {
    canSave() {
      return this.validate(true);
    }
  },
  data() {
    return {
      showChart: false,
      defaultPurpose: this.$root.configs.purpose
    };
  },
  methods: {
    validate(notToast) {
      var validator = this.$validate(this.model.main, {
        code: "required",
        name: "required",
        purpose: "required",
        method: "required",
        element: "required"
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
          code: "",
          name: "",
          purpose: this.$root.configs.purpose,
          method: null,
          element: null,
          group: null,
          lines: []
        }
      };
    },
    list() {
      this.$router.push({
        name: "module",
        params: {
          module: "amiba.allot.rule.list"
        }
      });
    },
    onLineAdd() {
      this.$refs["lineRef"].open();
    },
    lineRefClose(datas) {
      _forEach(datas, (v, k) => {
        this.$refs.grid &&
          this.$refs.grid.addDatas({
            group: v,
            element: this.model.main.element,
            rate: "0"
          });
      });
    },
    init_group_ref(options) {
      options.wheres.$leaf = {
        is_leaf: "1"
      };
      if (this.model.main.$purpose) {
        options.wheres.$purpose = {
          purpose_id: this.model.main.purpose.id
        };
      } else {
        options.wheres.$purpose = false;
      }
    },
    init_method_ref(options) {
      if (this.model.main.purpose) {
        options.wheres.$purpose = {
          purpose_id: this.model.main.purpose.id
        };
      } else {
        options.wheres.$purpose = false;
      }
    },
    init_element_ref(options) {
      if (this.model.main.purpose) {
        options.wheres.$purpose = {
          purpose_id: this.model.main.purpose.id
        };
      } else {
        options.wheres.$purpose = false;
      }
    },
    initChart() {
      var opts = {
        title: {
          text: ""
        },
        series: [
          {
            keys: ["from", "to", "weight"],
            point: {
              // 鼠标划过节点时高亮相连的连接节点
              events: {
                mouseOver: function() {
                  if (this.isNode) {
                    Highcharts.each(this.linksFrom, function(p) {
                      var newColor = new Highcharts.color(p.color).setOpacity(
                        1
                      );
                      p.graphic.element.setAttribute(
                        "fill",
                        "rgba(" + newColor.rgba.join() + ")"
                      );
                    });
                    Highcharts.each(this.linksTo, function(p) {
                      var newColor = new Highcharts.color(p.color).setOpacity(
                        1
                      );
                      p.graphic.element.setAttribute(
                        "fill",
                        "rgba(" + newColor.rgba.join() + ")"
                      );
                    });
                  }
                },
                mouseOut: function() {
                  if (this.isNode) {
                    Highcharts.each(this.linksFrom, function(p) {
                      var newColor = new Highcharts.color(p.color).setOpacity(
                        0.5
                      );
                      p.graphic.element.setAttribute(
                        "fill",
                        "rgba(" + newColor.rgba.join() + ")"
                      );
                    });
                    Highcharts.each(this.linksTo, function(p) {
                      var newColor = new Highcharts.color(p.color).setOpacity(
                        0.5
                      );
                      p.graphic.element.setAttribute(
                        "fill",
                        "rgba(" + newColor.rgba.join() + ")"
                      );
                    });
                  }
                }
              }
            },
            data: [
              ["巴A1", "巴B1", 1],
              ["巴A1", "巴B2", 1],
              ["巴B2", "巴C1", 1],
              ["巴C1", "巴D1", 1],
              ["巴C1", "巴D2", 1],
              ["巴B2", "巴C2", 1],
              ["巴B2", "巴C3", 1],
              ["巴B2", "巴C4", 1],
              ["巴A1", "巴B3", 1],
              ["巴A1", "巴C1", 1],
              ["E3", "巴C1", 1],
              ["F8", "巴D4", 1]
            ],
            type: "sankey",
            name: "Sankey demo series"
          }
        ]
      };

      this.$http
        .get("amiba/allot-rules/relations", {
          params: {
            purpose_id: this.defaultPurpose ? this.defaultPurpose.id : ""
          }
        })
        .then(res => {
          var datas = [];
          _forEach(res.data.data,function(d) {
            datas.push([d.fm_name, d.to_name, d.rate]);
          });
          opts.series[0].data = datas;

          this.$refs.myChart && this.$refs.myChart.mergeOption(opts);
        });
    }
  },
  mounted() {
    this.initChart();
  },
  created() {
    this.model.entity = "suite.amiba.allot.rule";
    this.model.order = "code";
    this.route = "amiba/allot-rules";
  }
};
</script>
<style lang="scss" scoped>
.md-drawer {
  width: 70%;
}
</style>
