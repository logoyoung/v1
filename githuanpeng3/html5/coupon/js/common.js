var conf = {};
(function () {
    var n = location.protocol + "//";
    var p = "pro";
    var l = n + "www.huanpeng.com/";
    var r = "/";
    if(document.domain == 'www.huanpeng.com'){
    	p = 'pro';
        l = n + document.domain + "/";
        r = "/"
    }else if (document.domain == "dev.huanpeng.com") {
        p = "dev";
        l = n + document.domain + "/";
        r = "/"
    } else if (document.domain == "pre.huanpeng.com"){
        p = "pre";
        l = n + document.domain + "/";
        r = "/"
    }else {
        p = "other";
        l = n + document.domain + "/";
        r = "/"
    }
    var m = l + "api/";
    var k = l + "personal/";
    console.log(p);
    var q = [];
    q.dev = n + "dev-img.huanpeng.com";
    q.pre = n + "pre-img.huanpeng.com";
    q.pro = n + "img.huanpeng.com";
    var j = {};
    var o = 2 * 1024 * 1024;
    conf = {
        getConf: function () {
            return {
                angleImage: "angle_class",
                domain: l,
                api: m,
                img: q[p],
                person: k,
                pushRoomID: 1,
                maxUid: 3000000000,
                group: {
                    own: 5,
                    admin: 4,
                    user: 1
                },
                taskUrl: {
                    6: k,
                    12: k + "mp/certify_email/",
                    30: k + "recharge.php",
                    36: l + "download.php"
                },
                modifyNickCost: 600,
                uploadImgSize: o,
                video: {
                    WAIT: 0,
                    CHECK: 1,
                    PUBLISH: 2
                },
                defaultFace: q[p] + "/5/e/5e49f1310263dae8f0bc3f484860f2ad.png",
                defaultUserPic: l + "static/img/userface.png",
                certStatus: {
                    mail: {
                        not: 0,
                        wait: 1,
                        pass: 2
                    },
                    phone: {
                        not: 0,
                        pass: 1
                    },
                    ident: {
                        not: 0,
                        wait: 1,
                        unpass: 100,
                        pass: 101
                    },
                    bank: {
                        not: 0,
                        wait: 1,
                        unpass: 100,
                        pass: 101
                    }
                },
                cookie: ["_uid", "_enc", "_uinfo", "_uproperty", "_unick", "_uface", "_loginway"],
                isIE: navigator.appVersion.indexOf("MSIE") > 0 || navigator.appVersion.indexOf("Trident/7.0") > 0,
                isIE7: navigator.appVersion.indexOf("MSIE 7.0") > 0,
                isFF: navigator.appVersion.indexOf("Firefox") > 0,
                cookiepath: r
            }
        }
    }
}());
var $conf = conf.getConf();
 
function ajaxRequest(f, g, i, j) {
    var h = {
        url: "",
        type: "post",
        dataType: "json",
        success: function (a) {
            if (a.status == 1) {
                g && typeof g == "function" && g(a.content)
            } else {
                i && typeof i == "function" && i(a.content)
            }
        },
        error: function (a) {
            j && typeof j == "function" && j();
            checkStatus(a)
        }
    };
    return $.ajax($.extend({}, h, f))
}
function checkStatus(b) {
    if (b.code == "-30008" || b.code == "-30009") {
        location.href = $conf.domain + "oauth.php?err=帐号被封禁"
    } else {
        if (b.code == "-1013") {
            logout_submit()
        }
    }
};