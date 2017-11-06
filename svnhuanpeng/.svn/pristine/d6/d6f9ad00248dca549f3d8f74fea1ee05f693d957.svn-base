/**
 * Created by hantong on 16/5/16.
 */


!function(){
    var id = 'videoPlayer';
    var file = $conf.domain + 'static/flash/videoPlayer.swf';
    var version = '9.0.0';
    var install = 'expressInstall.swf';
    var flashvar = {
        'urlb':$conf.domain + 'static/flash/barrage.swf',
        'urlw':$conf.domain + 'static/flash/wait.swf',
        'urld':$conf.domain + 'static/flash/dot.swf',
        'loadingURL':$conf.domain + 'static/flash/loading.swf',
        'UIButtonURL':$conf.domain + 'static/flash/UIButton.swf'
    };
    var param = {
        quality:'hight',
        bgcolor:'#869ca7',
        allowScriptAccess:'always',
        allowFullScreen:'true',
        allowFullScreenInteractive:'true',
        WindowlessVideo:'1',
        wmode:'transparent'
    };
    var attribute = {
        allowScriptAccess:'always',
        allowFullScreen:'true',
        allowFullScreenInteractive:'true',
        name:id,
        align:'middle'
    };

    swfobject.embedSWF(file, id, '100%', "100%", version, install, flashvar, param, attribute);
}()
var VRoom ;

var $ROOM = {};

