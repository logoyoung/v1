var VideoPlayBox;
(function(){
    var $conf = conf.getConf();
    var ajaxDefault = {
        url:'',
        type:'post',
        dataType:'json',
        data:{},
        success:function(){}
    };

    VideoPlayBox = {
        boxHtml:function(){
            var htmlstr = '';
            htmlstr += '<div class="videoPlay">';
            htmlstr += '    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"id="videoPlayer" width="100%" height="100%"codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab">';
            htmlstr += '        <param name="movie" value="../../static/mp4player.swf" />';
            htmlstr += '        <param name="quality" value="high" />';
            htmlstr += '        <param name="bgcolor" value="#869ca7" />'
            htmlstr += '        <param name="allowScriptAccess" value="always" />';
            htmlstr += '        <embed src="../../static/mp4player.swf"  bgcolor="#869ca7" width="100%" height="100%" name="videoPlayer" align="middle"play="true" loop="false" quality="high" allowScriptAccess="sameDomain"type="application/x-shockwave-flash"pluginspage="http://www.macromedia.com/go/getflashplayer">';
            htmlstr += '   </object>';
            htmlstr += '   <div class="closeVideo personal_icon close2"></div>';
            htmlstr += '</div>';

            return htmlstr;
        },
        create:function(){
            var self = this;
            if($('#VideoPlayBox')){
                self.remove();
            }
            $('<div/>',{
                id:'videoPlayBox',
                'class':'videoPlayBox',
                style:'position:fixed; left:50%;top:50%;z-index:1000;',
                html:self.boxHtml()
            }).appendTo(document.body);

            $('#videoPlayBox .videoPlay .closeVideo').bind('click', self.hide);
        },
        remove:function(){
            if(!$('#videoPlayBox')){
                return;
            }
            Mask.remove();
            $('#videoPlayBox').remove();
        },
        initVideo:function(videoid){
            var self = this;
            var player = getSwfObject('videoPlayer');
            if(!player)
                return;

            self.show();
            var interval = setInterval(function(){//防止 chatProxy 未加载完而出现错误
                try{
                    if(player.PercentLoaded() == 100){
                        player.inputURL('http://dev-img.huanpeng.com/v/3/d/','3d58822d69cde47beab5291f4896c4ef.mp4');
                    }
                }catch(e){}
            },500);

            function getSwfObject(obj){
                if (navigator.appName.indexOf("Microsoft") != -1) {
                    return window[obj];
                } else {
                    return document[obj];
                }
            }
        },
        show:function(){
            Mask.creates();
            Mask.box.css('background-color', 'rgba(0,0,0,0.8)');
            $('#videoPlayBox').show();
        },
        hide:function(){
            Mask.remove();
            $('#videoPlayBox').hide();
        }

    }
}());


