/**
 * Created by hantong on 16/5/12.
 */

var pageUser = {};
!function () {
    pageUser.uid = getCookie('_uid') || 0;
    pageUser.enc = getCookie('_enc') || '';
    //pageUser.videoLimitCount = getCookie('_videoLimitCount') || 5;
}();


var VideoPlayBox;
(function () {
    var $conf = conf.getConf();
    var ajaxDefault = {
        url: '',
        type: 'post',
        dataType: 'json',
        data: {},
        success: function () {
        }
    };

    VideoPlayBox = {
        boxHtml: function () {
            var htmlstr = '<div class="videoPlay"><div id="videoPlayer"></div><div class="closeVideo personal_icon close2"></div></div>';

            //htmlstr += '<div class="videoPlay">';
            //htmlstr += '    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"id="videoPlayer" width="100%" height="100%"codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab">';
            //htmlstr += '        <param name="movie" value="../../static/flash/videoPlayer.swf" />';
            //htmlstr += '        <param name="quality" value="high" />';
            //htmlstr += '        <param name="bgcolor" value="#869ca7" />'
            //htmlstr += '        <param name="allowScriptAccess" value="always" />';
            //htmlstr += '        <embed src="../../static/flash/videoPlayer.swf"  bgcolor="#869ca7" width="100%" height="100%" name="videoPlayer" align="middle"play="true" loop="false" quality="high" allowScriptAccess="sameDomain"type="application/x-shockwave-flash"pluginspage="http://www.macromedia.com/go/getflashplayer">';
            //htmlstr += '   </object>';
            //htmlstr += '   <div class="closeVideo personal_icon close2"></div>';
            //htmlstr += '</div>';

            return htmlstr;
        },
        create: function () {
            var self = this;
            if ($('#VideoPlayBox')) {
                self.remove();
            }
            $('<div/>', {
                id: 'videoPlayBox',
                'class': 'videoPlayBox',
                style: 'position:fixed; left:50%;top:50%;z-index:1000;',
                html: self.boxHtml()
            }).appendTo(document.body);
            createPlayer();
            $('#videoPlayBox .videoPlay .closeVideo').bind('click',function(){
                self.hide();
                runSwfFunction('videoPlayer','inputURL','');
            });

            function createPlayer(){
                var file = $conf.domain + 'static/flash/videoPlayer.swf',
                    id = 'videoPlayer',
                    version = '9.0.0',
                    install = 'expressInstall.swf';
                var flashvar = {
                    'loadingURL':$conf.domain + 'static/flash/loading.swf',
                    'UIButtonURL':$conf.domain + 'static/flash/UIButton.swf',
                    'giftURL':$conf.domain + 'static/flash/gift.swf'
                };
                var param = {
                    quality:'high',
                    bgcolor:'#869ca',
                    allowScriptAccess:'always',
                    align:'middle',
                    allowFullScreen: 'true'
                }
                var attribute = {
                    allowScriptAccess:'always',
                    allowFullScreen: 'true',
                    name:id,
                    bgcolor:'#869ca',
                    align:'middle'
                }
                swfobject.embedSWF(file, id, '100%', '100%', version, install, flashvar, param, attribute);
            }
        },
        remove: function () {
            if (!$('#videoPlayBox')) {
                return;
            }
            Mask.remove();
            $('#videoPlayBox').remove();
        },
        initVideo: function (videoid) {
            var self = this;

            initVideoPlayer();
            self.show();

            function initVideoPlayer(){
                var requestUrl = $conf.api + 'video/getVideoPlayUrl.php';
                var requestData ={
                    videoID: videoid,
                    uid: pageUser.uid,
                    encpass: pageUser.enc
                }
                ajaxRequest({url:requestUrl,data:requestData},function (d) {
                    if(d.videoUrl){
                        runSwfFunction('videoPlayer','inputURL',d.videoUrl);
                        var angle = 1;
                        if(d.orientation == 1 || d.orientation == 4){
                            angle = 0;
                        }
                        runSwfFunction('videoPlayer','angle',angle);
                    }
                },function(d){

                });
            }
        },
        show: function () {
            Mask.creates();
            Mask.box.css('background-color', 'rgba(0,0,0,0.8)');
            $('#videoPlayBox').show();
        },
        hide: function () {
            Mask.remove();
            $('#videoPlayBox').hide();
        }

    }
}());


