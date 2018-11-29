export default function formatDecimal(num, options) {
  //precision:精度，保留的小数位数
  //unit:单位，0个，1十，2百，3千
  //quantile:分位数，默认3，表示千分位
  options = options || {}
  if (!options.precision) options.precision = 2;
  if (!options.unit) options.unit = 0;
  if (!options.quantile) options.quantile = 3;

  num = parseFloat(num);
  if (options.unit) {
    num = num / Math.pow(10, options.unit);
  }
  var vv = Math.pow(10, options.precision);
  num = Math.round(num * vv) / vv;

  const groups = (/([\-\+]?)(\d*)(\.\d+)?/g).exec('' + num);
  // 获取符号(正/负数)
  const sign = groups[1];
  //整数部分
  const integers = (groups[2] || "").split("");
  // 求出小数位数值
  var cents = groups[3] || ".0";
  while (cents.length <= options.precision) {
    cents = cents + '0';
  }
  cents = options.precision ? cents.substring(0, options.precision + 1) : '';
  var temp = integers.join('');
  if (options.quantile > 0) {
    var remain = integers.length % options.quantile;
    temp = integers.reduce(function (previousValue, currentValue, index) {
      if (index + 1 === remain || (index + 1 - remain) % options.quantile === 0) {
        return previousValue + currentValue + ",";
      } else {
        return previousValue + currentValue;
      }
    }, "").replace(/\,$/g, "");
  }

  const rtn = sign + temp + cents;
  if (options.quantile < 1) {
    return parseFloat(rtn);
  }
  return rtn;
}