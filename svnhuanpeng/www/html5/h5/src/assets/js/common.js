var _protocol = location.protocol+'//';

var env = 'pro';
var _domain = _protocol + 'www.huanpeng.com/';
var cookiepath = '/';

if( document.domain == 'dev.huanpeng.com' )
{
    env = 'dev';
    _domain = _protocol + document.domain + '/';
    cookiepath = '/';
}
else if( document.domain == 'pre.huanpeng.com')
{
    env = 'pre';
    _domain = _protocol + document.domain + '/';
    cookiepath = '/';
}



//设置cookie
  function setCookie(name, value) {
      var exp = new Date();
      exp.setTime( exp.getTime() + 30 * 24 * 3600 * 1000 );
      document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString() + "; path="+cookiepath+";domain=" + document.domain + ";";
  }
  //获取cookie
  function getCookie(name) {
      var a  = document.cookie.match(new RegExp("(?:^|;)\\s*" + name + "=([^;]*)"));
      return (a) ? decodeURIComponent(a[1]) : null;
  }
  //清除cookie
  function deleteCookie(name) {
      var exp = new Date();
      exp.setTime(exp.getTime() - 3600);

      var cval = getCookie(name);
      if(cval != null){
        document.cookie = name + "=" + encodeURIComponent(cval) + ";expires=" + exp.toGMTString() + "; path="+cookiepath+";domain=" + document.domain + ";";
    }
  }

  export { setCookie,getCookie,deleteCookie };