var MyZone;
!function (a) {
    var $conf = conf.getConf();
    var ajaxDefault = {
        url: '',
        type: 'post',
        dataType: 'json',
        data: {},
        success: function () {
        }
    }

    var dialogConf = {
        title: '提示',
        content: '',
        cancelValue: '取消',
        cancel: function () {
        },
        okValue: '确定',
        ok: function () {
        }
    }
    //var videoAllCount = getCookie('_videoLimitCount') || 5;


    MyZone = {
        pageHtml: {
            noPublishedVideoNoticePageHtml: function () {
                var htmlstr = '<div class="noVideo mt-60"><div class="logo"><img src="../../static/img/noData/lr-novideo.png" alt=""/></div><div class="noticeword left "><p>你还没有发布录像哦~</p></div><div class="clear"></div></div>';
                return htmlstr;
            }
        },
        init: function () {
            var self = this;

            //$('.videoLimitCount').text(pageUser.videoLimitCount);
            self.willPublishModal.init();
            $('.select_tab li').bind('click', function () {
                $('.select_tab li').removeClass('selected');
                $('.tab_con').addClass('none');

                var i = $(this).index();

                $(this).addClass('selected');
                $('.tab_con').eq(i).removeClass('none');

                if (i == 0) {
                    $('.zoneopt .delBtnGroup').remove();
                    self.willPublishModal.init();

                } else if (i == 1) {
                    $('.zoneopt .delBtnGroup').remove();
                    $('.zoneopt').append('<div class="delBtnGroup right"></div>');
                    self.alreadyPublishedModal.init();
                } else {
                    $('.zoneopt .delBtnGroup').remove();
                    self.editBulletinModal.init();
                }
            });

            VideoPlayBox.create();
        },
        /**
         * 待发布录像模块
         */
        willPublishModal: {
            pVideoHtmlstr: function (d) {

                var htmlstr = '';
                var imageClass = (d.angle == 0 && d.ispic == 1) ? $conf.angleImage : '';
                htmlstr += '<div class="pvideoOne" data-videoid="' + d.videoID + '">';
                htmlstr += '<div class="liveOne">';
                htmlstr += '<div class="imagecontainer">';
                htmlstr += '<img class="'+imageClass+'" src="' + d.poster + '">';
                htmlstr += '<div class="previewOptModal">';
                htmlstr += '<div class="previewOpt">预览</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="pvideoinfo">';
                htmlstr += '<div class="pgroup">';
                htmlstr += '<label class="label">录像标题:</label>';
                htmlstr += '<div class="pinfo title">' + d.title + '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="pgroup">';
                htmlstr += '<label class="label">录像时间:</label>';
                htmlstr += '<div class="pinfo">' + js_date_format('yyyy-MM-dd HH:mm:ss', d.videoUploadDate) + '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="pgroup">';
                htmlstr += '<label class="label">录像时长:</label>';
                htmlstr += '<div class="pinfo">' + d.videoTimeLength + '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="pgroup">';
                htmlstr += '<label class="label">录像内容:</label>';
                htmlstr += '<div class="pinfo ">' + d.gameName + '</div>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                if (d.videoStatus == $conf.video.CHECK) {
                    htmlstr += '<button class="btn published">审核中</button>';
                }
                else {
                    htmlstr += '<button class="btn publish">发布</button>';
                    htmlstr += '<div class="countDownTag">保存时间:还剩<span class="days"> ' + d.timeOut + ' </span>天</div>';
                }

                htmlstr += '<div class="clear"></div>';
                htmlstr += '</div>';

                return htmlstr;
            },

            initVideoList: function (videoList, videoCount) {
                var self = this;
                var videoListDiv = $('.unpublishtab .videoList');
                if (videoCount == 0) {
                    videoListDiv.html(MyZone.pageHtml.noPublishedVideoNoticePageHtml());
                    return;
                }

                var thePVideoHtmlStr = '';
                for (var i in videoList) {
                    thePVideoHtmlStr += self.pVideoHtmlstr(videoList[i]);
                }
                videoListDiv.html(thePVideoHtmlStr);
                angleImage();
                //发布录像事件定义
                self.publishVideo();

                //视频预览点击事件
                $('.unpublishtab .liveOne .previewOpt').bind('click', function () {
                    var videoid = $(this).parents('.pvideoOne').data('videoid') || 0;
                    VideoPlayBox.initVideo(videoid);
                });
            },

            pageCallBackFunction: function (page, postData) {
                var self = this;
                postData.page = page;
                var requestUrl = $conf.api + 'video/myVideo.php';
                var requestData = postData;

                ajaxRequest({url:requestUrl,data:requestData},function (d) {
                    self.initVideoList(d.list, d.total);
                })

            },

            publishVideo: function () {

                var publishBtn = $('.unpublishtab .videoList .pvideoOne .btn.publish');
                if (!publishBtn.get()[0]) {
                    //console.log('没有要发布的录像');
                    return;

                }

                var publish = function () {
                    var videoID = a(this).parent().attr('data-videoid');
                    if (!parseInt(videoID)) {
                        console.log('无效的视频ID');
                        return;
                    }

                    var element = this;

                    function successFn() {
                        //var htmlstr = self.publishVideoSuccessNoticeBoxHtml();
                        //NoticeBox.create(htmlstr);
                        //$("#noticeBox .close").bind('click', NoticeBox.remove);
                        $(element).unbind('click', publish);
                        $(element).removeClass('publish').addClass('published').text('审核中');

                        var dconf = dialogConf;
                        var diaLogs = dialog(a.extend(dconf, {
                            skin: 'err-notice',
                            content: '<p>您的录像发布成功</p><p>审核通过就可以观看了哦~</p>',
                            cancelValue: '',
                            cancel: '',
                            okValue: '确定',
                            ok: function () {
                                return true;
                            }
                        }));
                        diaLogs.showModal();
                        $('.err-notice').css('margin-top', '-100px').find('.ui-dialog-close').text('');

                    }
                    var requestUrl = $conf.api + 'video/publishVideo.php';
                    var requestData = {
                        uid: getCookie('_uid'),
                        encpass: getCookie('_enc'),
                        videoID: videoID,
                        type: 1
                    };

                    ajaxRequest({url:requestUrl,data:requestData},function (d) {
                        successFn();
                    },function (d) {
                        tips(d.desc);
                    });
                }

                publishBtn.bind('click', publish);
            },

            initVideo: function () {
                var self = this;
                var postData,
                    ajaxOption;
                var size = 4;


                postData = {
                    uid: getCookie('_uid'),
                    encpass: getCookie('_enc'),
                    size: size,
                    page: 1,
                    order: 0,
                    gameid: 0,
                    gametid: 0
                };

                var successFn = function (videoList, videoCount) {
                    self.initVideoList(videoList, videoCount);

                    if (videoCount > size) {
                        var pageCount = parseInt(videoCount / size);
                        if (videoCount % size != 0) {
                            pageCount += 1;
                        }
                        //重新绘制的时候先清除原先的element
                        $('.unpublishtab .pageIndex').remove();
                        $('.unpublishtab').append('<div class="pageIndex"></div>');
                        $('.unpublishtab .pageIndex').createPage({
                            pageCount: pageCount,
                            backFn: function (page) {
                                self.pageCallBackFunction(page, postData);
                            }
                        });
                    } else {
                        //如果pageCount为一页，那么则清除翻页按钮
                        $('.unpublishtab .pageIndex').remove();
                    }

                };
                var requestUrl = $conf.api + 'video/myVideo.php';
                var requestData = postData;
                ajaxRequest({url:requestUrl,data:requestData},function(d){
                    if(d.list){
                        $('.unpublishtab .videoList .pvideoOne').remove();
                        successFn(d.list, d.total);
                    }
                });
            },
            init: function () {
                var requestUrl = $conf.api + 'video/publishInfo.php';
                var requestData = {
                    uid:pageUser.uid,
                    encpass:pageUser.enc
                };
                ajaxRequest({url:requestUrl,data:requestData},function (d) {
                    $('.currVideoCount').text(d.publish);
                    $('.videoLimitCount').text(d.limit);
                });
                this.initVideo();
            }
        },

        /**
         * [已经发布视频模块]
         * @type {Object}
         */
        alreadyPublishedModal: {
            currentPage: 1,
            publishedVideoOneHtmlstr: function (d) {
                var htmlstr = '';
                var imageClass = (d.orientation == 0 && d.ispic == 1) ? $conf.angleImage : '';
                var url = $conf.domain + 'videoRoom.php?videoid=' + d.videoID;
                htmlstr += '<div class="liveOne" data-videoid="' + d.videoID + '">';
                htmlstr += '<a href="' + url + '">';
                htmlstr += '<div class="imagecontainer">';
                htmlstr += '<img class="'+imageClass+'" src="' + d.poster + '" alt=""/>';
                //htmlstr += '<div class="live_anchor_name">';
                //htmlstr += '<span>' + d.publisherNickName + '</span>';
                //htmlstr += '</div>';
                htmlstr += '<div class="playopt"></div>';
                htmlstr += '</div>';
                htmlstr += '<div class="liveInfo">';
                htmlstr += '<div class="videoName">' + d.title + '</div><div class="clear"></div>';
                htmlstr += '<div class="liveDetail">';
                htmlstr += '<span class="anchor_icon viewerIcon"></span>';
                htmlstr += '<span>' + d.viewCount + '</span>';
                htmlstr += '<span class="anchor_icon commentIcon"></span>';
                htmlstr += '<span>' + d.commentCount + '</span>';
                htmlstr += '<span class="game_name">' + d.gameName + '</span>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '</a>';
                htmlstr += '</div>';

                return htmlstr;

            },

            deleteVideoNoticeBoxHtml: function () {
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

            resize: function () {
                var videoOne = $('.publishedtab .videoList .liveOne').get();
                if (!videoOne[0]) return;
                for (var i in videoOne) {
                    if ((i % 3) == 1) {
                        $(videoOne[i]).css('margin', '0px 30px 20px 30px');
                    } else {
                        $(videoOne[i]).css('margin', '0px 0px 20px 0px');
                    }
                }
            },

            editStatus: function () {
                var self = this;
                var lOne = $('.publishedtab .videoList .liveOne');
                var btngroup = $(".delBtnGroup");

                lOne.append('<div class="deleteOptModal"><div class="deleteOpt">选择</div></div>');
                lOne.find('.imagecontainer').append('<div class="bgModal" style="display: block; height: 100%; position: absolute; top: 0px; width: 100%; background-color: rgba(0, 0, 0, 0.6);"></div>');
                lOne.find('.playopt').addClass('none');
                btngroup.html('<button class="btn" id="finishdel">删除</button><button class="btn" id="canceldel">取消</button>');

                $('.deleteOptModal .deleteOpt').bind('click', function () {
                    var o = $(this).parent();
                    if (o.hasClass('marked')) {
                        o.removeClass('marked');
                    } else {
                        o.addClass('marked');
                    }
                });
                $('#canceldel').bind('click', function () {
                    self.normalStatus();
                });
                $('#finishdel').bind('click', function () {
                    self.ensureDeleteVideoEvent();
                });
                /*$('.deleteOpt').hover(function () {
                    $(this).text('选择');
                },function () {
                    $(this).text('选择');
                })*/
            },

            normalStatus: function () {
                var self = this;
                var lOne = $('.publishedtab .videoList .liveOne');
                var btngroup = $(".delBtnGroup");

                lOne.find('.deleteOptModal').remove();
                lOne.find('.imagecontainer .bgModal').remove();
                lOne.find('.playopt').removeClass('none');
                $("#finishdel").remove();
                $('#canceldel').remove();

                btngroup.html('<button id="deleteVideo" class="btn right">编辑</button>');
                $("#deleteVideo").bind('click', function () {
                    self.editStatus();
                });
            },

            ensureDeleteVideoEvent: function () {
                var self = this;
                var diaLogs = dialog({
                    skin: 'err-notice person-notice',
                    title: '提示',
                    content: '<p>确定删除这些录像么？</p><p class="err-word-notice">录像一经删除不可找回</p>',
                    cancelValue: '取消',
                    cancel: function () {
                    },
                    okValue: '确定',
                    ok: deleteVideo
                });
                diaLogs.showModal();
                $('.ui-dialog-close').text('');

                function deleteVideo() {
                    var deleteList = $('.publishedtab .videoList .liveOne .deleteOptModal.marked');
                    if (!deleteList[0])
                        return;

                    var deleteVideoID = [];
                    deleteList.each(function () {
                        var videoid = $(this).parent().attr('data-videoid');
                        deleteVideoID.push(videoid);
                    });

                    if (!deleteVideoID[0]) {
                        console.log('some err there no video id ');
                        return;
                    }
                    console.log(deleteVideoID);
                    var requestUrl = $conf.api + 'video/deleteMyPublishVideo.php';
                    var requestData = {
                        uid: getCookie('_uid'),
                        encpass: getCookie('_enc'),
                        videoList: deleteVideoID.join(',')
                    };
                    ajaxRequest({url:requestUrl,data:requestData},function(d){
                        deleteList.each(function () {
                            $(this).parent().remove();
                        })
                        NoticeBox.remove();
                        self.initPageCross();
                        self.resize();
                    },function(d){

                    });
                }
            },
            init: function () {
                var self = this;
                self.currentPage = 1;
                var requestUrl = $conf.api + 'video/publishInfo.php';
                var requestData = {
                    uid:pageUser.uid,
                    encpass:pageUser.enc
                };
                ajaxRequest({url:requestUrl,data:requestData},function (d) {
                    $('.currVideoCount').text(d.publish);
                    $('.videoLimitCount').text(d.limit);
                });
                self.initPageCross();
            },
            initPageCross: function () {
                var self = this;
                var size = 9,
                    pageCount;

                var requestUrl = $conf.api + 'video/getVideoList.php';
                var requestData = {
                    luid:getCookie('_uid'),
                    encpass:getCookie('_enc'),
                    size:size,
                    page:self.currentPage
                };

                ajaxRequest({url:requestUrl,data:requestData},function(d){
                    successFn(d.list, d.total);
                },function(d){

                });

                var successFn = function (videoList, videoCount) {
                    //设置当前视频数量
                    self.initVideoList(videoList, videoCount);

                    if (videoCount > size) {
                        pageCount = parseInt(videoCount / size);
                        if ((videoCount % size) != 0) {
                            pageCount += 1;
                        }
                        if (self.currentPage >= pageCount) {
                            self.currentPage = pageCount;
                        }
                        $('.publishedtab .pageIndex').remove();
                        $('.publishedtab').append('<div class="pageIndex"></div>')
                        a('.publishedtab .pageIndex').createPage({
                            pageCount: pageCount,
                            current: self.currentPage,
                            backFn: function (page) {
                                self.pageCallBackFunction(page);
                            }
                        });
                    } else {
                        $('.publishedtab .pageIndex').remove();
                    }
                };
            },
            initVideoList: function (videolist, videoCount) {
                var self = this;
                var videoListDiv = $('.publishedtab .videoList');
                if (videoCount == 0) {
                    videoListDiv.html(MyZone.pageHtml.noPublishedVideoNoticePageHtml());
                    $('.delBtnGroup').children().remove();
                    return;
                }
                var publishedVideoHtmlStr = '';
                for (var i in videolist) {
                    publishedVideoHtmlStr += self.publishedVideoOneHtmlstr(videolist[i]);
                }
                videoListDiv.html(publishedVideoHtmlStr);
                angleImage();

                videoListDiv.append('<div class="clear"></div>');
                self.resize();
                self.normalStatus();
            },
            pageCallBackFunction: function (page) {
                var self = this;
                var size = 9;
                self.currentPage = page;
                var requestUrl = $conf.api + 'video/getVideoList.php';
                var requestData = {
                    luid:getCookie('_uid'),
                    encpass:getCookie('_enc'),
                    size:size,
                    page:page
                };
                ajaxRequest({url:requestUrl,data:requestData},function (d) {
                    $('.publishedtab .videoList .liveOne').remove();
                    self.initVideoList(d.list,d.total);
                });
            }
        },

        /**
         * 公告编辑模块
         */
        editBulletinModal: {
            message:'',
            editStatusHtmlstr: function () {
                var htmlStr = '';
                htmlStr = '<div class="noticeOnEdit mt-30"> <div class="edithead"></div> <div class="editbody"> <textarea id="Notice_wd" maxlength="300" warp="virtual" placeholder="请输入公告内容..."></textarea> <div class="wordnum">300</div> </div> <div class="editfoot"></div> <div class="editopt"></div> <button class="btn" id="submitNotice">提交待审核</button> <div class="clear"></div> </div>';

                return htmlStr;
            },
            normalStatusHtmlstr: function (text) {
                var htmlStr = '';
                htmlStr = '<div class="noticeEdit mt-30"> <div class="edithead" style="color: red;margin-bottom: 14px;"></div> ' +
                '<div class="editbody">' + text + ' </div>' +
                '<div class="editfoot"></div> <div class="editopt"></div><button class="btn" id="editNotice">编辑</button><div class="clear"></div></div>';

                return htmlStr;
            },
            normalStatus: function () {
                var self = this;

                var initNormalStatusEvent = function () {
                    $('#editNotice').bind('click', function () {
                        $('.editnotice .noticeEdit').remove();
                        self.editStatus();
                        // $('#Notice_wd').val('');
                    });
                }
                var successFn = function (msg,status) {
                    self.message = msg || '';
                    $('.editnotice').html(self.normalStatusHtmlstr(self.message));
                    if(status == 0){
                        $('.editnotice .edithead').html('您提交的公告正在审核中...');
                    }else if(status == 2){
                        $('.editnotice .edithead').html('您提交的公告未通过审核，请重新编写');
                    }
                    initNormalStatusEvent();
                }

                var requestUrl = $conf.api + 'user/info/shamApi_getLiveBulletin.php';
                var requestData = {
                    uid:getCookie('_uid'),
                    encpass:getCookie('_enc'),
                    type:1
                };
                ajaxRequest({url:requestUrl,data:requestData},function(d){

                    successFn(d.message,d.status);
                },function(){});

            },
            editStatus: function () {
                var self = this;
                console.log('run here');
                $('.editnotice').html(self.editStatusHtmlstr());

                $('#Notice_wd').bind('input propertychange', function () {
                    var len = $(this).val().length;
                    len = 300 - len;
                    $('.noticeOnEdit .editbody .wordnum').text(len);
                });
                $("#Notice_wd").val(self.message);
                $('.noticeOnEdit .editbody .wordnum').text(300 - self.message.length);
                $('#submitNotice').bind('click', function () {
                    var text_value = $.trim(xssReplce($('#Notice_wd').val()));
                    if (!text_value)
                        return;

                    var requestUrl = $conf.api + 'user/info/shamApi_editMyBulletin.php';
                    var requestData = {
                        uid: getCookie('_uid'),
                        encpass: getCookie('_enc'),
                        bulletin: text_value
                    };
                    ajaxRequest({url:requestUrl,data:requestData},function (d) {
                        successFn();
                    })
                    var successFn = function () {
                        $('.editnotice .noticeOnEdit').remove();
                        self.normalStatus();
                    }
                });
            },
            init: function () {
                this.normalStatus();
            }
        }
    };
}(jQuery);