<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/7
 * Time: 下午8:26
 */

include_once './../../init.php';
include_once (INCLUDE_DIR.'User.class.php');

//获取邀请码
function getICode(){
    $i_code = isset($_GET['i_code'])?$_GET['i_code']:'';
    if($i_code) return $i_code;
    //
    //if(isMobile()) return '';
    //
    $uid = isset($_COOKIE['_uid'])?(int)$_COOKIE['_uid']:'';
    $enc = isset($_COOKIE['_enc'])?$_COOKIE['_enc']:'';
    if(!$uid||!$enc) return '';
    $user = new UserHelp($uid);
    $errCode = $user->checkStateError($enc);
    $i_code = $errCode?'':$uid;
    return $i_code;
}

$isMobile = isMobile();
$isMobile = $isMobile?1:0;
$i_code = getICode();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta content="email=no" name="format-detection" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" >
    <title>邀请主播签约-欢朋</title>
    <link rel="stylesheet" href="index.css" id="styleCss">
    <script>
        (function () {

            if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i))  {

                var styleCss = document.querySelector('#styleCss');

                styleCss.href = 'mIndex.css';
                /*rem*/
                (function(){

                    size();
                    window.onresize = function (){

                        size();

                    };

                    function size(){

                        var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;

                        document.documentElement.style.fontSize = winW / 20 +'px';

                    }

                })();

            }
        })();
    </script>
    <?php include '../../tpl/commSource.php';?>
</head>
<!--<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="format-detection" content="telephone=no"/>
    <title>邀请主播签约-欢朋</title>
    <link rel="stylesheet" href="index.css">
    <?php /*include '../../tpl/commSource.php';*/?>
