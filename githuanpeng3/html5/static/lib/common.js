var obj = {};
obj.numberFormat = null;
(function(){
  var units_array = ['','十','百','千','万','十万','百万','千万','亿'];
  var numberFn = function(number,units,decimal){
    //number=数字 units＝量级1,2... decimal＝保留小数点后几位
    decimal = (typeof decimal == 'undefined')?0:decimal;
    var retStr = (number/(Math.pow(10,units))).toFixed(decimal);
    return retStr + units_array[units];
  }
  obj.numberFormat = function(number,decimal){
    var decimal = (typeof(decimal)!=null)?decimal:0;
    if(typeof parseInt(number) != 'number')
      return number;
    if(number>10000&&number<=100000000)
      return numberFn(number,4,decimal);
    else if(number>100000000)
      return numberFn(number,8,decimal);
    else
      return number;
  }
}());
obj.digitsFormat = function(num, float){
    var fixed = arguments[2] ? arguments[2] : 2;
    num = float ? parseFloat(num).toFixed(fixed) + '' : num + '';
    var tmp = num;
    var num = tmp.split('.')[0];
    var decimals = tmp.split('.')[1];
    //console.log(decimals);
    var out = num.length > 3 ? num.length % 3 : 0;
    var pre = num.slice(0, out);
    var num = num.slice(out);

    pre = out ? pre + ',' : '';
    decimals = float ? "." + decimals : '';

    return pre + num.replace(/\d{1,3}(?=(\d{3})+(\.\d*)?)/g, '$&,') + decimals;
}
obj.cookieTodo = {
  //设置cookie
  setCookie: function (cname, cvalue, exdays) {
      var d = new Date();
      d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
      var expires = "expires=" + d.toUTCString();
      console.info(cname + "=" + cvalue + "; " + expires);
      document.cookie = cname + "=" + cvalue + "; " + expires;
      console.info(document.cookie);
  },
  //获取cookie
  getCookie: function (cname) {
      var name = cname + "=";
      var ca = document.cookie.split(';');
      for (var i = 0; i < ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') c = c.substring(1);
          if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
      }
      return "";
  },
  //清除cookie
  clearCookie: function () {
      this.setCookie("username", "", -1);

  }
}
export default obj


