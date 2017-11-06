/**
 * Created by logoyoung on 16/11/15.
 */
define('UA',function(){
    var ua = {
        //版本
        versions: function() {
            var e = navigator.userAgent;
            //t = navigator.appVersion;
            return {
                trident: e.indexOf("Trident") > -1,
                presto: e.indexOf("Presto") > -1,
                webKit: e.indexOf("AppleWebKit") > -1,
                gecko: e.indexOf("Gecko") > -1 && e.indexOf("KHTML") == -1,
                mobile: !!e.match(/AppleWebKit.*Mobile.*/),
                ios: !!e.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
                android: e.indexOf("Android") > -1 || e.indexOf("Linux") > -1,
                iPhone: e.indexOf("iPhone") > -1,
                iPad: e.indexOf("iPad") > -1,
                Pad: e.indexOf("Pad") > -1,
                webApp: e.indexOf("Safari") == -1,
                weixin: e.indexOf("MicroMessenger") > -1,
                qq: !!function(){
                    var qq = e.match(/\sQQ/i);
                    qq =  qq!= null ? qq : [''];
                    return qq[0].toLowerCase()==' qq';
                }()
            }
        } (),
        //语言
        language: (navigator.browserLanguage || navigator.language).toLowerCase()
        //其他属性
        //todo

    };
    return ua;
})