</head>-->
<body>
<?php if(!$isMobile) include '../../head.php';?>
<div class="inAu-container">
    <div class="inAu-body">

        <div class="inAu-header">
            <h2>活动时间:&nbsp;2月8日~2月28日</h2>
        </div>

        <div class="inAu-section">

            <div class="seCenter">
                <div class="seLtr">
                    <span class="codeDesc">邀请码:</span>
                    <input id="i_code" type="text" readonly>
                    <input id="copy_url" type="button" value="复制">
                </div>
                <div class="seRtr">
                    <span class="invDesc">分享邀请活动到</span>
                    <div id="pWechat" class="weChat" data-cmd="wx">
                        <div id="wx-share-qrcode" class="qrcode"></div>
                        <img src="wechat.png">
                        <p>微信</p>
                    </div>
                    <div id="pQQ" class="qqShare" data-cmd="tqq">
                        <img src="qq.png">
                        <p>QQ</p>
                    </div>
                    <div id="pQzone" class="qzoShare" data-cmd="tqzone">
                        <img src="qzone.png">
                        <p>QQ空间</p>
                    </div>
                    <div id="pWeibo" class="wbShare" data-cmd="tsina">
                        <img src="weibo.png">
                        <p>微博</p>
                    </div>
                </div>
                <div class="mShare">
                    <button id="mShareBtn">点击分享</button>
                </div>
            </div>

        </div>

        <div class="inAu-desc">
            <div class="inAu-center">
                <p>一.活动时间：2月8日-2月28日</p>
                <p>二.活动平台：欢朋游戏直播官网(www.huanpeng.com）及官网二维码下载安卓客户端。</p>
                <p>三.活动适用人群：QQ群内人员及预备签约主播人员</p>
                <p>四.活动内容:</p>

                <p class="left-1">1.解决BUG活动内容：</p>
                <p class="left-2">a.您发现BUG后，在群内提交给群主，经欢朋运营团队审核后，确认有此BUG，奖励在审核后的第二日发放QQ红包2元；</p>
                <p class="left-3">提交技术性BUG导致无法正常开启直播的经核实，奖励QQ红包5元。</p>
                <p class="left-2">b.每项BUG仅一人有效，在玩家C提交BUG前，已有玩家A提出相同BUG，则玩家C不计入奖励。</p>
                <p class="left-2">c.提交BUG流程：</p>
                <p class="left-3">您将QQ号、欢朋昵称、BUG描述详情及BUG截图——发送至群主（1728296767）。审核后群主会在群里发布奖励通知。</p>

                <p class="left-1">2.发布精彩视频活动内容：</p>
                <p class="left-2 indent-2">首先在官网注册并完成认证，开启欢朋直播。直播结束并勾选上传该直播视频，经欢朋运营审核后完成发布视频，参与领取红包的录播视频时长要求最低1小时以上。</p>
                <p class="left-2">A.精彩视频数量榜：</p>
                <p class="left-3">活动期间主播发布视频数量最多者</p>
                <p class="left-3">第一名：现金
                    <span class="orange">100元+1000欢朋币</span>
                </p>
                <p class="left-3">第二名至第三名：现金
                    <span class="orange">50元+500欢朋币</span>
                </p>
                <p class="left-3">第四名至第五名：现金
                    <span class="orange">30元+200欢朋币</span>
                </p>
                <p class="left-2">B.精彩视频时长榜：</p>
                <p class="left-3">活动期间主播发布有效视频时长最长者</p>
                <p class="left-3">第一名：
                    <span class="orange">100元+500欢朋币</span>
                </p>
                <p class="left-3">第二名至第三名：
                    <span class="orange">50元+200欢朋币</span>
                </p>
                <p class="left-3">第四名至第五名：
                    <span class="orange">25元+100欢朋币</span>
                </p>
                <p class="left-2">C.精彩视频阳光普照奖：</p>
                <p class="left-3 indent-2">您上传精彩视频内容，每录制一路视频经欢朋运营审核通过，以上数量和精彩奖均未获得者可获得
                    <span class="orange">欢朋豆2000</span>
                    （同一ID最多领取3次奖励）</p>
                <p class="left-2">D.微信分享活动：</p>
                <p class="left-3 indent-2">您成功邀请一名好友参加此次系列活动
                    <span class="orange">(被邀请用户需在 “我做主播-开始认证”时输⼊上⽅的邀请码)</span>
                    ，完成主播认证并直播。您将可领取
                    <span class="orange">5元红包+欢朋豆5000</span>
                    ，邀请好友需提供好友QQ号码、欢朋昵称和直播截图以便运营审核发放奖励。</p>
                <p class="left-3">以上红包、现金全部由QQ或微信形式发放，欢朋币、欢朋豆会在活动结束后由平台统一发放。</p>
                <p class="left-1">3. 意见征集活动：</p>
                <p class="left-2 indent-2">在欢朋封测期间，群策群力集思广益但凡提出活动、直播功能性等建议，一经采纳，奖励
                    <span class="orange">100元</span>
                    。（官方正在搭建中或已在策划中将视为无效）。</p>
                <p class="right">此活动最终解释权归欢朋运营团队所有</p>
            </div>
        </div>

        <div class="inAu-footer"></div>

    </div>
    <div class="inAu-modal">
        <div class="shareBox">
            <button id="mWechat"></button>
            <button id="mQQ"></button>
            <button id="mQZone"></button>
            <button id="mWeibo"></button>
        </div>
        <div class="shareBG"></div>
    </div>
</div>
<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery-1.9.1.min.js?v=1.0.2" ></script>
<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery.zclip.js?v=1.0.2" ></script>
<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery.qrcode.min.js?v=1.0.2" ></script>
<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery.artDialog.js?v=1.0.2" ></script>
<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery.form.js?v=1.0.2"></script>
<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery.mCustomScrollbar.concat.min.js?v=1.0.2" ></script>
<script type="text/javascript" src='<?php echo STATIC_JS_PATH; ?>swfobject.js?v=1.0.2'></script>
<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>common.js?v=1.0.2"></script>
<script>
    var isMobile = "<?php echo (int)$isMobile; ?>";
    isMobile = parseInt(isMobile);
    if(!isMobile)
        var head = new head(null,false);
    (function () {
        if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb|micromessenger)/i))  {
            var  seRtrSty = document.querySelector('.seRtr');
            var  shareBtnsty = document.querySelector('.mShare');
            var  mShareBtn = document.querySelector('#mShareBtn');
            var  modalBox = document.querySelector('.inAu-modal');
            var  modalBG = document.querySelector('.shareBG');

            seRtrSty.style.display = 'none';
            shareBtnsty.style.display = 'inline-block';

            mShareBtn.onclick = function () {
                modalBox.style.display = 'block';
            };

            modalBG.onclick = function () {
                modalBox.style.display = 'none';
            };
            /*var  descP = document.querySelectorAll('.inAu-center>p');

            for(var i = 0; i < descP.length; i++){
                descP[i].style.fontSize = '32px';
            }*/

        }else{
            var mShare = document.querySelector('.mShare');
            mShare.style.display = 'none';

        }
    })();

