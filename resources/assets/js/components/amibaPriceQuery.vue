<template>
  <div class="layout-row layout flex layout-fill">
    <div class="layout page-menu-wraper layout-column">
      <md-toolbar class="md-dense" md-elevation="1">
        <md-menu md-size="auto" md-align-trigger>
          <md-button md-menu-trigger class="md-full" :class="{'md-primary':current_purpose}">
            <span>{{current_purpose?current_purpose.name:'核算目的'}}</span>
            <md-icon>arrow_drop_down</md-icon>
          </md-button>
          <md-menu-content>
            <md-menu-item v-for="item in data_purposes" :key="item.id" :value="item.id" @click="onPurposeChanged(item)"
              :class="{'md-primary':current_purpose&&current_purpose.id==item.id}">{{item.name}}</md-menu-item>
          </md-menu-content>
        </md-menu>
      </md-toolbar>
      <div class="layout flex  layout-column">
        <div class="list-item layout-column" @click="onPriceChanged()" :class="{'md-primary':!current_price}">
          <p>全部价表</p>
          <span class="md-body-1">查询所有价表数据</span>
        </div>
        <div v-for="item in data_prices" :key="item.id" class="list-item layout-column" @click="onPriceChanged(item)"
          :class="{'md-primary':current_price&&current_price.id==item.id}">
          <p>{{item.name}}</p>
          <span class="md-body-1">{{item.code}}</span>
        </div>
      </div>
    </div>
    <div class="layout flex layout-column">
      <md-part-toolbar>
        <span class="flex"></span>
        <md-part-toolbar-group>
          <md-fetch :fetch="doFetch"></md-fetch>
        </md-part-toolbar-group>
      </md-part-toolbar>
      <md-part-body class="no-padding">
        <md-grid :datas="fetchPriceLines" :pagerSize="50" :auto-select="false" :multiple="false" ref="mainGrid"
          :row-focused="false" :auto-load="true">
          <md-grid-column label="来源巴" width="200px">
            <template slot-scope="row" v-if="row.fm_group">
              <div class="md-body-1">{{ row.fm_group?row.fm_group.name:''}}</div>
              <div class="md-caption">{{ row.fm_group?row.fm_group.code:''}}</div>
            </template>
          </md-grid-column>
          <md-grid-column label="目标巴" width="200px">
            <template slot-scope="row" v-if="row.group">
              <div class="md-body-1">{{ row.group?row.group.name:''}}</div>
              <div class="md-caption">{{ row.group?row.group.code:''}}</div>
            </template>
          </md-grid-column>
          <md-grid-column label="料品" width="200px">
            <template slot-scope="row" v-if="row.item">
              <div class="md-body-1">{{ row.item?row.item.name:''}}</div>
              <div class="md-caption">{{ row.item?row.item.code:''}}</div>
            </template>
          </md-grid-column>
          <md-grid-column label="最新价格" width="200px">
            <template slot-scope="row">
              {{ row.cost_price}}
            </template>
          </md-grid-column>
          <md-grid-column label="历史价" width="100px">
            <template slot-scope="row">
              <md-button class="md-primary" @click="showPriceDetail(row.price_id,$event)">历史价</md-button>
            </template>
          </md-grid-column>
        </md-grid>
      </md-part-body>
    </div>
  </div>
</template>
<script>
  import _map from 'lodash/map'
  import _extend from 'lodash/extend'
  export default {
    name: "amibaPriceQuery",
    data() {
      return {
        current_purpose: null,
        current_price: null,
        data_purposes: [],
        data_prices: [],
        loading: 0,
        currentQ: ''
      };
    },
    methods: {
      create() {
        this.$router.push({
          name: 'module',
          params: {
            module: 'amiba.price.edit'
          }
        });
      },
      edit(item) {
        this.$router.push({
          name: 'id',
          params: {
            module: 'amiba.price.edit',
            id: item.id
          }
        });
      },
      showPriceDetail(priceId,event){
        event&&event.preventDefault&&event.preventDefault();
        event&&event.stopPropagation&&event.stopPropagation();
      },
      doFetch(q) {
        if (this.currentQ != q) {
          this.currentQ = q;
          this.$refs.mainGrid.refresh();
        }
        this.currentQ = q;
      },
      onPurposeChanged(item) {
        this.current_purpose = item;
      },
      onPriceChanged(item) {
        this.current_price = item;
        this.$refs.mainGrid.refresh();
      },
      fetchPriceLines({
        pager
      }) {
        if (!this.current_price) {
          return [];
        }
        const params = _extend({}, pager, {
          purpose_id: this.current_purpose ? this.current_purpose.id : "",
          price_id: this.current_price ? this.current_price : ""
        });
        return new Promise((resolve, reject) => {
          this.$http.get('amiba/prices/' + this.current_price.id + '/lines', {
            params: params
          }).then(res => {
            resolve(res)
          }, err => {
            this.$toast(err);
            reject(err)
          });
        });
      },
      fetchPrices() {
        if (!this.current_purpose) {
          this.data_prices = [];
          return;
        }
        return new Promise((resolve, reject) => {
          this.$http.get('amiba/prices', {
            params: {
              purpose_id: this.current_purpose ? this.current_purpose.id : ""
            }
          }).then(res => {
            this.data_prices = res.data.data;
            resolve()
          }, err => {
            this.$toast(err);
            reject()
          });
        });
      },
      fetchPurposes() {
        return new Promise((resolve, reject) => {
          this.$http.get('amiba/purposes').then(res => {
            this.data_purposes = res.data.data;
            resolve()
          }, err => {
            this.$toast(err);
            reject()
          });
        });
      },
    },
    mounted() {
      this.fetchPurposes().then(res => {
        if (this.data_purposes && this.data_purposes.length) {
          this.current_purpose = this.data_purposes[0];
        }
        return this.fetchPrices();
      }).then(res => {
        if (this.data_prices && this.data_prices.length) {
          this.current_price = this.data_prices[0];
        }
        return this.fetchPriceLines();
      }).then(res => {

      }).catch(err => {

      });
    }
  };
</script>
<style lang="scss" scoped>
  .page-menu-wraper {
    width: 240px;
    background: #fff;
    border-right: 1px solid #ddd;

    .md-menu {
      width: 100%;
    }

    .list-item {
      padding: 10px 16px;
      position: relative;

      &:before {
        content: ' ';
        height: 1px;
        position: absolute;
        background: #ddd;
        bottom: 0px;
        left: 0px;
        right: 0px;
      }

      &.md-primary {
        font-weight: bold;

        &:after {
          content: ' ';
          width: 2px;
          position: absolute;
          background: #0f9d58;
          top: 1px;
          right: 0px;
          bottom: 0px;
        }
      }


      &:hover {
        background-color: #ECEFF1;
        cursor: pointer;
      }

      p {
        line-height: 1.25em;
        text-overflow: ellipsis;
        margin: 0px;
      }
    }
  }
</style>