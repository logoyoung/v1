/**
 * 获取录像信息 采用接口形式，而不是 预先加载
 * 接口地址: main/a/getVideoInfo.php;
 * 去除掉$VROOM
 *
 */

var VRoom = function(){
    var vroom;

    var a = jQuery;
    var _vroom = $VROOM;

    var _isLogin = pageUser.isLogin;
    var _isFollow = _vroom.isFollow == '1' ? true : false;
    $conf = conf.getConf();

    vroom = {
        pageHtml: {
            navPlayerStr:function(){
                var htmlstr = ''
                htmlstr += '<div class="player_face"><img src=""></div>';
                htmlstr += '<div class="player_info">';
                htmlstr += '<p class="publisher_name"></p>';
                htmlstr += '<div class="clear"></div>';
                htmlstr +='<div class="player_otherdesc ">';
                htmlstr +='<div class="videodesc left">';
                htmlstr +='<span class="anchor_icon videoCountIcon"></span>';
                htmlstr +='<span></span>';
                htmlstr +='<div class="clear"></div>';
                htmlstr +='</div>';
                htmlstr +='<div class="onplay left">';
                htmlstr +='<span class="anchor_icon unplayIcon">';
                htmlstr +='</span>';
                htmlstr +='<span></span>';
                htmlstr +='<div class="clear"></div>';
                htmlstr +='</div>';
                htmlstr +='<div class="clear"></div>';
                htmlstr +='</div>';
                return htmlstr;
            },
            navAttentionStr:function(){
                var htmlstr = '';
                htmlstr += '<div class="nav_attention">';
                htmlstr += '<div id="followbtn" class="nav_attention_right"><span class="anchor_icon followIcon3"></span><em style="font-style: normal">关注</em></div>';
                htmlstr += '<div class="nav_attention_left"></div>';
                htmlstr += '<div class="clear"></div>'
                htmlstr += '</div>';
                htmlstr += '<div class="enterliveRoom">进入直播间</div>';
                htmlstr +='<div class="clear"></div>'

                return htmlstr;
            },
            videoListOne:function(d){
                var videourl = $conf.domain + 'videoRoom.php?videoid=' + d.videoID;
                var posterurl = d.poster;
                var vtitle = d.title;
                var gamename = d.gameName;
                var playcount = d.viewCount;
                var collectcount = d.collectCount;

                var htmlstr = '';
                htmlstr += '<div class="videoOne">';
                htmlstr += '<a href="'+videourl+'">';
                htmlstr += '<div class="vinfo_left left vPoster">';
                htmlstr += '<img src="' +posterurl+'">';
                htmlstr += '</div>';
                htmlstr += '<div class="vinfo_right  vInfo">';
                htmlstr += '<div class="v_title">' + vtitle + '</div>';
                htmlstr += '<div class="v_gamet">' + gamename + '</div>'
                htmlstr += '<div class="v_otherinfo">'
                htmlstr += '<span class="icon anchor_icon unplayIcon"></span>';
                htmlstr += '<span class="text">' + playcount + '</span>';
                htmlstr += '<span class="icon anchor_icon commentIcon" style="margin-top: 1px;"></span>';
                htmlstr += '<span class="text">' + collectcount + '</span>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '</a>';
                htmlstr += '</div>';

                return htmlstr;
            }
        },
        init:function(){
            var self = this;
            self.initRoomContent_nav();
            self.initSendComment();
            self.initVideoOpt();
            self.initCommentList();
            self.initAnchorVideoList();
        },
        initRoomContent_nav:function(){
            var self = this;
            a('.live_nav_player').html(self.pageHtml.navPlayerStr());
            a('.live_nav_opshow').html(self.pageHtml.navAttentionStr());

            (function(){
            //定义录像作品数以及状态以及时间长度
                // 需要联系 增加live表的结束时间
                var postdata = {};
                console.log(_vroom);
                postdata.targetUserID = _vroom.publisherUserID || 0;
                console.log(postdata);
                if(_isLogin){
                    postdata.uid = getCookie('_uid') || 0;
                    postdata.encpass = getCookie('_enc') || '';
                }
                function s(d){
                    var player = a('.live_nav_player');
                    var playerFace = player.find('.player_face img');
                    var playerNick = player.find('.publisher_name');
                    var playerVideoCount = player.find('.videodesc span:eq(1)');
                    var playerLivingInfo = player.find('.onplay');

                    var lstime = d.liveStime || false;
                    var letime = d.liveEtime || false;

                    playerFace.attr('src', $conf.img + d.userPicURL);
                    playerNick.text(d.nickName);
                    playerVideoCount.text('录像作品数：' + d.videoCount);

                    (function(){
                        if(d.liveStatus == 100){
                            playerLivingInfo.find('span:eq(1)').text('正在直播');
                            playerLivingInfo.addClass('playing');
                            playerLivingInfo.find('span:eq(0)').addClass('playingIcon').removeClass('unplayIcon');
                        }else{
                            playerLivingInfo.find('span:eq(1)').text('暂未直播');
                            playerLivingInfo.removeClass('playing');
                            playerLivingInfo.find('span:eq(0)').addClass('unplayIcon').removeClass('playingIcon');

                        }
                    }());
                }
                function e(d){

                }
                function followcount(d){
                    a('.nav_attention_left').text(_vroom.fansCount);
                    if(d.isFollow && _isLogin) {
                        a('#followbtn').addClass('followed').find('em').text('已关注').css('display','block');
                        a('#followbtn .anchor_icon').remove();
                        a('#followbtn.followed').hover(function(){
                            if($(this).hasClass('followed')){
                                $(this).find('em').text('取消关注');
                            }
                        }, function(){
                            if($(this).hasClass('followed')) {
                                $(this).find('em').text('已关注');
                            }
                        })
                    }
                }
                var requestUrl = $conf.api + 'video/getVideoPublishInfo.php';
                var requestData = postdata;
                ajaxRequest({url:requestUrl,data:requestData},function(responseData){

                });
                $.ajax({
                    url:'http://' + document.domain + '/main/a/getVideoPublisherInfo.php',
                    type:'post',
                    dataType:'json',
                    data:postdata,
                    success:function(d){
                        console.log(d);
                        s(d);
                        followcount(d);
                    }
                });

            }());
            (function(){//定义关注事件以及进入直播间点击事件
                var fbtn = a('#followbtn');
                var enroom = a('.enterliveRoom');

                function followRequest(){
                    var requestUrl = $conf.api + 'room/followUser.php';
                    var requestData = {luid:_vroom.publisherUserID,uid:getCookie('_uid') || 0,encpass:getCookie('_enc') || ''};

                    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                        a('#followbtn').addClass('followed').find('em').text('已关注').css('display','block');
                        a('#followbtn .anchor_icon').remove();
                        a('#followbtn.followed').hover(function(){
                            if($(this).hasClass('followed')){
                                $(this).find('em').text('取消关注');
                            }
                        }, function(){
                            if($(this).hasClass('followed')) {
                                $(this).find('em').text('已关注');
                            }
                        })
                        var attenLeft = a('.nav_attention_left')
                        var fc = attenLeft.text() || 0;
                        if(fc) attenLeft.text(parseInt(fc) + 1);
                    })
                };
                function followCancelRequest(){
                    var requestUrl = $conf.api + 'room/followUserCancel.php';
                    var requestData = {luid:_vroom.publisherUserID,uid:getCookie('_uid') || 0,encpass:getCookie('_enc') || ''};
                    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                        a('#followbtn').removeClass('followed').find('em').text('关注').css('display','');
                        if(!a('#followbtn .anchor_icon').get()[0]){
                            a('#followbtn').append('<span class="anchor_icon followIcon3"></span>');
                        }
                        var attenLeft = a('.nav_attention_left')
                        var fc = attenLeft.text() || 0;
                        if(fc) attenLeft.text(parseInt(fc) - 1);
                    })

                    // var postdata = {};
                    // postdata.targetUserID = _vroom.publisherUserID;
                    // postdata.uid = getCookie('_uid') || 0;
                    // postdata.encpass = getCookie('_enc') || '';
                    // a.ajax({
                    //     url:'http://' + document.domain + '/a/followUserCancel.php',
                    //     type:'post',
                    //     dataType:'json',
                    //     data:postdata,
                    //     success:function(d){
                    //         if(d.isSuccess == '1'){
                    //             a('#followbtn').removeClass('followed').find('em').text('关注').css('display','');
                    //             if(!a('#followbtn .anchor_icon').get()[0]){
                    //                 a('#followbtn').append('<span class="anchor_icon followIcon3"></span>');
                    //             }
                    //             var attenLeft = a('.nav_attention_left')
                    //             var fc = attenLeft.text() || 0;
                    //             if(fc) attenLeft.text(parseInt(fc) - 1);
                    //         }
                    //     }
					//
                    // });
                }
                fbtn.bind('click',function(){
                    if(fbtn.hasClass('followed'))
                        followCancelRequest();
                    else
                        followRequest();
                });
                enroom.bind('click',function(){
                    location.href = 'http://' + document.domain + '/main/liveRoom.php?luid=' +   _vroom.publisherUserID;
                });
            }());
        },
        initVideoOpt:function(){//定义播放收藏选项
            var viewcount_text = a('.liveRoom_opt .videodetail .text:eq(0)');
            var commcount_text = a('.liveRoom_opt .videodetail .text:eq(1)');
            var upcount_text = a('.liveRoom_opt .videoopt .text:eq(0)');
            var collectcount_text = a('.liveRoom_opt .videoopt .collectoptDiv .text');

            viewcount_text.text(_vroom.totalViewCount);
            commcount_text.text(_vroom.commentCount || 0);
            upcount_text.text(_vroom.upcount || 0);
            collectcount_text.text(_vroom.collectCount);

            var collect = $('#collectVideo');
            if(_vroom.isCollect) collect.addClass('collected');

            (function(){
                function collectRequest(){
                    var requestUrl = $conf.api + 'room/collectVideo.php';
                    var requestData = {
                        videoID:_vroom.videoID,
                        uid:getCookie('_uid') || 0,
                        encpass:getCookie('_enc')||''
                    };
                    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                        collect.addClass('collected');
                        collectcount_text.text(parseInt(_vroom.collectCount) + 1);
                        _vroom.collectCount ++;
                    });
                    /*var postdata = {};
                    postdata.videoID = _vroom.videoID;
                    postdata.uid = getCookie('_uid') || 0;
                    postdata.encpass = getCookie('_enc') || '';
                    a.ajax({
                        url:'http://' + document.domain + '/main/a/collectVideo.php',
                        type:'post',
                        dataType:'json',
                        data:postdata,
                        success:function(d){
                            if(d.isSuccess == '1'){
                                collect.addClass('collected');
                                collectcount_text.text(parseInt(_vroom.collectCount) + 1);
                                _vroom.collectCount ++;
                            }
                        }
                    });*/
                }
                function collectCancelRequest(){
                    var requestUrl = $conf.api + 'video/cancelCollectVideo.php';
                    var requestData = {
                        videoIDList:_vroom.videoID,
                        uid:getCookie('_uid'),
                        encpass:getCookie('_enc')
                    };

                    ajaxRequest({url:requestUrl,data:requestData}, function (responseData) {
                        collect.removeClass('collected');
                        collectcount_text.text(parseInt(_vroom.collectCount) - 1);
                        _vroom.collectCount --;
                    });
                    /*var postdata = {};
                    postdata.videoIDList = _vroom.videoID;
                    postdata.uid = getCookie('_uid') || 0;
                    postdata.encpass = getCookie('_enc') || '';
                    a.ajax({
                        url:'http://' + document.domain + '/main/a/cancelCollectVideo.php',
                        type:'post',
                        dataType:'json',
                        data:postdata,
                        success:function(d){
                            if(d.isSuccess == '1'){
                                collect.removeClass('collected');
                                collectcount_text.text(parseInt(_vroom.collectCount) - 1);
                                _vroom.collectCount --;
                            }
                        }
                    });*/
                }
                collect.bind('click',function(){
                    if(collect.hasClass('collected'))
                        collectCancelRequest();
                    else
                        collectRequest();
                });
            }());

            var upvideo = a('#upvideo');
            (function(){//视频点赞事件
                upvideo.bind('click',function(){
                    //...
                });
            }());

        },
        initSendComment:function(){
            //定义评论发表
            var self = this;
            var edithead = a('.videocomment .editheader');
            var editbodyDiv = a('.videocomment .editbodyDiv');
            var emoji = a('.videocomment editfooter emoji icon_emoji');
            var publishcomm = a('#publishcomment');

            (function(){//定义评论内容
                var editbodyHtmlStr = '';
                if(_isLogin){
                    editbodyHtmlStr += '<textarea id="commentval" style="width: 100%" class="editbody" maxlength="300" wrap="virtual" placeholder="这个录像怎么样？想说什么就马上说吧！"></textarea>';
                }else{
                    editbodyHtmlStr += '<div class="editbody" maxlength="300" wrap="virtual">';
                    editbodyHtmlStr += '<div class="placeholder">这个录像怎么样？想说什么就马上说吧！</div>'
                    editbodyHtmlStr += '<div class="unlogin">发表评论请先 <a onclick="loginFast.login(0)">登录</a>或<a onclick="loginFast.login(1)">注册</a></div>';
                    editbodyHtmlStr += '</div>';
                }
                //editbodyDiv.html(editbodyHtmlStr);
            }());

            var comment = a("#commentval")
            comment.bind('input propertychange',function(){//字数统计
                var len = $(this).val().length;
                $('.editheader').text(len + "/300");
            });

            publishcomm.bind('click',function(){
                if(!check_login()){
                    return;
                }
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


                var requestUrl = $conf.api + 'video/commentVideo.php';
                var requestData = {
                    videoID:_vroom.videoID,
                    uid:getCookie('_uid'),
                    encpass:getCookie('_enc'),
                    comment:commentval
                };

                ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                    $("#commentval").val('');
                    $('.editheader').text("0/300");
                    self.initCommentList();
                },function(responseData){
                    alert('您已经评论过了');
                });
                // var postdata = {};
                // postdata.videoID = _vroom.videoID
                // postdata.uid = getCookie('_uid') || 0;
                // postdata.encpass = getCookie('_enc') || '';
                // postdata.comment = commentval;
                // postdata.rate = 2;
                // a.ajax({
                //     url:'http://' + document.domain + '/main/a/commentVideo.php',
                //     type:'post',
                //     dataType:'json',
                //     data:postdata,
                //     success:function(d){
                //         //评论成功,在评论列表首行插入
                //         //或者重新发送请求？ 这样做不好
                //         if(d.isSuccess == 1){
                //             $("#commentval").val('');
                //             $('.editheader').text("0/300");
                //             self.initCommentList();
                //             //var one = d.commentData;
                //             //addToCommentList(one);
                //         }else if(d.isSuccess == 0){
                //             alert('您已经评论过了');
                //         }
                //     }
                // });

                //function addToCommentList(d){
                //    var one = self.onecommentHtml(d);
                //    var all = parseInt(a('.allcomment .commentheader span').text()) || 0;
                //
                //    if(all >= 4) a('.allcomment .commentbody .commentone:last').remove();
                //
                //    a('.allcomment .commentbody .commentone:first').before(one);
                //    a('.allcomment .commentheader span').text(++all);
                //
                //    重新定义页数
                //}
            });

        },
        initCommentList:function(){//评论列表
            var self = this;
            var requestUrl = $conf.api + 'video/getVideoComment.php';
            var requestData = {
                videoID:_vroom.videoID,
                page:1,
                size:4
            };
            ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                var total = Number(responseData.total);
                var commentList = responseData.list;
                for(var i in commentList){
                    var htmlstr = self.onecommentHtml(commentList[i]);
                    a('.allcomment .commentbody').append(htmlstr);
                }
                $('.allcomment .commentheader span').text('（' + total+'）');
                $('.liveRoom_opt .videoCommentIcon + .text').text(total);
            });
            // a.ajax({
            //     url:'http://' + document.domain + 'video/getVideoComment.php',
            //     type:'post',
            //     dataType:'json',
            //     data:{
            //         videoID:_vroom.videoID,
            //         page:1,
            //         size:4
            //     },
            //     success:function(d){
            //         d.total = parseInt(d.total) || 0;
            //         var commentList = d.commentList;
            //         for(var i in commentList){
            //             var htmlstr = self.onecommentHtml(commentList[i]);
            //             a('.allcomment .commentbody').append(htmlstr);
            //         }
            //         a('.allcomment .commentheader span').text('（' + d.total+'）');
            //         $('.liveRoom_opt .videoCommentIcon + .text').text(d.total);
            //     }
            // });
        },
        onecommentHtml:function(d){
            function commenttime (time){
                var str = [
                    '年',
                    '月',
                    '天',
                    '小时',
                    '分钟',
                ];
                var t = calTime(time);
                console.log(t);
                for(var i in t)
                    if(t[i] > 0 && i < 5)
                        return t[i] + str[i] + "前";

                return '刚刚';
            }
            var imgsrc = d.head;
            var nick = d.nick;
            var comment = replace_em(d.comment);
            var commenttimes = commenttime(d.ctime);

            var htmlstr = '';
            htmlstr += '<div class="commentone">';
            htmlstr += '<span class="userface">';
            htmlstr += '<img src="'+ imgsrc+'">';
            htmlstr += '</span>';
            htmlstr += '<div class="commentinfo">';
            htmlstr += '<p class="nick">' + nick+ '</p>';
            htmlstr += '<div class="comment_content">'+ comment+'</div>';
            htmlstr += '<div class="comment_time">' + commenttimes+'</div>';
            htmlstr += '</div>';
            htmlstr += '<div class="clear"></div>';
            htmlstr += '</div>';
            return htmlstr;
        },
        initAnchorVideoList:function(){
            var self = this;
            var requestUrl = $conf.api + 'video/getVideoList';
            var requestData = {
                luid:_vroom.publisherUserID,
                size:6
            };

            ajaxRequest({url:requestUrl,data:requestData},function(responseData){
                if(responseData.list.length){
                    var video = responseData.list;
                    for(var i in video){
                        var htmlstr = self.pageHtml.videoListOne(video[i]);
                        //console.log(htmlstr);
                        a('.videoListDiv .tabcon').eq(0).append(htmlstr);
                    }
                    $('.videolist').mCustomScrollbar({
                        scrollInertia:400
                    });
                }
            });

            // a.ajax({
            //     url:'http://' + document.domain + '/main/a/getVideoList.php',
            //     type:'post',
            //     dataType:'json',
            //     data:{
            //         userID:_vroom.publisherUserID,
            //         size:6
            //     },
            //     success:function(d){
            //         console.log(d);
            //         if(d.videoList[0]){
            //             var video = d.videoList;
            //             for(var i in video){
            //                 var htmlstr = self.pageHtml.videoListOne(video[i]);
            //                 //console.log(htmlstr);
            //                 a('.videoListDiv .tabcon').eq(0).append(htmlstr);
            //             }
            //             $('.videolist').mCustomScrollbar({
            //                 scrollInertia:400
            //             });
            //         }
            //     }
            // });
        }
    };

    return {
        init:function(){
            vroom.init();
        }
    }
}();