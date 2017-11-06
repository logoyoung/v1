/**
 * Created by hantong on 16/1/25.
 */

(function(a){
    var u = {
        'tsina':'http://service.weibo.com/share/share.php?',
        'tqqwb':'http://v.t.qq.com/share/share.php?',
        'tqzone':'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?',
        'tqqFriend':'http://connect.qq.com/widget/shareqq/index.html?'
    };
    var defaultOption = {
        qq:{
            url:'',
            desc:'',/*分享理由(风格应模拟用户对话),支持多分享语随机展现（使用|分隔）*/
            title:'', /*分享标题(可选)*/
            summary:'', /*分享摘要(可选)*/
            pics:'', /*分享图片(可选)*/
            site:'' /*分享来源(可选) 如：QQ分享*/,
            showcount:'1'
        },
        sina:{
            url:'',
            title:'',
            language:'zh_cn',
            searchPic:'true',
            pic:'',
            source:''
        }
    };

    a.fn.share = function(type,option){
        a(this).bind('click',function(){
            var shareOption;

            if(type == 'tsina'){
                shareOption = a.extend(defaultOption['sina'], option);
            }else{
                shareOption = a.extend(defaultOption['qq'], option);
            }
            var tmp = [];
            for(var i in shareOption){
                tmp.push(i + '=' + encodeURIComponent(shareOption[i] || ''));
            }
            var url = u[type] + tmp.join('&');
            window.open(url);
        });
    };
}(jQuery));
var InviteFriend;
(function(a){
    var $conf = conf.getConf();
    var ajaxDefault = {
        url:'',
        type:'post',
        dataType:'json',
        data:{},
        success:function(){}
    };

    var qq = {
        url:'',
        desc:'',/*分享理由(风格应模拟用户对话),支持多分享语随机展现（使用|分隔）*/
        title:'', /*分享标题(可选)*/
        summary:'', /*分享摘要(可选)*/
        pics:'', /*分享图片(可选)*/
        site:'' /*分享来源(可选) 如：QQ分享*/,
        showcount:'1'
    }
    var sina = {
        url:'',
        title:'',
        language:'zh_cn',
        searchPic:'true',
        pic:'',
        source:''
    }


    InviteFriend = {
        inviteBoxHtml:function(){
            function headHtml(){
                var htmlstr = '';
                htmlstr += '<div class="box_head">';
                htmlstr += '<p class="title left">邀请好友</p>';
                htmlstr += '<div class="closeBox">';
                htmlstr += '<span class="personal_icon close"></span>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '</div>';
                htmlstr += '</div>';

                return htmlstr;
            }
            function bodyHtml(){
                var htmlstr = '';
                htmlstr += '<div class="box_body">';
                htmlstr += '<div class="inviteShareDiv">';
                htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_red"><span class="share_icon weibo"></span></div><div class="clear"></div><p>新浪微博</p></div>';
                htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_yellow"><span class="share_icon qzone"></span></div><div class="clear"></div><p>QQ空间</p></div>';
                htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_blue"><span class="share_icon qqfriend"></span></div><div class="clear"></div><p>QQ好友</p></div>';
                htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_blue"><span class="share_icon qqwb"></span></div><div class="clear"></div><p>腾讯微博</p></div>';
                htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_green"><span class="share_icon wxfriend"></span></div><div class="clear"></div><p>微信</p></div>';
                htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_deepblue"><span class="share_icon copylink"></span></div><div class="clear"></div><p>复制链接</p></div>';
                htmlstr += '</div>';
                htmlstr += '</div>';

                return htmlstr;
            }

            var htmlstr = '';
            htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:220px;z-index: 1000;">';
            htmlstr += '<div class="theBox" style="padding: 30px 20px; height: 370px;">';
            htmlstr = htmlstr + headHtml() + bodyHtml() + "</div> </div>" ;

            return htmlstr;
        },
        inviteBoxHtml_wxShare:function(){
            function headHtml(){
                var htmlstr = '';
                htmlstr += '<div class="box_head">';
                htmlstr += '<p class="title left">微信分享</p>';
                htmlstr += '<div class="closeBox">';
                htmlstr += '<span class="personal_icon close"></span>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '</div>';
                htmlstr += '</div>';

                return htmlstr;
            }
            function bodyHtml(){
                var htmlstr = '';
                htmlstr += '<div class="box_body">';
                htmlstr += '<div class="qrcodeDiv mt-30" style="margin-left: 118px;">';
                htmlstr += '<div id="wx_qrcode"></div>';
                htmlstr += '<p>使用微信扫一扫</p><p>即刻分享给您的微信好友或朋友圈哦～</p>';
                htmlstr +='</div>';
                htmlstr += '</div>';

                return htmlstr;
            }
            var htmlstr = '';
            htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:220px;z-index: 1000;">';
            htmlstr += '<div class="theBox" style="padding: 30px 20px; height: 370px;">';
            htmlstr = htmlstr + headHtml() + bodyHtml() + "</div> </div>" ;

            return htmlstr;
        },
        inviteBtnClikcEvent:function(){
            var self = this;
            NoticeBox.create(self.inviteBoxHtml());
            var closeBtn = $("#noticeBox .box_head .close");
            closeBtn.bind('click', NoticeBox.remove);

            $("#noticeBox .inviteShareDiv .share_iconDiv .weibo").parent().share('tsina',sina);
            $("#noticeBox .inviteShareDiv .share_iconDiv .qzone").parent().share('tqzone', qq);
            $("#noticeBox .inviteShareDiv .share_iconDiv .qqfriend").parent().share('tqqFriend', qq);
            $("#noticeBox .inviteShareDiv .share_iconDiv .qqwb").parent().share('tqqwb', qq);

            $("#noticeBox .inviteShareDiv .share_iconDiv .wxfriend").parent().bind('click', function(){
                NoticeBox.create(self.inviteBoxHtml_wxShare());
                $('#noticeBox .box_body .qrcodeDiv p').css({'text-align':'left','color':'#666666'});
                $("#noticeBox .box_head .close").bind('click', NoticeBox.remove);
                $('#wx_qrcode').qrcode({
                    render:'table',
                    text:'http://dev.huanpeng.com/main/index.php'
                });
            });
        },
        init:function(){
            var self = this;
            $("#inviteNow").bind('click', function(){
               self.inviteBtnClikcEvent();
            });
        }
    }
}(jQuery));

