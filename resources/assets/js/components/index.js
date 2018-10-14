import amibaElementEdit from './amibaElementEdit.vue';
import amibaElementList from './amibaElementList.vue';

import amibaGroupEdit from './amibaGroupEdit.vue';
import amibaGroupList from './amibaGroupList.vue';

import amibaPurposeEdit from './amibaPurposeEdit.vue';
import amibaPurposeList from './amibaPurposeList.vue';

import amibaDtiModelingEdit from './amibaDtiModelingEdit.vue';
import amibaDtiModelingList from './amibaDtiModelingList.vue';

import amibaAllotMethodEdit from './amibaAllotMethodEdit.vue';
import amibaAllotMethodList from './amibaAllotMethodList.vue';

import amibaAllotRuleEdit from './amibaAllotRuleEdit.vue';
import amibaAllotRuleList from './amibaAllotRuleList.vue';

import amibaDtiLog from './amibaDtiLog.vue';
import amibaDtiRun from './amibaDtiRun.vue';

import amibaModelingEdit from './amibaModelingEdit.vue';
import amibaModelingList from './amibaModelingList.vue';

import amibaDataInitEdit from './amibaDataInitEdit.vue';
import amibaDataInitList from './amibaDataInitList.vue';

import amibaDataTimeEdit from './amibaDataTimeEdit.vue';
import amibaDataTimeList from './amibaDataTimeList.vue';

import amibaDataDocEdit from './amibaDataDocEdit.vue';
import amibaDataDocList from './amibaDataDocList.vue';

import amibaDataCloseEdit from './amibaDataCloseEdit.vue';
import amibaDataCloseList from './amibaDataCloseList.vue';

import amibaDataAccountingEdit from './amibaDataAccountingEdit.vue';
import amibaDataAccountingList from './amibaDataAccountingList.vue';

import amibaDataDistributeEdit from './amibaDataDistributeEdit.vue';
import amibaDataDistributeList from './amibaDataDistributeList.vue';

import amibaPriceEdit from './amibaPriceEdit.vue';
import amibaPriceList from './amibaPriceList.vue';
import amibaPriceQuery from './amibaPriceQuery.vue';

import amibaPriceAdjustEdit from './amibaPriceAdjustEdit.vue';
import amibaPriceAdjustList from './amibaPriceAdjustList.vue';

import amibaDataTargetEdit from './amibaDataTargetEdit.vue';
import amibaDataTargetList from './amibaDataTargetList.vue';

import amibaDataAdjustEdit from './amibaDataAdjustEdit.vue';
import amibaDataAdjustList from './amibaDataAdjustList.vue';

import amibaQueryAccount from './amibaQueryAccount.vue';
import amibaQueryProfit from './amibaQueryProfit.vue';
import amibaQueryDocBiz from './amibaQueryDocBiz.vue';
import amibaQueryDocFi from './amibaQueryDocFi.vue';

import amibaReportGroupTrendAns from './amibaReportGroupTrendAns.vue';
import amibaReportGroupCompareAns from './amibaReportGroupCompareAns.vue';
import amibaReportGroupAnalogyAns from './amibaReportGroupAnalogyAns.vue';
import amibaReportGroupRankAns from './amibaReportGroupRankAns.vue';
import amibaReportGroupPurposeTrend from './amibaReportGroupPurposeTrend.vue';
import amibaReportGroupPurposeCompare from './amibaReportGroupPurposeCompare.vue';
import amibaReportStatementFunctionAns from './amibaReportStatementFunctionAns.vue';
import amibaReportStatementDevoteAns from './amibaReportStatementDevoteAns.vue';
import amibaReportStatementCompare from './amibaReportStatementCompare.vue';
import amibaReportStatementTrend from './amibaReportStatementTrend.vue';
import amibaReportStatementPurpose from './amibaReportStatementPurpose.vue';
import AmibaDtiModelingPrice from './amibaDtiModelingPrice.vue';


