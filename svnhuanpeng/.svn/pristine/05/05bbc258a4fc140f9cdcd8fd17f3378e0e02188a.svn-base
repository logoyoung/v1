/**
 * Created by hantong on 16/5/31.
 */

function initPageCode(count, size, contEle,callBack, current){
    if(count > size){
        var pageCount = Math.ceil(count / size);


        if(current && current > pageCount){
            current = pageCount;
        }

        contEle.find('.pageIndex').remove();
        contEle.append('<div class="pageIndex"></div>');
        contEle.find('.pageIndex').createPage({
            current:current,
            pageCount:pageCount,
            backFn:function(page){
                callBack(page);
            }
        });
    }else{
        contEle.find('.pageIndex').remove();
    }
}

var Follow;
!function(a){
    var pageUser = {};
    pageUser.uid = getCookie('_uid');
    pageUser.enc = getCookie('_enc');

    Follow = {
        init:function(){
            this.followModal.init();
            this.collectModal.init();

            var self = this;
            var selectTab = $('.select_tab li');
            selectTab.bind('click', function(){
                selectTab.removeClass('selected');
                $(this).addClass('selected');
                var i = $(this).index();
                var tabCon = $('.follow_info .tabCon');
                tabCon.addClass('none');
                tabCon.eq(i).removeClass('none');

                if(i == 1 && tabCon.eq(i).hasClass('videoList')){
                    if($('.videoListContainer').find('.liveOne').length != 0){
                        $('#deleteCollect').removeClass('none');
                    }
                }else{
                    self.collectModal.normalStatus();
                    $('#deleteCollect').addClass('none');
                }
            });
        },
        followModal:{
            size:20,
            currentPage:1,
            init:function(){
                this.init2(this.currentPage);
            },
            init2:function(current){
                var self = this;
                if(current && current > 0){
                    self.currentPage = current;
                }
                var requestUrl = $conf.api + 'room/followList.php';
                var requestData = {
                    uid:pageUser.uid,
                    encpass:pageUser.enc,
                    size:self.size,
                    page:self.currentPage
                };
                ajaxRequest({url:requestUrl,data:requestData},function (d) {
                    if(d.list){
                        self.followList(d.list,d.total);
                        initPageCode(d.total,self.size,$('.follow_info .follow_list'), function (page) {
                           self.currentPage = page;
                            var requestData = {
                                uid:pageUser.uid,
                                encpass:pageUser.enc,
                                size:self.size,
                                page:self.currentPage
                            };
                            ajaxRequest({url:requestUrl,data:requestData},function(d){
                               self.followList(d.list, d.total);
                            });
                        }, self.currentPage);
                    }else{
                        var element = $('.follow_info .follow_list .followListContainer');
                        element.html('<div class="nodata" style="width: 260px;height: 258px; margin: 100px auto;"><img src="'+$conf.domain+'/static/img/logo/nodata-follow.png" alt=""></div>');
                        return;
                    }
                })

            },
            followList:function(list, count){
                var self = this;
                var element = $('.follow_info .follow_list .followListContainer');
                if(count <= 0){
                    element.html('<div class="nodata" style="width: 260px;height: 258px; margin: 100px auto;"><img src="'+$conf.domain+'/static/img/logo/nodata-follow.png" alt=""></div>');
                    return;
                }

                var html = '';
                for(var i in list){
                    html += self.liveOneHtml(list[i]);
                }
                element.html(html);
                element.append('<div class="clear"></div>');
                self.cancelFollow();
            },
            liveOneHtml:function(d){
                var url = $conf.domain  + d.roomID;
                var htmlstr = '';
                htmlstr += '<div class="liveOne" data-luid="' + d.uid + '">';
                htmlstr += '<a href="' + url + '">';
                htmlstr += '<div class="face"><img src="' + d.head + '"></div>';
                htmlstr += '<div class="info">';
                htmlstr += '<p>' + d.nick + '</p>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += d.isLiving == '1' ? '<span class="anchor_icon playingIcon">' : '<span class="anchor_icon unplayIcon">';
                htmlstr += '</span>';
                htmlstr += d.isLiving == '1' ? '<span class="playtime onplayed">直播中</span>' : '<span class="playtime">休息中</span>';
                htmlstr += '<div class="right" style="float:right">';
                htmlstr += '<span class="anchor_icon viewerIcon2"></span>';
                htmlstr += '<span class="viewercount">' + d.viewCount + '</span>';
                htmlstr += '</div>';
                htmlstr += '</div>';
                htmlstr += '<div class="opt">';
                htmlstr += '<a>取消关注</a>';
                htmlstr += '</div>';
                htmlstr += '</a>';
                htmlstr += '</div>';

                return htmlstr;
            },
            cancelFollow:function(){
                var self = this;
                var cancelBtn = a('.follow_list .liveOne .opt');
                cancelBtn.bind('click', function(){
                    var luid = $(this).parent().attr('data-luid');
                    var liveOne = $(this).parent();
                    if (!luid) return;
                    var requestUrl = $conf.api + 'room/followUserCancel.php';
                    var requestData = {
                        uid:pageUser.uid,
                        encpass:pageUser.enc,
                        luids:luid
                    };
                    ajaxRequest({url:requestUrl,data:requestData},function(d){
                        //tips
                        $('.followListContainer .liveOne').remove();
                        tips('取消关注成功');
                        //alert('取消关注成功');
                        self.init2(self.currentPage);
                    });
                });
            }
        },
        collectModal:{
            size:9,
            currentPage:1,
            init:function(){
                this.init2(this.currentPage);
                this.initDeleteCollectVideo();
            },
            init2:function(current){
                var self = this;
                if(current && current > 0){
                    self.currentPage = current;
                }
                var requestUrl = $conf.api + 'user/info/getCollectedVideoList.php';
                var requestData = {
                    uid:pageUser.uid,
                    encpass:pageUser.enc,
                    size:self.size,
                    page:self.currentPage
                };
                ajaxRequest({url:requestUrl, data:requestData},function(d){
                    if(d.total == 0){
                        $('#deleteCollect').addClass('none');
                    }
                    if(d.list){
                        self.collectList(d.list,d.total);
                        initPageCode(d.total,self.size,$('.videoList'),function(d){
                            self.currentPage = page;
                            var requestData = {
                                uid:pageUser.uid,
                                encpass:pageUser.enc,
                                size:self.size,
                                page:self.currentPage
                            };
                            ajaxRequest({url:requestUrl,data:requestData},function (d) {
                                self.collectList(d.list, d.total);
                            })
                        },function (d) {

                        })
                    }
                },function (d) {

                });
            },
            collectList:function(list, count){
                var self = this;
                var element = $('.videoList .videoListContainer');
                if(count <= 0){
                    element.html('<div class="nodata" style="width: 260px;height: 258px; margin: 100px auto;"><img src="'+$conf.domain+'/static/img/logo/nodata-collect.png" alt=""></div>');
                    self.resize();
                    return;
                }

                var html = '';
                for(var i in list){
                    html += self.collectOneHtml(list[i]);
                }
                element.html(html);
                element.append('<div class="clear"></div>');

                self.resize();
            },
            collectOneHtml:function(d){
                var htmlstr;
                var url = $conf.domain + 'videoRoom.php?videoid=' + d.videoID;
                htmlstr = '<div class="liveOne" data-videoid="' + d.videoID + '"><a href="' + url + '"><div class="imagecontainer"><img src="' + d.poster + '" alt=""/><div class="live_anchor_name"><span>' + d.nick + '</span></div><div class="playopt"></div></div><div class="liveInfo"><div class="videoName">' + d.title + '</div><div class="clear"></div><div class="liveDetail"><span class="anchor_icon viewerIcon"></span><span>' + d.viewCount + '</span><span class="anchor_icon commentIcon"></span><span>' + d.commentCount + '</span><span class="game_name">' + d.gameName + '</span></div></div></a></div>';
                return htmlstr;
            },
            initDeleteCollectVideo:function(){
                var self = this;
                var deleteBtn = $('#deleteCollect');
                deleteBtn.bind('click', function(){
                    self.editStatus();
                });

                var ensureDeleteBtn = $('#deleteFinish');
                ensureDeleteBtn.bind('click', ensureDeleteCollectVideoEvent);

                var cancelDeleteBtn = $('#deleteCancel');
                cancelDeleteBtn.bind('click', function () {
                    self.normalStatus();
                });

                function ensureDeleteCollectVideoEvent(){
                    var ensureDeletediaLog = dialog({
                        title:'提示',
                        skin:'err-notice person-notice deleteCollect-notice',
                        content:'确定要删除这些收藏视频么？',
                        cancelValue:'取消',
                        cancel:function(){
                            self.normalStatus();
                        },
                        okValue:'确定',
                        ok: function () {
                            requestDeleteCollectVideo();
                        }
                    });

                    ensureDeletediaLog.showModal();
                    $('.deleteCollect-notice').find('.ui-dialog-close').text('');

                    function requestDeleteCollectVideo(){
                        var deleteList = $('.liveOne .deleteOptModal.marked');
                        if(!deleteList[0])
                            return;
                        var deleteVideoIDList = [];
                        deleteList.each(function () {
                            var videoID = $(this).parent().attr('data-videoid');
                            deleteVideoIDList.push(videoID);
                        });
                        var requestUrl = $conf.api + 'video/cancelCollectVideo.php';
                        var requestData = {
                            uid:pageUser.uid,
                            encpass:pageUser.enc,
                            videoIDList:deleteVideoIDList.join(',')
                        };
                        ajaxRequest({url:requestUrl,data:requestData},function (d) {
                            $('.videoList .liveOne').remove();
                            self.init2(self.currentPage);
                            self.normalStatus();
                            self.resize();
                            ensureDeletediaLog.close().remove();
                        });
                    }
                }
            },
            editStatus:function(){
                var self = this;
                var videoOne = $('.videoList .liveOne');
                var deleteBtn = $('#deleteCollect');
                var btnGroup = $('.follow_opt .deleteBtngroup');

                videoOne.append('<div class="deleteOptModal"><div class="deleteOpt">选择</div></div>');
                videoOne.find('.playopt').addClass('none');
                deleteBtn.addClass('none');
                btnGroup.removeClass('none');

                var deleteVideoOptBtn = videoOne.find('.deleteOpt');
                deleteVideoOptBtn.bind('click', function(){
                    var element = $(this).parent();
                    if(element.hasClass('marked')){
                        element.removeClass('marked');
                    }else{
                        element.addClass('marked');
                    }
                });
                /*deleteVideoOptBtn.hover(function () {
                    $(this).text('选择');
                },function () {
                    $(this).text('选择');
                });*/

            },
            normalStatus: function () {
                var videoOne = $('.videoList .liveOne');
                var deleteBtn = $('#deleteCollect');
                var btnGroup = $('.follow_opt .deleteBtngroup');

                videoOne.find('.deleteOptModal').remove();
                videoOne.find('.playopt').removeClass('none');
                deleteBtn.removeClass('none');
                btnGroup.addClass('none');
            },
            resize:function(){
                var liveOne = $('.videoList .liveOne').get();
                for (var i in liveOne) {
                    if ((i % 3) == 1)
                        $(liveOne[i]).css('margin', '0px 30px 20px 30px');
                    else
                        $(liveOne[i]).css('margin', '0px 0px 20px 0px');
                }
            }
        }
    };
}(jQuery)