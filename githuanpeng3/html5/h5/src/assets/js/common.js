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
  // 搜过过滤结果
  function filterColor(name) {
      const keyword = this.keyword
      const start = name.indexOf(keyword);
        if(start<0) {
        return name;
      }
      const end = start + this.keyword.length+1;
      let nameArr = name.split('');
      nameArr.splice(start,0,'<span class="search-key">');
      nameArr.splice(end,0,'</span>');
      return nameArr.join('');
    }
  export { setCookie,getCookie,deleteCookie,filterColor };