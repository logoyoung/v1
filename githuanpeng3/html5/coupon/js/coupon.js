/**
 * Created by junxiao on 2017/8/2.
 */
$(function () {
    //localStorage.removeItem('_closeVideo');
    //var videoSrc = 'assets/hp.mp4?' + new Date().getTime();
    var videoLock = false;
    var share = {
        init: function (){
            this._initBind();
            this._initRecieve();
        },
        _initBind: function () {
            // if in WeChat  微信里 MicroMessage UserAgent
            /*document.addEventListener("WeixinJSBridgeReady", function (){
             share.videoPlayEvent();
             }, false);*/

            //download Modal
            $('.hp_download .close').on('touchstart', function(){
                $('.hp_download').hide();
            });

            $('.video-swipeTop').on('touchstart', function () {
                share.closeVideoEvent();
                $('.video-center').hide();
            });

            //video PlayBtn
            $('#videoBtn').on('click', function(){
                if(localStorage.getItem('_closeVideo')){
                    $(this).hide() && share.closeVideoEvent();
                }else{
                    $(this).hide() && share.openVideoEvent();
                }
            });

            //video openEvent
            $('.video-open').on('click', function () {
                share.openVideoEvent();
            });

            //video touch
            /*$('.video-content').on('click', function () {
                if(videoLock == true){
                    share.closeVideoEvent();
                    $('.video-center').hide();
                }
            })*/

        },
        _initRecieve: function () {
            if(localStorage.getItem('_phone')){
                var phoneNum = localStorage.getItem('_phone');
                $('#tel_phoneNum').focus().val(phoneNum);
            }
            $('#tel_phoneNum').focus(function () {
                $('.hp_download').hide();
                // $('.coupon-container').addClass('focus');
                $('.coupon-container').scrollTop('300');
            }).blur(function () {
                $('.hp_download').show();
                // $('.coupon-container').removeClass('focus');
                $('.coupon-container').scrollTop('0');
            });
            $('#receive_btn').on('touchstart', function () {
                var tel = $('#tel_phoneNum').val();
                if(!(/^1[34578]\d{9}$/.test(tel))){
                    share.mobileTips('手机号错误');
                }else{
                    localStorage.setItem('_phone', tel);

                    share._initAccess(tel);
                }
            })
        },
        _initAccess: function (tel) {
            var telNum = tel;
            var activityId  = share.queryString().activityId ? share.queryString().activityId : '';
            var receiveUuid = share.queryString().receiveUuid ? share.queryString().receiveUuid : '';
            function status(a, b){
                if(a == 1){
                    var tpl = '<p class="desc">恭喜你！获得优惠券</p>\
                                <p class="money"><span class="num">'+b+'</span><span>欢朋币</span></p>';
                    return tpl;
                }else{
                    //nochace
                    var tpl = '<p class="fail">感谢参与！</p>\
                                <p class="fail">你今天已经领取过哦~</p>';
                    return tpl;
                }
            }

            if(!activityId ){
                share.mobileTips('分享链接出错啦~');
                return;
            }
            if(!telNum){
                share.mobileTips('手机号错误~');
                return;
            }
            var requestUrl = $conf.api + 'due/receiveCoupon.php';
            var requestData = {
                activityId  : activityId,
                receiveUuid : receiveUuid,
                phone       : telNum
            };
            ajaxRequest({ url: requestUrl, data: requestData},function (responseData) {
                console.log(responseData);
                $('.coupon-first').addClass('none');
                $('.coupon-second').removeClass('none');
                var dom = status(1, responseData.price);
                $('#coupon_result').html(dom);
            }, function (responseData) {
                console.log(responseData);
                var dom = status(0, null);
                if(responseData.code == '-8022'){
                    $('.coupon-first').addClass('none');
                    $('.coupon-second').removeClass('none');
                    $('#coupon_result').html(dom);
                }else{
                    share.mobileTips(responseData.desc);
                    return;
                }
            })
        },
        videoPlayEvent: function () {
            var video = $('.video-content')[0];
            enableInlineVideo(video);
            // video.load();
            video.play();
            setTimeout(function () {
                videoLock = true;
            }, 1500);
        },
        closeVideoEvent: function () {
            videoLock = false;
            localStorage.setItem('_closeVideo', '1');
            $('.mask-container, .video-center,.share-container').hide();
            $('.coupon-container, .hp_download').show();
            $('.video-content')[0].pause();
        },
        openVideoEvent: function () {
            localStorage.removeItem('_closeVideo');
            $('.video-center').removeClass('loadVideo').addClass('normal');
            $('.mask-container').hide();
            $('.share-container ,.video-center, .video-swipeTop').show();
            $('.coupon-container, .hp_download').hide();
            share.videoPlayEvent();
        },
        queryString: function () {
            var b = location.href;
            var c = new Object();
            if (b.indexOf('?') > -1) {
                var str = b.substr(b.indexOf('?')+1);
                strs = str.split("&");
                for(var i = 0; i < strs.length; i ++) {
                    c[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
                }
            }
            return c;
        },
        mobileTips: function (a) {
            if(!a){
                return;
            }
            $('#error-content').text(a) && $('.modal-box , .error-modal').show() && setTimeout(function () {
                $('.modal-box , .error-modal').hide() && $('#error-content').text('')
            }, 1500);
        }
    };
    share.init();

});