export default function install(Vue) {
  Vue.component('amibaElementEdit', amibaElementEdit);
  Vue.component('amibaElementList', amibaElementList);

  Vue.component('amibaGroupEdit', amibaGroupEdit);
  Vue.component('amibaGroupList', amibaGroupList);

  Vue.component('amibaPurposeEdit', amibaPurposeEdit);
  Vue.component('amibaPurposeList', amibaPurposeList);

  Vue.component('amibaDtiModelingEdit', amibaDtiModelingEdit);
  Vue.component('amibaDtiModelingList', amibaDtiModelingList);

  Vue.component('amibaDtiLog', amibaDtiLog);
  Vue.component('amibaDtiRun', amibaDtiRun);

  Vue.component('amibaAllotMethodEdit', amibaAllotMethodEdit);
  Vue.component('amibaAllotMethodList', amibaAllotMethodList);

  Vue.component('amibaAllotRuleEdit', amibaAllotRuleEdit);
  Vue.component('amibaAllotRuleList', amibaAllotRuleList);

  Vue.component('amibaModelingEdit', amibaModelingEdit);
  Vue.component('amibaModelingList', amibaModelingList);

  Vue.component('amibaDataInitEdit', amibaDataInitEdit);
  Vue.component('amibaDataInitList', amibaDataInitList);

  Vue.component('amibaDataTimeEdit', amibaDataTimeEdit);
  Vue.component('amibaDataTimeList', amibaDataTimeList);

  Vue.component('amibaDataDocEdit', amibaDataDocEdit);
  Vue.component('amibaDataDocList', amibaDataDocList);

  Vue.component('amibaDataCloseEdit', amibaDataCloseEdit);
  Vue.component('amibaDataCloseList', amibaDataCloseList);

  Vue.component('amibaDataAccountingEdit', amibaDataAccountingEdit);
  Vue.component('amibaDataAccountingList', amibaDataAccountingList);

  Vue.component('amibaDataDistributeEdit', amibaDataDistributeEdit);
  Vue.component('amibaDataDistributeList', amibaDataDistributeList);

  Vue.component('amibaPriceEdit', amibaPriceEdit);
  Vue.component('amibaPriceList', amibaPriceList);
  Vue.component(amibaPriceQuery.name, amibaPriceQuery);

  Vue.component('amibaPriceAdjustEdit', amibaPriceAdjustEdit);
  Vue.component('amibaPriceAdjustList', amibaPriceAdjustList);

  Vue.component('amibaDataTargetEdit', amibaDataTargetEdit);
  Vue.component('amibaDataTargetList', amibaDataTargetList);

  Vue.component('amibaDataAdjustEdit', amibaDataAdjustEdit);
  Vue.component('amibaDataAdjustList', amibaDataAdjustList);

  Vue.component('amibaQueryAccount', amibaQueryAccount);
  Vue.component('amibaQueryProfit', amibaQueryProfit);
  Vue.component('amibaQueryDocFi', amibaQueryDocFi);
  Vue.component('amibaQueryDocBiz', amibaQueryDocBiz);

  Vue.component('amibaReportGroupTrendAns', amibaReportGroupTrendAns);
  Vue.component('amibaReportGroupCompareAns', amibaReportGroupCompareAns);
  Vue.component('amibaReportGroupAnalogyAns', amibaReportGroupAnalogyAns);
  Vue.component('amibaReportGroupRankAns', amibaReportGroupRankAns);

  Vue.component('amibaReportGroupPurposeTrend', amibaReportGroupPurposeTrend);
  Vue.component('amibaReportGroupPurposeCompare', amibaReportGroupPurposeCompare);

  Vue.component('amibaReportStatementFunctionAns', amibaReportStatementFunctionAns);
  Vue.component('amibaReportStatementDevoteAns', amibaReportStatementDevoteAns);
  Vue.component('amibaReportStatementCompare', amibaReportStatementCompare);
  Vue.component('amibaReportStatementTrend', amibaReportStatementTrend);
  Vue.component('amibaReportStatementPurpose', amibaReportStatementPurpose);

  Vue.component('AmibaDtiModelingPrice', AmibaDtiModelingPrice);

}