</script>
<script>
    //  PC端 和 移动端 的分享 不共用ID  需要在script中 将两个ID 绑定
    //  微信:    #mWechat
    //  QQ :    #mQQ
    //  空间:    #mQZone
    //  微博:    #mWeibo
    $(document).ready(function () {
        var isMobile = "<?php echo (int)$isMobile; ?>";
        isMobile = parseInt(isMobile);
        var url = document.location.href;
        //copy event set
        var that = {
            alert:false
        };
        var copyEvent = function () {
            if(isMobile != '1'){
                $('#copy_url').zclip({
                    path: $conf.domain + 'static/js/ZeroClipboard.swf',
                    copy:$('#i_code').val(),
                    afterCopy:function(){
                        if(!that.alert)
                            alert('已经成功复制到您的剪切板');
                        that.alert = true;
                    }
                });
            }else{
                $('#copy_url').select();
                try {
                    var msg = document.execCommand('copy') ? '成功' : '失败';
                    alert('复制内容'+msg);
                }catch (err){
                    alert('您当前的浏览器不支持，请手动复制');
                }
            }
        }
        $('#copy_url').bind('click',copyEvent);

        //share
        if(isMobile){
            //is huanpeng app
            var i_code = "<?php echo $i_code;?>";
            $('#i_code').val(i_code);
            var shareBody = {
                //
                title:'欢朋直播封测活动开启',
                content:'邀请好友一起参与封测有奖励哦～',
                url:url,
                poster:$conf.domain+'activity/inviAuthor/poster.png',
                weixinPoster:$conf.domain+'activity/inviAuthor/weixin_poster.png'
            };
            if(window.huanpengShare){
                //wechat
                $('#mWechat').click(function(){
                    window.huanpengShare.turnTo('wechat',shareBody['content'],shareBody['title'],shareBody['url'],shareBody['weixinPoster']);
                })
                //QQ
                $('#mQQ').click(function(){
                    window.huanpengShare.turnTo('shareToQQ',shareBody['content'],shareBody['title'],shareBody['url'],shareBody['poster']);
                })
                //QZone
                $('#mQZone').click(function(){
                    window.huanpengShare.turnTo('shareToQQzone',shareBody['content'],shareBody['title'],shareBody['url'],shareBody['poster']);
                })
                //weibo
                $('#mWeibo').click(function(){
                    window.huanpengShare.turnTo('weibo',shareBody['content'],shareBody['title'],shareBody['url'],shareBody['poster']);
                })
            }//is mobile browser
            else{
                $('.mShare').hide();
                //var tt = 0;
            }
            // call native function to share website
        }else{
            var i_code = "<?php echo $i_code; ?>";
            //var loginStatus = "<?php echo $loginStatus; ?>";
            //loginStatus = parseInt(loginStatus);
            if(i_code)
                $('#i_code').val(i_code);
            else {
                $('#i_code').attr("placeholder", "查看邀请码").css('cursor','pointer');
                $('#i_code').click(function(){loginFast.login(0);});
            }

            $('#wx-share-qrcode').qrcode({render: 'canvas', text: location.href+'?i_code='+i_code, width: 150, height: 150});//table

            $('#pWechat, #pQQ, #pQzone, #pWeibo').click(function () {
                if(!i_code) {
                    loginFast.login(0);
                }
                else{
                    var option = {
                        url:location.href+'?i_code=<?php echo $i_code; ?>',
                        title:document.title,
                        sumary:document.title
                    }

                    var cmd = $(this).attr('data-cmd');
                    if(cmd == 'tsina'){
                        option = $.extend({},option,{pic:$conf.domain+'activity/inviAuthor/poster.png'});
                    }else{
                        option = $.extend({},option,{pics:$conf.domain+'activity/inviAuthor/poster.png'});
                    }
                    var cmdData = {
                        'tsina':'weibo',
                        'tqq':'qq',
                        'tqzone':'qq',
                        'wx':'wechat'
                    };
                    if(cmd){
                        if(cmd == 'wx'){
                            //show qrcode
                            //$('#shareModal #wx-share-qrcode').show();
                            $('#wx-share-qrcode').css('display','block');
                        }else{
                            Share.init(option,{channel:cmd});
                            //qrcode hide
                            $('#wx-share-qrcode').css('display','none');
                        }
                    }
                }
            })
        }
    });
</script>
<?php if(!$isMobile) include '../../footerSub.php';?>
</body>
</html>
