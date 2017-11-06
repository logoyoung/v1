var Share;
(function(){
    var a = jQuery;
    var u = {
        'tsina':'http://service.weibo.com/share/share.php?',
        //'tqq':'http://v.t.qq.com/share/share.php?',
        'tqq':'http://connect.qq.com/widget/shareqq/index.html?',
        'tqzone':'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?'
    };
    var defaults = {
        tqq: {
            url: '',
            sumary: '',
            desc: '',
            title: '',
            //pic: '',

        },
        tsina: {
            url: '',
            title: '',//document.title,//分享的文字内容
            count: '0',
            sumary: '',//摘要
            desc: '',//qq空间的主要描述（发布理由）
            language: 'zh_cn',
            searchPic: 'true',
            rnd: new Date().valueOf(),
            site: '',//来源 腾讯
            pic: ''
        },
        tqzone: {
            url: '',
            title: '',//document.title,//分享的文字内容
            count: '0',
            sumary: '',//摘要
            desc: '',//qq空间的主要描述（发布理由）
            language: 'zh_cn',
            searchPic: 'true',
            rnd: new Date().valueOf(),
            site: '',//来源 腾讯
            pic: ''
        },
        wx:{
            url: '',
            title: '',//document.title,//分享的文字内容
            count: '0',
            sumary: '',//摘要
            desc: '',//qq空间的主要描述（发布理由）
            language: 'zh_cn',
            searchPic: 'true',
            rnd: new Date().valueOf(),
            site: '',//来源 腾讯
            pic: ''
        }

    };
    Share = {
        options:{
        },
        init:function(option,c){
            var self = this;
            self.options = a.extend(defaults[c], option);
            console.log(self.options);
            var p = self.options;
            var tmp = [];
            for(v in p){
                tmp.push(v + '=' + encodeURIComponent(p[v] || ''));
            }

            if(c.channel == 'wx'){
                self.wx_qrcode(c.top, c.left);
            }else{
                var url = u[c.channel] + tmp.join('&');
                window.open(url);
            }
        },
        wx_qrcode:function(top,left){
            var self = this;
            if(self.qrcode)
                self.qrcode.remove();

            self.qrcode = a('<div/>',{
                'class':'wx_share_dialog'
            }).css({
                'z-index':'10001',
                'background-color':'#fff',
                'position':'absolute',
                'border':'1px solid #fefefe',
                'padding':'10px',
                'left':left,
                'top':top
            }).appendTo(document.body);
            a('.wx_share_dialog').append('<div class="wx_share_dialog_head"> <span>分享到微信朋友圈</span> <a href="javascript:;" class="wx_share_dialog_close">X</a> </div><div id="wx_qrcode"></div>')
            a('.wx_share_dialog_head').css({
                'font-size':'12px',
                'font-weight':'bold',
                'position':'relative',
                'color':'#000',
                'text-align':'left',
                'height':'16px'
            });
            a('.wx_share_dialog_close').css({
                'width':'16px',
                'height':'16px',
                'position':'absolute',
                'right':'0',
                'top':'0',
                'color':'#999',
                'text-decoration':'none',
                'font-size':'16px'
            });

            a('#wx_qrcode').qrcode({
                render:'table',
                text:self.options.url
            });
            a('.wx_share_dialog_close').bind('click',function(){
                a('.wx_share_dialog').remove();
            })
        }
    }
}());