!function(a){
    $conf = conf.getConf();
    var pageUser = {};
    pageUser.uid = getCookie('_uid') || 0;
    pageUser.enc = getCookie('_enc') || '';

    var commentSize = 4;

    VRoom = {
        init:function(){
            var requestUrl = $conf.api + 'video/getVideoInfo.php';
            var requestData = {
                uid:pageUser.uid,
                encpass:pageUser.enc,
                videoID:videoID
            };
            ajaxRequest({url:requestUrl,data:requestData}, function (responseData) {
               if(responseData.videoID){
                   $ROOM = responseData;
                   VRoom.initPlayerInfo(responseData.uid);
                   VRoom.initVideoInfo(responseData);
                   VRoom.initVideoOpt(responseData);
                   VRoom.initVideoPlayer(responseData);
                   VRoom.initCommentContent();
                   VRoom.initCommentEmoji();
                   VRoom.initCommentRequest();
                   VRoom.initAnchorVideoList(responseData);
                   VRoom.initSimilarVideoList();
                   VRoom.initLivingNowList(responseData);

               }
            });
            // a.ajax({
            //     url:$conf.api + 'getVideoInfo.php',
            //     type:'post',
            //     dataType:'json',
            //     data:{
            //         uid:pageUser.uid,
            //         encpass:pageUser.enc,
            //         videoID:videoID
            //     },
            //     success:function(d){
            //         if(d.videoID){
            //             VRoom.initPlayerInfo(d.publisherUserID);
            //             VRoom.initVideoInfo(d);
            //             VRoom.initVideoOpt(d);
            //             VRoom.initVideoPlayer(d);
            //             VRoom.initCommentContent();
            //             VRoom.initCommentEmoji();
            //             VRoom.initCommentRequest();
            //             VRoom.initAnchorVideoList(d);
            //             VRoom.initSimilarVideoList();
            //             VRoom.initLivingNowList(d);
            //         }
            //     }
            // });

            $('.videoListDiv .selected_tab li').bind('click', function(){
                $('.videoListDiv .selected_tab li').removeClass('selected');
                $(this).addClass('selected');

                $('.videoListDiv .tabcon').addClass('none');
                $('.videoListDiv .tabcon').eq($(this).index()).removeClass('none');

            });
        },
        initPlayerInfo:function(targetuserid){

            var requestUrl = $conf.api + 'video/getVideoPublisherInfo.php';
            var requestData = {
                uid:pageUser.uid,
                encpass:pageUser.enc,
                luid:targetuserid
            };

            ajaxRequest({url:requestUrl,data:requestData},function(responseData){

            });

            // a.ajax({
            //     url:$conf.api + 'getVideoPublisherInfo.php',
            //     type:'post',
            //     dataType:'json',
            //     data:{
            //         uid:pageUser.uid,
            //         encpass:pageUser.enc,
            //         targetUserID:targetuserid
            //     },
            //     success:function(d){
            //
            //     }
            // })
        },
        initVideoInfo:function(d){
            a('.liveRoom_nav .player_face img').attr('src', d.head);
            a('.liveRoom_nav .publisher_name').text(d.nick);
            if(d.isLiving == '1'){
                a('.liveRoom_nav .anchor-live-stat').removeClass('none');
            }
            a('.liveRoom_nav .player_otherdesc').text(d.title);
            a('title').text(d.title+'－欢朋直播－精彩手游直播平台！');

            if(d.isFollow > 0){
                var followBtn = a('.liveRoom_nav #followbtn');
                followBtn.addClass('followed').find('em').text('已关注').css('display','block');
                followBtn.find('.anchor_icon').remove();
                followBtn.hover(function(){
                    if($(this).hasClass('followed')){
                        $(this).find('em').text('取消关注');
                    }
                },function(){
                    if($(this).hasClass('followed')) {
                        $(this).find('em').text('已关注');
                    }
                });
            }

            a('.nav_attention_left').text(d.fansCount);

            //a('.liveRoom_nav .enterliveRoom').attr('href', $conf.domain +'room.php?luid='+ d.uid);
            a('.liveRoom_nav .enterliveRoom').attr('href', $conf.domain + d.roomID);

            a('.liveRoomContent .liveRoom_opt .videodetail .text:eq(0)').text(d.viewCount);
            a('.liveRoomContent .liveRoom_opt .videodetail .text:eq(1)').text(d.commentCount);

            var videoopt = a('.liveRoomContent .liveRoom_opt .videoopt');

            videoopt.find('.upDiv .text').text(d.upCount);
            if(d.isUp > 0){
                videoopt.find('.upDiv .likeIcon').addClass('liked');
            }

            videoopt.find('.collectoptDiv .text').text(d.collectCount);
            if(d.isCollect > 0){
                videoopt.find('.collectoptDiv .anchor_icon').addClass('collected');
            }

        },
        initVideoOpt:function(d){
            this.initAnchorFollowEvent(d.uid);
            this.initVideoUpEvent();
            this.initVideoCollectEvent();
            this.initVideoShareEvent();
        },
        initAnchorFollowEvent:function(publisherUserID){
            var followBtn = a('#followbtn');

            var follow_loading = false;

            followBtn.bind('click', function(){
                if(!check_login()){
                    return;
                }
                if(!check_phoneStatus()){
                    return;
                }
                if($(this).hasClass('followed')){
                    followCancelRequest();
                }else{
                    followRequest();
                }
            });

            function followRequest(){
                if(follow_loading){
                    return;
                }
                follow_loading =  true;
                var requestUrl = $conf.api + 'room/followUser.php';
                var requestData = {luid:publisherUserID,uid:pageUser.uid,encpass:pageUser.enc};
                ajaxRequest({url:requestUrl,data:requestData}, function(responseData){
                    follow_loading = false;
                    var followCount = parseInt(a('.nav_attention_left').text()) || 0;
                    followBtn.addClass('followed').find('em').text('已关注').css('display','block');
                    followBtn.find('.anchor_icon').remove();
                    followBtn.hover(function(){
                        if($(this).hasClass('followed')){
                            $(this).find('em').text('取消关注');
                        }
                    },function(){
                        if($(this).hasClass('followed')) {
                            $(this).find('em').text('已关注');
                        }
                    });
                    var attenLeft = a('.nav_attention_left')

                    attenLeft.text(parseInt(followCount) + 1);
                },function (responseData) {

                    follow_loading = false;
                });
                // a.ajax({
                //     url:$conf.api+'followUser.php',
                //     type:'post',
                //     dataType:'json',
                //     data:{
                //         uid:pageUser.uid,
                //         encpass:pageUser.enc,
                //         targetUserID:publisherUserID
                //     },
                //     success:function(d){
                //         follow_loading = false;
                //         var followCount = parseInt(a('.nav_attention_left').text()) || 0;
                //         if(d.isSuccess == '1'){
                //             followBtn.addClass('followed').find('em').text('已关注').css('display','block');
                //             followBtn.find('.anchor_icon').remove();
                //             followBtn.hover(function(){
                //                 if($(this).hasClass('followed')){
                //                     $(this).find('em').text('取消关注');
                //                 }
                //             },function(){
                //                 if($(this).hasClass('followed')) {
                //                     $(this).find('em').text('已关注');
                //                 }
                //             });
                //             var attenLeft = a('.nav_attention_left')
                //
                //             attenLeft.text(parseInt(followCount) + 1);
                //         }
                //     },
                //     error:function(){
                //         follow_loading = false;
                //     }
                //
                // });
            }

            function followCancelRequest(){
                if(follow_loading)
                    return;

                follow_loading = true;
                var requestUrl = $conf.api + 'room/followUserCancel.php';
                var requestData = {luids:publisherUserID,uid:pageUser.uid,encpass:pageUser.enc};
                ajaxRequest({url:requestUrl,data:requestData}, function (responseData) {
                    follow_loading = false;
                    var followCount = parseInt(a('.nav_attention_left').text()) || 0;
                    followBtn.removeClass('followed').find('em').text('关注').css('display','');
                    if(!followBtn.find('.anchor_icon').get()[0]){
                        followBtn.append('<span class="anchor_icon followIcon3"></span>');
                    }
                    var attenLeft = a('.nav_attention_left')

                    if(followCount) attenLeft.text(parseInt(followCount) - 1);
                },function (resposneData) {
                    follow_loading = false;
                });

                // a.ajax({
                //     url:$conf.api+'followUserCancel.php',
                //     type:'post',
                //     dataType:'json',
                //     data:{
                //         uid:pageUser.uid,
                //         encpass:pageUser.enc,
                //         targetUserID:publisherUserID
                //     },
                //     success:function(d){
                //         follow_loading = false;
                //
                //         var followCount = parseInt(a('.nav_attention_left').text()) || 0;
                //         if(d.isSuccess == '1'){
                //             followBtn.removeClass('followed').find('em').text('关注').css('display','');
                //             if(!followBtn.find('.anchor_icon').get()[0]){
                //                 followBtn.append('<span class="anchor_icon followIcon3"></span>');
                //             }
                //             var attenLeft = a('.nav_attention_left')
                //
                //             if(followCount) attenLeft.text(parseInt(followCount) - 1);
                //         }
                //     },
                //     error:function(){
                //         follow_loading = false;
                //     }
                //
                // });
            }

        },
        initVideoUpEvent:function(){
            var upElement = a('.liveRoom_opt .upDiv .likeIcon');

            upElement.bind('click', function(){
                if($(this).hasClass('liked')){
                    upCancelRequest();
                }else{
                    upVideoRequest();
                }
            });
            var up_video_loading = false;

            function upVideoRequest(){
                if(!check_login() || up_video_loading) return

                up_video_loading = true;
                var requestUrl = $conf.api + 'video/upCount.php';
                var requestData= {
                    videoID:videoID,
                    uid:pageUser.uid,
                    encpass:pageUser.enc,
                    type:1
                };
                ajaxRequest({url:requestUrl,data:requestData}, function(responseData){
                    up_video_loading = false;
                    var upCount = parseInt(upElement.next().text());
                    upElement.addClass('liked');
                    upElement.next().text(parseInt(upCount) + 1);
                },function(responseData){
                    up_video_loading = false;
                });
                // a.ajax({
                //     url:$conf.api + 'upCount.php',
                //     type:'post',
                //     dataType:'json',
                //     data:{
                //         videoId:videoID,
                //         uid:pageUser.uid,
                //         encpass:pageUser.enc,
                //         type:1
                //     },
                //     success:function(d){
                //         up_video_loading = false;
                //         var upCount = parseInt(upElement.next().text());
                //         if(d.isSuccess == '1'){
                //             upElement.addClass('liked');
                //             upElement.next().text(parseInt(upCount) + 1);
                //         }
                //     },
                //     error:function(){
                //         up_video_loading = false;
                //     }
                // });
            }
            function upCancelRequest(){
                if(up_video_loading)
                    return;

                up_video_loading = true;
                var requestUrl = $conf.api + 'video/upCount.php';
                var requestData= {
                    videoID:videoID,
                    uid:pageUser.uid,
                    encpass:pageUser.enc,
                    type:0
                };
                ajaxRequest({url:requestUrl,data:requestData}, function(responseData){
                    up_video_loading = false;
                    var upCount = parseInt(upElement.next().text());
                    upElement.removeClass('liked');
                    upElement.next().text(parseInt(upCount) - 1);
                },function(responseData){
                    up_video_loading = false;
                });
                // a.ajax({
                //     url:$conf.api + 'upCount.php',
                //     type:'post',
                //     dataType:'json',
                //     data:{
                //         videoId:videoID,
                //         uid:pageUser.uid,
                //         encpass:pageUser.enc,
                //         type:0
                //     },
                //     success:function(d){
                //         up_video_loading = false;
                //         var upCount = parseInt(upElement.next().text());
                //         if(d.isSuccess == '1'){
                //             upElement.removeClass('liked');
                //             upElement.next().text(parseInt(upCount) - 1);
                //         }
                //     },
                //     error: function () {
                //         up_video_loading = false;
                //     }
                // });
            }
        },
        initVideoCollectEvent:function(){
            var collectEle = $('#collectVideo');

            collectEle.bind('click', function(){
                if($(this).hasClass('collected')){
                    collectCancelRequest();
                }else{
                    collectRequest()
                }
            });

            var collect_video_loading = false;

            function collectRequest(){
                if(!check_login()) return;
                if(collect_video_loading)return;

                collect_video_loading = true;
                var requestUrl = $conf.api + 'room/collectVideo.php';
                var requestData = {
                    videoID:videoID,
                    uid:pageUser.uid,
                    encpass:pageUser.enc
                };

                ajaxRequest({url:requestUrl,data:requestData}, function (respoinseData) {
                    collect_video_loading = false;
                    var collectCount = parseInt(collectEle.next().text());
                    collectEle.addClass('collected');
                    collectEle.next().text(parseInt(collectCount) + 1);
                },function(responseData){
                    collect_video_loading = false;
                });

                // a.ajax({
                //     url:$conf.api + 'collectVideo.php',
                //     type:'post',
                //     dataType:'json',
                //     data:{
                //         videoID:videoID,
                //         uid:pageUser.uid,
                //         encpass:pageUser.enc
                //     },
                //     success:function(d){
                //         collect_video_loading = false;
                //         var collectCount = parseInt(collectEle.next().text());
                //         if(d.isSuccess == '1'){
                //             collectEle.addClass('collected');
                //             collectEle.next().text(parseInt(collectCount) + 1);
                //         }
                //     },
                //     error:function(){
                //         collect_video_loading = false;
                //     }
                //
                // });
            }
            function collectCancelRequest(){
                if(collect_video_loading)
                    return;

                collect_video_loading = true;
                var requestUrl = $conf.api + 'video/cancelCollectVideo.php';
                var requestData = {
                    videoIDList:videoID,
                    uid:pageUser.uid,
                    encpass:pageUser.enc
                };

                ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                    collect_video_loading = false;
                    var collectCount = parseInt(collectEle.next().text());
                    collectEle.removeClass('collected');
                    collectEle.next().text(parseInt(collectCount) - 1);
                },function (responseData) {
                    collect_video_loading = false;
                });
                // a.ajax({
                //     url:$conf.api + 'cancelCollectVideo.php',
                //     type:'post',
                //     dataType:'json',
                //     data:{
                //         videoIDList:videoID,
                //         uid:pageUser.uid,
                //         encpass:pageUser.enc
                //     },
                //     success:function(d){
                //         collect_video_loading = false;
                //         var collectCount = parseInt(collectEle.next().text());
                //         if(d.isSuccess == 1){
                //             collectEle.removeClass('collected');
                //             collectEle.next().text(parseInt(collectCount) - 1);
                //         }
                //     },
                //     error:function(){
                //         collect_video_loading = false;
                //     }
                // });
            }

        },
        initVideoShareEvent:function(){
            var shareUrl = $conf.domain + 'shareVideo.php?videoid='+videoID;
            $('#wx-share-qrcode').qrcode({render:'canvas', text:shareUrl, width:120,height:120});
            $('#shareModal .modalBody .url_text').val(location.href);

            var b = a('.shareoptDiv');
            var dom = {
                shareBtn:b,
                shareContent: b.find('#shareModal'),
                copyBtn: b.find('#copyUrl'),
                shareInput: b.find('.url_text')
            }
            var that = {};
            that.alert = false;
            dom.shareBtn.on("mouseenter", function(){
                if(!that.hasCopy){
                    b.addClass('onhover').css('height', '32px');
                    dom.shareContent.removeClass('none');
                    dom.copyBtn.zclip({
                        path:$conf.domain+'static/js/ZeroClipboard.swf',
                        copy:dom.shareInput.val(),
                        afterCopy: function(){
                            if(!that.alert){
                                alert('已成功复制到您的剪切板');
                            }
                            that.alert = true;
                        }
                    });
                    that.hasCopy = !0;
                }
                $('#shareModal #wx-share-qrcode').hide();
            });

            dom.shareContent.on('hover', function(){
                dom.shareContent.removeClass('none');
            },function () {
                a('.zclip').remove();
                that.hasCopy = !1;
                dom.shareContent.addClass('none');
            });
            dom.shareBtn.on('mouseleave', function(){
                that.timer = setTimeout(function(){
                    //b.removeClass('onhover').css('height','');
                    dom.shareContent.addClass('none');
                    a('.zclip').remove();
                    that.hasCopy = !1;
                }, 100);
                $('#shareModal #wx-share-qrcode').hide();
            });
            dom.copyBtn.on('click', function(){
                that.alert = false;
            });
            $('#shareModal .moreShare .shareBtn span').click(function(){
                var option = {
                    url:shareUrl,
                    title:document.title +'/'+$('.publisher_name').text(),
                    sumary:document.title +'/'+$('.publisher_name').text()
                }
                var cmd = $(this).attr('data-cmd');
                var cmdData = {
                    tsina:'weibo',
                    tqq:'qq',
                    tqzone:'qq',
                    wx:'wechat'
                }
                var shareDesc = getShareContent(cmdData[cmd],$ROOM.title,$ROOM.nick,$ROOM.uid,pageUser.uid);

                if(cmd){
                    if(cmd == 'wx'){
                        $('#shareModal #wx-share-qrcode').show();
                        //Share.init(option,{channel:'wx',left:300,top:300});
                    }else{
                        option = {
                            url:shareUrl,
                            title:shareDesc.title,
                            // sumary:shareDesc.content,
                            desc:shareDesc.content,
                            pics:"https://rpic.douyucdn.cn/a1701/11/14/52876_170111145251.jpg"
                        }
                        Share.init(option,{channel:cmd});
                        $('#shareModal #wx-share-qrcode').hide();
                    }
                }
            });
        },
        initVideoPlayer:function(d){
            runSwfFunction('videoPlayer', 'inputURL', d.videoUrl);
            var angle = 1;
            if(d.orientation == 1 || d.orientation == 4){
                angle = 0;
            }
            runSwfFunction('videoPlayer', 'angle', angle);
        },
        initCommentContent:function(){
            //定义
            var userface = getCookie('_uface') || $conf.defaultFace;
            var html = '<span class="user-face"><img src="'+userface+'" alt=""/></span>';
            if(check_user_login()){
                html += '<textarea id="commentval" class="editbody" maxlength="300" wrap="virtual" placeholder="这个录像怎么样？想说什么就马上说吧！"></textarea>';
            }else{
                html += '<div class="editbody" maxlength="300" wrap="virtual">';
                html += '<div class="unlogin">发表评论请先 <a onclick="loginFast.login(0)">登录</a>或<a onclick="loginFast.login(1)">注册</a></div>';
                html += '</div>';
            }
            html += '<div id="publishcomment">发送</div><div class="clear"></div>';
            a('.videocomment .editbodyDiv').html(html);

            $("#publishcomment").bind('click', function(){
                if(!check_login()){
                    return;
                }
                if(!check_phoneStatus(1))
                    return;
                var commentval = a('#commentval').val();
                commentval = a.trim(commentval);
                commentval = commentval.replace(/\r\n/,'');

                if(!commentval){
                    console.log('内容不能为空');
                    return;
                }
                if(commentval.length > 300){
                    console.log('字数超过限制');
                    return;
                }
                var postdata = {};
                postdata.videoID = videoID
                postdata.uid = pageUser.uid
                postdata.encpass = pageUser.enc
                postdata.comment = commentval;
                // postdata.rate = 2;

                var requestUrl = $conf.api + 'video/commentVideo.php';
                var requestData = postdata;

                ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                    $("#commentval").val('');
                    $('.editheader').text("0/300");
                    VRoom.initCommentRequest();

                },function (responseData) {
                    if(responseData.type == 1)
                        alert('评论失败');
                    else
                        alert(responseData.desc);
                });

                // a.ajax({
                //     url: $conf.api + 'commentVideo.php',
                //     type:'post',
                //     dataType:'json',
                //     data:postdata,
                //     success:function(d){
                //         //评论成功,在评论列表首行插入
                //         //或者重新发送请求？ 这样做不好
                //         if(d.isSuccess == 1){
                //             $("#commentval").val('');
                //             $('.editheader').text("0/300");
                //             VRoom.initCommentRequest();
                //         }else if(d.isSuccess == 0){
                //             alert('您已经评论过了');
                //         }
                //     }
                // });
            });
        },
        initCommentEmoji:function(){
            var options = {
                id:'facebox',
                path:'static/img/emoji/',
                assign:'commentval',
                tip:'em_',
                position:'top',
                allCount:22,
                rowCount:8
            };
            var selector = '.emoji';
            Emoji.init(selector,options);
        },
        initCommentRequest:function(){
            var self = this;
            var requestUrl = $conf.api + 'video/getVideoComment.php';
            var requestData = {
                videoID:videoID,
                page:1,
                size:commentSize
            };
            ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
               if(responseData.total){
                   self.initComment(responseData);
               }
            });
            // a.ajax({
            //     url:$conf.api+'getVideoComment.php',
            //     type:'post',
            //     dataType:'json',
            //     data:{
            //         videoID:videoID,
            //         page:1,
            //         size:commentSize
            //     },
            //     success:function(d){
            //         if(d.total){
            //             self.initComment(d);
            //         }
            //     }
            // });
        },
        initComment:function(d){
            this.initCommentList(d.list);
            this.initPageCode(d.total);
        },
        initCommentList:function(d){
            var htmlStr = '';
            for(var i in d){
                htmlStr += this.onecommentHtml(d[i]);
            }
            if(htmlStr) a('.allcomment .commentbody').html(htmlStr);
        },
        initPageCode:function(allCount){
            var self = this;
            if(allCount > commentSize){
                var pageCount = parseInt(allCount / commentSize);
                if(allCount % commentSize != 0){
                    pageCount += 1;
                }
                $('.pageIndex').remove();
                $('.liveRoom_left').append('<div class="pageIndex"></div>');
                $('.pageIndex').createPage({
                    pageCount:pageCount,
                    backFn:function(page){
                        var requestUrl = $conf.api + 'video/getVideoComment.php';
                        var requestData = {
                            videoID:videoID,
                            page:page,
                            size:commentSize
                        };
                        ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                            if(responseData.total){
                                self.initCommentList(responseData.list);
                            }
                        });
                        // a.ajax({
                        //     url:$conf.api+'getVideoComment.php',
                        //     type:'post',
                        //     dataType:'json',
                        //     data:{
                        //         videoID:videoID,
                        //         page:page,
                        //         size:commentSize
                        //     },
                        //     success:function(d){
                        //         if(d.total){
                        //             self.initCommentList(d.commentList);
                        //         }
                        //     }
                        // });
                    }
                });
            }else{
                $('.pageIndex').remove();
            }
        },
        initAnchorVideoList:function(d){
            var self = this;
            var requestUrl = $conf.api + 'video/getVideoList.php';
            var requestData = {
                luid:d.uid,
                size:6
            };
            ajaxRequest({url:requestUrl,data:requestData},function(responseData){
                var video = responseData.list;
                for(var i in video){
                    try {
                        var htmlstr = self.videoListOneHtml(video[i]);
                    }catch(e){
                        console.log(e);
                    }
                    console.log(htmlstr);
                    $('.videoListDiv .tabcon').eq(0).append(htmlstr);
                }
                angleImage();
                $('.videolist').eq(0).mCustomScrollbar({
                    scrollInertia:400
                });
            })
            // a.ajax({
            //     url:$conf.api + 'getVideoList.php',
            //     type:'post',
            //     dataType:'json',
            //     data:{
            //         userID: d.uid,
            //         size:6
            //     },
            //     success:function(d){
            //         console.log(d);
            //         if(d.videoList[0]){
            //             var video = d.videoList;
            //             for(var i in video){
            //                 var htmlstr = self.videoListOneHtml(video[i]);
            //                 a('.videoListDiv .tabcon').eq(0).append(htmlstr);
            //             }
            //             angleImage();
            //             $('.videolist').eq(0).mCustomScrollbar({
            //                 scrollInertia:400
            //             });
            //         }
            //     }
            // });
        },
        initSimilarVideoList:function(){
            var self = this;
            var requestUrl = $conf.api + 'video/similarVideo.php';
            var requestData = {size:6,videoID:videoID};
            ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                var video = responseData.list;
                for(var i in video){
                    var htmlstr = self.videoListOneHtml(video[i]);
                    a('.videoListDiv .tabcon').eq(1).append(htmlstr);
                }
                angleImage();
                $('.videolist').eq(1).mCustomScrollbar({
                    scrollInertia:400
                });
            });
            // a.ajax({
            //     url:$conf.api + 'similarVideo.php',
            //     type:'post',
            //     dataType:'json',
            //     data:{
            //         size:6,
            //         videoId:videoID
            //     },
            //     success:function(d){
            //
            //         if(d.list[0]){
            //             var video = d.list;
            //             for(var i in video){
            //                 var htmlstr = self.videoListOneHtml(video[i]);
            //                 a('.videoListDiv .tabcon').eq(1).append(htmlstr);
            //             }
            //             angleImage();
            //             $('.videolist').eq(1).mCustomScrollbar({
            //                 scrollInertia:400
            //             });
            //         }
            //     }
            // });
        },
        initLivingNowList:function(d){
            var self = this;
            var requestUrl = $conf.api + 'other/homePageGameList.php';
            var requestData = {
                gameID:d.gameID,
                size:6
            };

            ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                var live = responseData.list;
                var htmlstr = '';
                for(var i in live){
                    htmlstr += self.liveListOneHtml(live[i]);
                }

                a('.liveNowListDiv .livelist').html(htmlstr);
                angleImage();
            });
            // a.ajax({
            //     url:$conf.api + 'getLiveList.php',
            //     type:'post',
            //     dataType:'json',
            //     data:{
            //         gameTypeID: d.gameTypeID,
            //         gameID: d.gameID,
            //         size:6
            //     },
            //     success:function(d){
            //         if(d.liveList[0]) {
            //             var live = d.liveList;
            //             var htmlStr = '';
            //             for (var i in live) {
            //                 htmlStr += self.liveListOneHtml(live[i]);
            //             }
            //             a('.liveNowListDiv .livelist').html(htmlStr);
            //             angleImage();
            //         }
            //     }
            // })
        },

        onecommentHtml:function(d){
            var htmlstr = '';
            htmlstr += '<div class="commentone">';
            htmlstr += '<span class="userface">';
            htmlstr += '<img src="'+ d.head+'">';
            htmlstr += '</span>';
            htmlstr += '<div class="commentinfo">';
            htmlstr += '<p class="nick">' + d.nick+ '</p>';
            htmlstr += '<div class="comment_content">'+ replace_em(d.comment)+'</div>';
            htmlstr += '<div class="comment_time">' + commenttime(d.ctime)+'</div>';
            htmlstr += '</div>';
            htmlstr += '<div class="clear"></div>';
            htmlstr += '</div>';
            return htmlstr;

            function commenttime (time){
                var str = [
                    '年',
                    '月',
                    '天',
                    '小时',
                    '分钟',
                ];

                var format_time = time ;

                var year = js_date_format("yyyy", format_time);
                var day = js_date_format("d", format_time);
                var month = js_date_format('M', format_time);

                var yearBetween = new Date().getFullYear() - year;

                if(yearBetween != 0){
                    return js_date_format("yyyy-MM-dd HH:mm");
                }else{
                    var monthBetween = new Date().getMonth() + 1 -month;
                    if(monthBetween != 0){
                        return js_date_format("MM-dd HH:mm", format_time);

                    }else{
                        var dayBetween = day - new Date().getDay();

                        if( dayBetween == 1){
                            return '昨天'.js_date_format("HH:mm", format_time);
                        }else if(dayBetween > 1) {
                            return js_date_format("MM-dd HH:mm", format_time);
                        }
                    }

                }

                var t = calTime(time);
                console.log(t);
                // for(var i in t)
                //     if(t[i] > 0 && i < 5)
                //         return t[i] + str[i] + "前";

                for(var i in t){
                    if(t[i] > 0 && i < 5){
                        if(t[3] > 2){
                            return js_date_format("HH:mm",format_time);
                        }else{
                            return t[i] + str[i] + "前";
                        }
                    }
                }

                return '刚刚';
            }
        },

        videoListOneHtml:function(d){
            var videoUrl = $conf.domain + 'videoRoom.php?videoid='+ d.videoID;
            var htmlStr = '';
            var imageClass = (d.orientation == 0 && d.ispic == 1) ? $conf.angleImage : '';
            if(d.videoID == videoID){
                htmlStr += '<div class="videoOne current">';
            }else{
                htmlStr += '<div class="videoOne">';
            }
            htmlStr += '<a href="'+videoUrl+'">';
            htmlStr += '<div class="vinfo_left left vPoster">';
            htmlStr += '<img class="'+imageClass+'" src="' + d.poster+'">';
            htmlStr += '</div>';
            htmlStr += '<div class="vinfo_right  vInfo">';
            htmlStr += '<div class="v_title">' + d.title + '</div>';
            htmlStr += '<div class="v_gamet">' + d.gameName + '</div>'
            htmlStr += '<div class="v_otherinfo">'
            htmlStr += '<span class="icon anchor_icon unplayIcon2"></span>';
            htmlStr += '<span class="text">' + d.viewCount + '</span>';
            htmlStr += '<span class="icon anchor_icon commentIcon" style="margin-top: 1px;"></span>';
            htmlStr += '<span class="text">' + d.commentCount + '</span>';
            htmlStr += '</div>';
            htmlStr += '</div>';
            htmlStr += '<div class="clear"></div>';
            htmlStr += '</a>';
            htmlStr += '</div>';

            return htmlStr;
        },

        liveListOneHtml: function (d) {
            var liveUrl = $conf.domain + d.roomID;
            var htmlStr = '';
            var imageClass = (d.orientation == 0 && d.ispic == 1) ? $conf.angleImage : '';
            htmlStr += '<div class="videoOne">';
            htmlStr += '<a href="'+liveUrl+'">';
            htmlStr += '<div class="vinfo_left left vPoster">';
            htmlStr += '<img class="'+imageClass+'" src="' + d.poster+'">';
            htmlStr += '</div>';
            htmlStr += '<div class="vinfo_right  vInfo">';
            htmlStr += '<div class="v_title">' + d.title + '</div>';
            htmlStr += '<div class="v_gamet">' + d.gameName + '</div>'
            htmlStr += '<div class="v_otherinfo">'
            htmlStr += '<span class="icon anchor_icon anchor"></span>';
            htmlStr += '<span class="text">' + d.nick + '</span>';
            htmlStr += '<span class="icon anchor_icon viewerIcon2" style="margin-top: 1px;"></span>';
            htmlStr += '<span class="text">' + d.viewCount + '</span>';
            htmlStr += '</div>';
            htmlStr += '</div>';
            htmlStr += '<div class="clear"></div>';
            htmlStr += '</a>';
            htmlStr += '</div>';

            return htmlStr;
        }


    };
}(jQuery);