var MyZone;
(function(a){
    var $conf = conf.getConf();
    var ajaxDefault = {
        url:'',
        type:'post',
        dataType:'json',
        data:{},
        success:function(){}
    }

    MyZone = {
        pageHtml:{
            noPublishedVideoNoticePageHtml:function(){
                var htmlstr = '<div class="noVideo mt-60"><div class="logo"><img src="../../static/img/logo/commerr.png" alt=""/></div><div class="noticeword left "><p>你还没有发布录像</p><p>录制的视频也可以得到观众的送礼哦～</p></div><div class="clear"></div></div>';
                return htmlstr;
            }
        },
        init:function(){
            var self = this;

            self.willPublishModal.init();
            $('.select_tab li').bind('click', function(){
                $('.select_tab li').removeClass('selected');
                $('.tab_con').addClass('none');

                var i = $(this).index();

                $(this).addClass('selected');
                $('.tab_con').eq(i).removeClass('none');

                if(i == 0){
                    $('.zoneopt .delBtnGroup').remove();
                    self.willPublishModal.init();

                }else if(i == 1){
                    $('.zoneopt .delBtnGroup').remove();
                    $('.zoneopt').append('<div class="delBtnGroup right"></div>');
                    self.checkPendingModal.init();
                } else if(i == 2){
                    $('.zoneopt .delBtnGroup').remove();
                    $('.zoneopt').append('<div class="delBtnGroup right"></div>');
                    self.alreadyPublishedModal.init();

                }else{
                    $('.zoneopt .delBtnGroup').remove();
                    self.editBulletinModal.init();
                }
            });

            VideoPlayBox.create();
        },
        /**
         * 待发布录像模块
         */
        willPublishModal:{
            pVideoHtmlstr:function(d){
                var htmlstr = '';
                htmlstr += '<div class="pvideoOne" data-videoid="'+ d.videoID+'">';
                htmlstr += '<div class="liveOne">';
                htmlstr += '<div class="imagecontainer">';
                htmlstr += '<img src="'+ d.posterURL+'">';
                htmlstr += '<div class="previewOptModal">';
                htmlstr += '<div class="previewOpt">预览</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="pvideoinfo">';
                htmlstr += '<div class="pgroup">';
                htmlstr += '<label class="label">录像标题:</label>';
                htmlstr += '<div class="pinfo title">'+ d.videoTitle+'</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="pgroup">';
                htmlstr += '<label class="label">录像时间:</label>';
                htmlstr += '<div class="pinfo">'+ d.videoUploadDate+'</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="pgroup">';
                htmlstr += '<label class="label">录像时长:</label>';
                htmlstr += '<div class="pinfo">'+ d.videoTimeLength+'</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="pgroup">';
                htmlstr += '<label class="label">录像内容:</label>';
                htmlstr += '<div class="pinfo ">'+ d.gameName+'</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                if(d.videoStatus == '0')
                    htmlstr += '<button class="btn published">已发布</button>';

                else
                    htmlstr += '<button class="btn publish">发布</button>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '</div>';

                return htmlstr;
            },

            publishVideoSuccessNoticeBoxHtml:function(){
                //录像发布成功提醒接口
                var htmlstr = '';
                htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:320px;z-index: 1000;">';
                htmlstr += '<div class="theBox" style="padding: 26px 16px">';
                htmlstr += '<div class="box_head">';
                htmlstr += '<div class="closeBox">';
                htmlstr += '<span class="personal_icon close"></span>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="box_body">';
                htmlstr += '<div class="imgLogo"></div>';
                htmlstr += '<p>您的录像发布成功</p>';
                htmlstr += '</div>';
                htmlstr += '<div class="box_foot">';
                htmlstr += '<button style="margin-left:186px;" class="btn close">关闭</button>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';

                return htmlstr;
            },
            initVideoByMenu:function(option){
                var self = this;
                var postData,
                    ajaxOption;
                var size = 4;

                postData = $.extend({
                    uid:getCookie('_uid'),
                    encpass:getCookie('_enc'),
                    size:size,
                    page:1,
                    order:0,
                    gameid:0,
                    gametid:0
                }, option);

                var successFn = function(videoList, videoCount){
                    self.initVideoList(videoList, videoCount);

                    if(videoCount > size){
                        var pageCount = parseInt(videoCount / size);
                        if(videoCount % size != 0){
                            pageCount += 1;
                        }
                        //重新绘制的时候先清除原先的element
                        $('.unpublishtab .pageIndex').remove();
                        $('.unpublishtab').append('<div class="pageIndex"></div>');
                        $('.unpublishtab .pageIndex').createPage({
                            pageCount:pageCount,
                            backFn:function(page){
                                self.pageCallBackFunction(page,postData);
                            }
                        });
                    }else{
                        //如果pageCount为一页，那么则清除翻页按钮
                        $('.unpublishtab .pageIndex').remove();
                    }

                }
                ajaxOption = {
                    url:$conf.api + 'myVideo.php',
                    data:postData,
                    success:function(d){
                        if(d.videoList){
                            $('.unpublishtab .videoList .pvideoOne').remove();
                            successFn(d.videoList, d.videoCount);
                        }
                    }
                };
                ajaxOption = a.extend(ajaxDefault, ajaxOption);
                a.ajax(ajaxOption);
            },
            initVideoList:function(videoList, videoCount){
                var self = this;
                var videoListDiv = $('.unpublishtab .videoList');
                if(videoCount == 0){
                    videoListDiv.html(MyZone.pageHtml.noPublishedVideoNoticePageHtml());
                    return;
                }

                var thePVideoHtmlStr = '';
                for(var i in videoList){
                    thePVideoHtmlStr += self.pVideoHtmlstr(videoList[i]);
                }
                videoListDiv.html(thePVideoHtmlStr);
                //发布录像事件定义;
                self.publishVideo();
                //视频预览点击事件
                $('.unpublishtab .liveOne .previewOpt').bind('click', function(){
                    var videoid = $(this).parents('.pvideoone').data('videoid') || 0;
                    VideoPlayBox.initVideo(videoid);
                })
            },
            pageCallBackFunction:function(page,postData){
                var self = this;
                postData.page = page;
                var requestUrl = $conf.api + 'video/myVideo.php';
                var requestData = postData;
                ajaxRequest({url:requestUrl,data:requestData}, function (responseData) {
                    self.initVideoList(responseData.list);
                });
            },
            publishVideo:function(){
                var self = this;
                var publishBtn = $('.unpublishtab .videoList .pVideoOne .btn.publish');
                if(!publishBtn.get()[0]){
                    //console.log('没有要发布的录像');
                    return;

                }

                var publish = function(){
                    var videoID = a(this).parent().attr('data-videoid');
                    if(!parseInt(videoID)){
                        console.log('无效的视频ID');
                        return;
                    }

                    var element = this;
                    function successFn(){
                        var htmlstr = self.publishVideoSuccessNoticeBoxHtml();
                        NoticeBox.create(htmlstr);
                        $("#noticeBox .close").bind('click', NoticeBox.remove);
                        $(element).unbind('click',publish);
                        $(element).removeClass('publish').addClass('published').text('审核中');

                    }
                    var ajaxOption = {
                        url:$conf.api + 'publishVideo.php',
                        data:{
                            uid:getCookie('_uid'),
                            encpass:getCookie('_enc'),
                            videoID:videoID
                        },
                        success:function(d){
                            if(d.isSuccess == 1){
                                successFn();
                            }
                        }
                    }
                    ajaxOption = a.extend(ajaxDefault, ajaxOption);
                    a.ajax(ajaxOption);
                }
                publishBtn.bind('click', publish);
            },
            menuSelectEvent:function(){
                var self = this;
                var option = {
                    gametid:0,
                    gameid:0,
                    order:0
                };
                $('.unpublishtab .seloptGroup .gametype span').bind('click', function(){
                    $(this).parent().find('.checked').removeClass('checked');
                    $(this).addClass('checked');

                    var gametid = parseInt($(this).attr('data-gametid')) || 0;
                    var gameid = parseInt($(this).attr('data-gameid')) || 0;

                    option.gametid = gametid;
                    option.gameid = gameid;
                    self.initVideoByMenu(option);
                });
                $(".unpublishtab .seloptGroup .orderby span").bind('click', function(){
                    $(this).parent().find('span .personal_icon').removeClass('arrow_bt').addClass('arrow_up');
                    $(this).parent().find('.checked').removeClass('checked');
                    $(this).addClass('checked').find('.personal_icon').removeClass('arrow_up').addClass('arrow_bt');

                    var order = parseInt($(this).attr('data-order')) || 0;
                    option.order = order;
                    self.initVideoByMenu(option);
                });
            },
            initSelectMenu:function(){
                var self = this;
                var gameTypeSelectMenu = $('.unpublishtab .seloptGroup .gametype .gameTypeSelectDiv');
                gameTypeSelectMenu.find('span').remove();
                gameTypeSelectMenu.append('<span class="checked" data-gametid="0" data-gameid="0">全部类型</span>');

                var selectOneHtmlStr = function(d){
                    return '<span data-gametid="'+ d.gametid+'">'+ d.gamename+'</span>';
                }
                var ajaxOption = {
                    url:$conf.api + 'shamApi_getGameTypeList.php',
                    success:function(d){
                        if(d.gameTypeList){
                            for(var i in d.gameTypeList)
                                gameTypeSelectMenu.append(selectOneHtmlStr(d.gameTypeList[i]));
                            gameTypeSelectMenu.find('div.clear').remove();
                            gameTypeSelectMenu.append('<div class="clear"></div>');
                            self.menuSelectEvent();
                        }
                    }
                }
                ajaxOption = $.extend(ajaxDefault, ajaxOption);
                $.ajax(ajaxOption);
            },
            init:function(){
                this.initSelectMenu();
                this.initVideoByMenu();
                //this.menuSelectEvent();
            }
        },

        /**
         *录像审核模块
         */
        checkPendingModal:{
            checkVideoOneHtmlStr:function(d){
                var htmlstr = '';
                var imageClass = (d.angle == 0 && d.ispic == 1) ? $conf.angleImage : '';
                htmlstr = '<div class="liveOne" data-videoid="100">'
                +'<div class="imagecontainer">'
                +'<img class="'+imageClass+'" src="'+d.posterURL+'" alt=""/>'
                +'<div class="live_anchor_name">'
                +'<span>'+d.publisherNickName+'</span>'
                +'</div>'
                +'<div class="previewOptModal">'
                +'<div class="previewOpt">预览</div>'
                +'</div>'
                +'</div>'
                +'<div class="liveInfo">'
                +'<div class="videoName">'+d.videoTitle+'</div>'
                +'<div class="clear"></div>'
                +'<div class="liveDetail">'
                +'<span class="anchor_icon viewerIcon"></span>'
                +'<span>'+d.totalViewCount+'</span>'
                +'<span class="anchor_icon commentIcon"></span>'
                +'<span>'+d.commentCount+'</span>'
                +'<span class="game_name">'+d.gameName+'</span>'
                +'</div>'
                +'</div>'
                +'</div>';

                return htmlstr;
            },
            cancelCheckNoticeBoxHtml:function(){
                var htmlstr = '';
                htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:320px;z-index: 1000;">'
                htmlstr += '<div class="theBox" style="padding: 26px 16px">';
                htmlstr += '<div class="box_head">';
                htmlstr += '<div class="closeBox">';
                htmlstr += '<span class="personal_icon close"></span>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="box_body">';
                htmlstr += '<div class="imgLogo"></div>';
                htmlstr += '<p>确定取消审核这些录像？</p>';
                htmlstr += '</div>';
                htmlstr += '<div class="box_foot">';
                htmlstr += '<button id="ensureCancelCheck" class="btn">确认取消</button>';
                htmlstr += '<button class="btn close">关闭</button>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';

                return htmlstr;
            },
            resize:function(){
                var videoOne = $('.checkPending .videoList .liveOne').get();
                if(!videoOne[0]) return;

                for(var i in videoOne){
                    if((i % 3) == 1){
                        $(videoOne[i]).css('margin', '0px 30px 20px 30px');
                    }else{
                        $(videoOne[i]).css('margin', '0px 0px 20px 0px');
                    }
                }
            },
            editStatus:function(){
                var self = this;
                var lOne = $('.checkPending .videoList .liveOne');
                var btngroup = $('.delBtnGroup');

                lOne.find('.imagecontainer').append('<div class="bgModal" style="display: block; height: 100%; position: absolute; top: 0px; width: 100%; background-color: rgba(0, 0, 0, 0.6);"></div>');
                lOne.append('<div class="deleteOptModal"><div class="deleteOpt">删除</div></div>');
                btngroup.html('<button class="btn" id="finishCancelCheck">确认</button><button class="btn" id="cancelCancelCheck">取消</button>');

                $('.deleteOptModal .deleteOpt').bind('click', function(){
                    var o = $(this).parent();
                    if(o.hasClass('marked')){
                        o.removeClass('marked');
                    }else{
                        o.addClass('marked');
                    }
                });
                $('#cancelCancelCheck').bind('click', function(){
                    self.normalStatus();
                });
                $('#finishCancelCheck').bind('click', function(){
                    self.ensureCancelCheckVideoEvent();
                });

            },
            normalStatus:function(){
                var self = this;
                var lOne = $('.checkPending .videoList .liveOne');
                var btngroup = $('.delBtnGroup');

                lOne.find('.deleteOptModal').remove();
                lOne.find('.imagecontainer .bgModal').remove();

                btngroup.html('<button id="cancelCheck" class="btn right">取消审核</button>');

                $('#cancelCheck').bind('click', function(){
                    self.editStatus();
                });

                // 视频预览点击事件
                $('.checkPending .liveOne .previewOpt').bind('click', function(){
                    var videoid = $(this).parents('.liveOne').data('videoid');
                    VideoPlayBox.initVideo(videoid);
                });
            },
            ensureCancelCheckVideoEvent:function(){
                var self = this;
                NoticeBox.create(self.cancelCheckNoticeBoxHtml());
                $('#noticeBox .close').bind('click', NoticeBox.remove);
                $('#ensureCancelCheck').bind('click', cancelCheckVideo);

                function cancelCheckVideo(){
                    var cancelList = $('.checkPending .videoList .liveOne .deleteOptModal.marked');
                    if(!cancelList[0]){
                        return;
                    }
                    var cancelVideoID = [];
                    cancelList.each(function(){
                        var videoid = $(this).parent().data('videoid');
                        cancelVideoID.push(videoid)
                    });
                    if(!cancelVideoID[0]){
                        console.log('some err there no video id ');
                        return;
                    }

                    $.ajax({
                        url:$conf.api + 'deleteMyCheckVideo.php',
                        type:'post',
                        dataType:'json',
                        data:{
                            uid:getCookie('_uid'),
                            encpass:getCookie('_ecn'),
                            videoList:cancelVideoID.join(',')
                        },
                        success:function(){
                            if(d.isSuccess == 1){
                                cancelList.each(function(){
                                    $(this).parent().remove();
                                });
                                NoticeBox.remove();
                                self.initPageCross();
                                self.resize();
                            }
                        }
                    });
                }
            },
            init:function(){
                var self = this;

                self.currentPage = 1;
                self.initPageCross();
            },
            initPageCross:function(){
                var self = this;
                var size = 9,
                    pageCount;

                var successFn = function(videoList, videoCount){
                    self.initVideoList(videoList, videoCount);
                    if(videoCount > size){
                        pageCount = parseInt(videoCount / size);
                        if((videoCount % size) != 0){
                            pageCount += 1;
                        }
                        if(self.currentPage >= pageCount){
                            self.currentPage = pageCount;
                        }
                        $('.checkPending .pageIndex').remove();
                        $('.checkPending').append('<div class="pageIndex"></div>');
                        $('.checkPending .pageIndex').createPage({
                            pageCount:pageCount,
                            current:self.currentPage,
                            backFn:function(page){
                                self.pageCallBackFunction(page);
                            }
                        });
                    }else{
                        $('.checkPending .pageIndex').remove();
                    }
                };
                var option = {
                    url:$conf.api + 'getVideoList.php',
                    data:{
                        userID:getCookie('_uid'),
                        encpass:getCookie('_enc'),
                        videoStatus:1,
                        size:size,
                        page:self.currentPage
                    },
                    success:function(d){
                        if(d.videoList){
                            successFn(d.videoList, d.allCount);
                        }
                    }
                };
                option = $.extend(ajaxDefault, option);
                $.ajax(option);
            },
            pageCallBackFunction:function(page){
                var self = this;
                var size = 9;
                self.currentPage = page;
                var option = {
                    url:$conf.api + 'getVideoList.php',
                    data:{
                        userID:getCookie('_uid'),
                        encpass:getCookie('_enc'),
                        videoStatus:1,
                        size:size,
                        page:page
                    },
                    success:function(d){
                        if(d.videoList){
                            $('.checkPending .videoList .liveOne').remove();
                            self.initVideoList(d.videoList, d.allCount);
                        }
                    }
                };
                option = $.extend(ajaxDefault, option);
                $.ajax(option);
            },
            initVideoList:function(videolist, videoCount){
                var self = this;
                var videoListDiv = $('.checkPending .videoList');
                if(videoCount == 0){
                    videoListDiv.html(MyZone.pageHtml.noPublishedVideoNoticePageHtml());
                    $('delBtnGroup').children().remove();
                    return;
                }
                var checkVideoHtmlStr = '';
                for(var i in videolist){
                    checkVideoHtmlStr += self.checkVideoOneHtmlStr(videolist[i]);
                }
                videoListDiv.html(checkVideoHtmlStr);
                angleImage();
                videoListDiv.append('<div class="clear"></div>');
                self.resize();
                self.normalStatus();
            }
        },
        /**
         * [已经发布视频模块]
         * @type {Object}
         */
        alreadyPublishedModal:{
            currentPage:1,
            publishedVideoOneHtmlstr:function(d){
                var htmlstr = '';
                var imageClass = (d.angle == 0 && d.ispic == 1) ? $conf.angleImage : '';
                var url = $conf.domain + 'videoRoom.php?videoid=' + d.videoID;
                htmlstr += '<div class="liveOne" data-videoid="'+ d.videoID+'">';
                htmlstr += '<a href="'+ url+'">';
                htmlstr += '<div class="imagecontainer">';
                htmlstr += '<img '+imageClass+' src="'+ d.posterURL+'" alt=""/>';
                htmlstr += '<div class="live_anchor_name">';
                htmlstr += '<span>'+ d.publisherNickName+'</span>';
                htmlstr += '</div>';
                htmlstr += '<div class="playopt"></div>';
                htmlstr += '</div>';
                htmlstr += '<div class="liveinfo">';
                htmlstr += '<div class="videoName">'+ d.videoTitle+'</div><div class="clear"></div>';
                htmlstr += '<div class="liveDetail">';
                htmlstr += '<span class="anchor_icon viewerIcon"></span>';
                htmlstr += '<span>'+ d.totalViewCount+'</span>';
                htmlstr += '<span class="anchor_icon commentIcon"></span>';
                htmlstr += '<span>'+ d.commentCount+'</span>';
                htmlstr += '<span class="game_name">'+ d.gameName+'</span>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '</a>';
                htmlstr += '</div>';

                return htmlstr;

            },

            deleteVideoNoticeBoxHtml:function(){
                //删除录像提醒窗口
                var htmlstr = '';
                htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:320px;z-index: 1000;">'
                htmlstr += '<div class="theBox" style="padding: 26px 16px">';
                htmlstr += '<div class="box_head">';
                htmlstr += '<div class="closeBox">';
                htmlstr += '<span class="personal_icon close"></span>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="box_body">';
                htmlstr += '<div class="imgLogo"></div>';
                htmlstr += '<p>真的要删除这些收藏么？</p>';
                htmlstr += '</div>';
                htmlstr += '<div class="box_foot">';
                htmlstr += '<button id="ensureDelete" class="btn">确认删除</button>';
                htmlstr += '<button class="btn close">关闭</button>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';

                return htmlstr;
            },

            resize:function(){
                var videoOne = $('.publishedtab .videoList .liveOne').get();
                if(!videoOne[0]) return;
                for(var i in videoOne){
                    if((i % 3) == 1){
                        $(videoOne[i]).css('margin', '0px 30px 20px 30px');
                    }else{
                        $(videoOne[i]).css('margin', '0px 0px 20px 0px');
                    }
                }
            },

            editStatus:function(){
                var self = this;
                var lOne = $('.publishedtab .videoList .liveOne');
                var btngroup = $(".delBtnGroup");

                lOne.append('<div class="deleteOptModal"><div class="deleteOpt">删除</div></div>');
                lOne.find('.imagecontainer').append('<div class="bgModal" style="display: block; height: 100%; position: absolute; top: 0px; width: 100%; background-color: rgba(0, 0, 0, 0.6);"></div>');
                btngroup.html('<button class="btn" id="finishdel">确认</button><button class="btn" id="canceldel">取消</button>');

                $('.deleteOptModal .deleteOpt').bind('click', function(){
                    var o = $(this).parent();
                    if(o.hasClass('marked')){
                        o.removeClass('marked');
                    }else{
                        o.addClass('marked');
                    }
                });
                $('#canceldel').bind('click', function(){
                    self.normalStatus();
                });
                $('#finishdel').bind('click', function(){
                    self.ensureDeleteVideoEvent();
                });
            },

            normalStatus:function(){
                var self = this;
                var lOne = $('.publishedtab .videoList .liveOne');
                var btngroup = $(".delBtnGroup");

                lOne.find('.deleteOptModal').remove();
                lOne.find('.imagecontainer .bgModal').remove();
                $("#finishdel").remove();
                $('#canceldel').remove();

                btngroup.html('<button id="deleteVideo" class="btn right">删除录像</button>');
                $("#deleteVideo").bind('click', function(){
                    self.editStatus();
                });
            },

            ensureDeleteVideoEvent:function(){
                var self = this;
                var htmlstr = self.deleteVideoNoticeBoxHtml();
                NoticeBox.create(htmlstr);

                $('#noticeBox .close').bind('click', NoticeBox.remove);
                $("#ensureDelete").bind('click', deleteVideo);

                function deleteVideo(){
                    var deleteList = $('.publishedtab .videoList .liveOne .deleteOptModal.marked');
                    if(!deleteList[0])
                        return;

                    var deleteVideoID = [];
                    deleteList.each(function(){
                        var videoid = $(this).parent().attr('data-videoid');
                        deleteVideoID.push(videoid);
                    });

                    if(!deleteVideoID[0]){
                        console.log('some err there no video id ');
                        return;
                    }
                    console.log(deleteVideoID);
                    $.ajax({
                        url: $conf.api + 'deleteMyPublishVideo.php',
                        type:'post',
                        dataType:'json',
                        data:{
                            uid:getCookie('_uid'),
                            encpass:getCookie('_enc'),
                            videoList:deleteVideoID.join(',')
                        },
                        success:function(d){
                            if(d.isSuccess == 1){
                                deleteList.each(function(){
                                    $(this).parent().remove();
                                });
                                NoticeBox.remove();
                                self.initPageCross();
                                //如果页面刷新，活着调用绘制列表好的函数，那么些累代码无用
                                //注意考虑分业的情况
                                self.resize();
                            }
                        }
                    });
                }
            },
            init:function(){
                var self = this;

                self.currentPage = 1;
                self.initPageCross();
            },
            initPageCross:function(){
                var self = this;
                var size = 9,
                    pageCount;

                var successFn = function(videoList, videoCount){

                    self.initVideoList(videoList, videoCount);

                    if(videoCount > size){
                        pageCount = parseInt(videoCount / size);
                        if((videoCount % size) != 0){
                            pageCount += 1;
                        }
                        if(self.currentPage >= pageCount){
                            self.currentPage = pageCount;
                        }
                        $('.publishedtab .pageIndex').remove();
                        $('.publishedtab').append('<div class="pageIndex"></div>')
                        a('.publishedtab .pageIndex').createPage({
                            pageCount:pageCount,
                            current:self.currentPage,
                            backFn:function(page){
                                self.pageCallBackFunction(page);
                            }
                        });
                    }else{
                        $('.publishedtab .pageIndex').remove();
                    }
                };
                var option = {
                    url:$conf.api + 'getVideoList.php',
                    data:{
                        userID:getCookie('_uid'),
                        encpass:getCookie('_enc'),
                        videoStatus:0,
                        size:size,
                        page:self.currentPage
                    },
                    success:function(d){
                        if(d.videoList){
                            successFn(d.videoList, d.allCount);
                        }
                    }
                };
                option = $.extend(ajaxDefault,option);
                a.ajax(option);
            },
            initVideoList:function(videolist, videoCount){
                var self = this;
                var videoListDiv = $('.publishedtab .videoList');
                if(videoCount == 0){
                    videoListDiv.html(MyZone.pageHtml.noPublishedVideoNoticePageHtml());
                    $('.delBtnGroup').children().remove();
                    return;
                }
                var publishedVideoHtmlStr = '';
                for(var i in videolist){
                    publishedVideoHtmlStr += self.publishedVideoOneHtmlstr(videolist[i]);
                }
                videoListDiv.html(publishedVideoHtmlStr);
                //videoListDiv.find('div.clear').remove();
                videoListDiv.append('<div class="clear"></div>');
                self.resize();
                self.normalStatus();
            },
            pageCallBackFunction:function(page){
                var self = this;
                var size = 9;
                self.currentPage = page;
                var ajaxOption = {
                    url: $conf.api + 'getVideoList.php',
                    data:{
                        userID:getCookie('_uid'),
                        encpass:getCookie('_enc'),
                        videoStatus:0,
                        size:size,
                        page:page
                    },
                    success:function(d){
                        if(d.videoList){
                            $('.publishedtab .videoList .liveOne').remove();
                            self.initVideoList(d.videoList, d.allCount);
                        }
                    }
                }
                ajaxOption = $.extend(ajaxDefault, ajaxOption);
                a.ajax(ajaxOption);
            }
        },

        /**
         * 公告编辑模块
         */
        editBulletinModal:{
            editStatusHtmlstr:function(){
                var htmlStr = '<div class="noticeOnEdit mt-30"> <div class="edithead"></div> <div class="editbody"> <textarea id="Notice_wd" maxlength="300" warp="virtual" placeholder="请输入公告内容..."></textarea> <div class="wordnum">300</div> </div> <div class="editfoot"></div> <div class="editopt"></div> <button class="btn" id="submitNotice">提交待审核</button> <div class="clear"></div> </div>';

                return htmlStr;
            },
            normalStatusHtmlstr:function(text){
                var htmlStr = '<div class="noticeEdit mt-30"> <div class="edithead"></div> ' +
                '<div class="editbody">'+text+' </div>'+
                '<div class="editfoot"></div> <div class="editopt"></div><button class="btn" id="editNotice">编辑</button><div class="clear"></div></div>';

                return htmlStr;
            },
            normalStatus:function(){
                var self = this;

                var initNormalStatusEvent = function(){
                    $('#editNotice').bind('click', function(){
                        $('.editnotice .noticeEdit').remove();
                        self.editStatus();
                    });
                }
                var successFn = function(msg){
                    var message = msg || '#点击编辑按钮编辑公告';
                    $('.editnotice').html(self.normalStatusHtmlstr(msg));
                    initNormalStatusEvent();
                }
                var ajaxOption = {
                    url:$conf.api + 'shamApi_getLiveBulletin.php',
                    data:{
                        uid:getCookie('_uid'),
                        encpass:getCookie('_enc')
                    },
                    success:function(d){
                        if(d.message){
                            successFn(d.message);
                        }
                    }
                }
                ajaxOption = $.extend(ajaxDefault, ajaxOption);
                $.ajax(ajaxOption);
            },
            editStatus:function(){
                var self = this;
                console.log('run here');
                $('.editnotice').html(self.editStatusHtmlstr());
                $('#Notice_wd').bind('input propertychange',function(){
                    var len = $(this).val().length;
                    len = 300 - len;
                    $('.noticeOnEdit .editbody .wordnum').text(len);
                });

                $('#submitNotice').bind('click', function(){
                    var text_value = $.trim($('#Notice_wd').val());
                    if(!text_value)
                        return;

                    var successFn = function(){
                        $('.editnotice .noticeOnEdit').remove();
                        self.normalStatus();
                    }
                    var ajaxOption = {
                        url:$conf.api + 'shamApi_editMyBulletin.php',
                        data:{
                            uid:getCookie('_uid'),
                            encpass:getCookie('_enc'),
                            bulletin:text_value
                        },
                        success:function(d){
                            if(d.isSuccess == 1)
                                successFn();
                        }
                    };
                    ajaxOption = $.extend(ajaxDefault, ajaxOption);
                    a.ajax(ajaxOption);
                });
            },
            init:function(){
                this.normalStatus();
            }
        }
    };
}(jQuery))
