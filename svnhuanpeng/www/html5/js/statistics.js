
var conf = {};
(function(){
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
    console.log(env);
    conf = {
        getConf:function(){
            return{
                domain : _domain,
                cookiepath:cookiepath
            }
        }
    }
}());
var $conf = conf.getConf();
var getUrlParams = function (){
	var b = location.href;
	var c = new Object();
	if (b.indexOf('?') > -1) {
	    var str = b.substr(b.indexOf('?') + 1);
	    var strs = str.split("&");
	    for (var i = 0; i < strs.length; i++) {
	        c[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
	    }
	}
	return c;
};
var setChannelCookie = function(name, value){
    var exp = new Date();
    exp.setTime( exp.getTime() + 30 * 24 * 3600 * 1000 );
    // 先序模式
    if(getCookie(name)){
        return;
    }else{
        document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString() + "; path="+$conf.cookiepath+";domain=" + document.domain + ";";
    }
};
var getCookie = function(name){
    var a  = document.cookie.match(new RegExp("(?:^|;)\\s*" + name + "=([^;]*)"));
    return (a) ? decodeURIComponent(a[1]) : null;
};
var initChannelUrl = function (){
	if(getUrlParams()._hp_promo_code){
		setChannelCookie('promo_code', getUrlParams()._hp_promo_code);
	}
	if(getUrlParams()._hp_channel){
		setChannelCookie('channelID', getUrlParams()._hp_channel);
	}
};
initChannelUrl();

