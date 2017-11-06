/** 
 * by fanguang 2017/07/17
 * @file 经纪公司签约h5
 * @entry  managerApply.init
 */



$(function() {

 var pageSize = '10';
 var pageSizeNum = 10;



  var managerApply = {
    loadMore_left: true,
    loadMore_right: false,
    videoPageAll_left: 0,
    videoPageAll_right: 0,
    loadMore_left_page: 1,
    loadMore_right_page: 1,

    init: function() {
      this.statusData = {
        aid: sessionStorage.getItem('aid'),
        cname: sessionStorage.getItem('cname'),
        applyStatus: sessionStorage.getItem('applyStatus'),
        reason: sessionStorage.getItem('reason'),
        uid: sessionStorage.getItem('uid'),
        encpass: sessionStorage.getItem('encpass'),
        cid: sessionStorage.getItem('cid'),
        poster: ''
      };
      this._initStatus(this.statusData.applyStatus);
      
      // console.log(sessionStorage);
    },
    // 状态分发 不同状态显示不同页面
    _initStatus: function(status) {
      var self = this;

      switch(status) {
      	case '-1':
      		self.applyTo();
      		break;
      	case '0':
      		self.applying(status);
      		break;
      	case '1':
      		self.applying(status);
      		break;
      	case '2':
      		self.applyFail();
      		break;
      	case '3':
      		self.applySuccess();
      		break;
      	default:
      		return null;
      		break;
      }
    },
    // 申请签约经纪公司成功
    applySuccess: function() {
      var self = this;
      $('#success').show();
      $('#managerName').text(self.statusData.cname);
    },
    // 申请签约经纪公司失败
    applyFail: function() {
      var self = this;
      $('#fail').show();
      $('#failReason').text(self.statusData.reason);

      $('#applyAgainBtn').off().on('click',function(e) {
      	e.preventDefault();
      	$('#fail').hide();
      	sessionStorage.setItem('applyStatus','-1');
      	self.applyTo();
      })
    },
    // 申请中..
    applying: function(type) {
      var self = this;
      $('#waiting').show();
      $('#applyId').text(self.statusData.cid);
      if(type === '1') {
      	$('#applyCancelBtn').hide();
        $('#waiting').find('.p2').text('经纪公司审核通过，待平台审核');
      }

      $('#applyCancelBtn').off().on('click',function(e) {
      	e.preventDefault();

      	$('.layer').show(300);
      })
      $('#canselSubmit').off().on('click',function(e) {
      	e.preventDefault();
      	var requestUrl = $conf.api + 'anchor/anchorCancelApply.php';
      	var requestData = {
      	  uid: self.statusData.uid,
      	  encpass: self.statusData.encpass,
      	  aid: self.statusData.aid
      	};
      	ajaxRequest({url: requestUrl,data: requestData},function(data) {
      		$('.layer').hide();
      	    window.location.href = $conf.domain + 'mobile/beAnchor' + new Date().getTime();
      	},function(failData) {
      		$('.layer').hide();
      		layer.msg(failData.content.desc);
      	})
      });
      $('#closeLayer').off().on('click',function(e) {
      	e.preventDefault();
      	$('.layer').hide();
      })
    },
    // 提交经纪公司签约申请
    applyTo: function() {
      var self = this;
      $('#apply').show().siblings().hide();

      var videoTitle = sessionStorage.getItem('videoTitle') || '未选择';
      var videoId = sessionStorage.getItem('videoId');
      // var videoPoster = sessionStorage.getItem('videoPoster') || '';
      var videoPoster = self.statusData.poster;
      $('#selectVideo').text(videoTitle);
      if(videoPoster) {
        $('#selectPoster').show().attr('src',videoPoster);
        $('#selectVideo').addClass('fc-black');
      }else {
        $('#selectPoster').hide();
        $('#selectVideo').removeClass('fc-black');
      }

      $('#applyToBtn').off().on('click',function(e) {
      	e.preventDefault();
      	// var regExp = /^[0-9]*$/;
      	var cid = $('#managerId').val();
        
      	if(!cid) {
          layer.msg('请填写经纪公司ID');
          return null;
      	}else if(videoTitle === '未选择' || '') {
      	  layer.msg('请选择签约审核视频');
      	  return null;
      	}
      	var requestUrl = $conf.api + 'anchor/anchorApplyCompany.php';
      	var requestData = {
      	  uid: self.statusData.uid,
      	  encpass: self.statusData.encpass,
      	  cid: cid,
      	  videoId: videoId
      	};
      	ajaxRequest({url: requestUrl,data: requestData},function(data) {
      		// console.log(data);
      	  self.statusData.cid = cid;
      	  self.statusData.aid = data.aid;
      	  sessionStorage.setItem('applyStatus','0');
      	  $('#apply').hide();
      	  self.applying('0');
      	},function(failData){
      	  layer.msg(failData.desc);
      	})
      });
      $('#goManagerVideo').off().on('click',function(e) {
        self.applyVideo();
      });
    },

    // 选择视屏页面
    applyVideo: function() {
      this._getVideo();                  
      this._selectVideo();
      this._tabVideo();
      this._loadMore();
      $('#video').show().siblings().hide();
    },
    // 获取视屏列表
    _getVideo: function() {
     var self = this;
     var requestUrl = $conf.api + 'video/myVideo.php';
     var requestData_left = {
       uid: sessionStorage.getItem('uid'),
       encpass: sessionStorage.getItem('encpass'),
       size: pageSize,
       type: '0'
     };
     var requestData_right = {
       uid: sessionStorage.getItem('uid'),
       encpass: sessionStorage.getItem('encpass'),
       size: pageSize,
       type: '2'
     }
     $.ajax({
       type: 'post',
       url: requestUrl,
       data: requestData_left,
       async: false,
       success: function(res) {
          // 待发布总页数
         var data = JSON.parse(res);
         self.videoPageAll_left = Math.ceil(parseInt(data.content.total,10)/pageSizeNum);
         self.loadMore_left_page = 1;
         $('#videoList').html(render(data.content));
         selectVideo();
         videoPlay();
       }
     });
     $.ajax({
       type: 'post',
       url: requestUrl,
       data: requestData_right,
       async: false,
       success: function(res) {
          // 已发布总页数
         var data = JSON.parse(res);
         self.videoPageAll_right = Math.ceil(parseInt(data.content.total,10)/pageSizeNum);
         self.loadMore_right_page = 1;
         $('#videoList_2').html(render(data.content));
         // self.loadVideo_right = true;
         selectVideo();
         videoPlay();
       }
     });
    
    },
    // 选择视屏
    _selectVideo: function() {
      var self =this;
       $('#selectVideoBtn').off().on('click',function(e) {
          e.preventDefault();
          var selectVideo = $('.video-ul').find('input:checked');
          if(!selectVideo[0]) {
            return null;
          }
          var videoId = selectVideo[0].id;
          var videoTitle = selectVideo[0].dataset.title;
          var videoPoster = selectVideo[0].dataset.poster;
          if(videoId) {
            sessionStorage.setItem('videoId',videoId);
            sessionStorage.setItem('videoTitle',videoTitle);
            // sessionStorage.setItem('videoPoster',videoPoster);
            self.statusData.poster = videoPoster;
            self.applyTo()
          }else {
            return null;
          }
       });
       // 取消选择
       $('#cancelVideoBtn').off().on('click',function(e) {
          e.preventDefault();
          sessionStorage.setItem('videoId','');
          sessionStorage.setItem('videoTitle','');
          // sessionStorage.setItem('videoPoster','');
          self.statusData.poster = '';
          self.applyTo();
       });
    },
    // 切换待发布和已发布
    _tabVideo: function() {
      var self = this;
      $('#tabVideo a').off().on('click',function() {
        var showId = '#' + this.dataset.video;
        var hideId = '#' + $(this).siblings()[0].dataset.video;
        $(this).addClass('hover')
               .siblings().removeClass('hover');
        $(showId).show();
        $(hideId).hide();

        // 加载更多依赖
        var loadMore = this.dataset.more;
        console.log(loadMore);
        if(loadMore === 'left') {
          self.loadMore_left = true;
          self.loadMore_right = false;
        }else {
          self.loadMore_right = true;
          self.loadMore_left = false;
        }
        self._loadMore();
      });
    },
    // 视屏页面加载更多数据
    _loadMore: function() {
      var self = this;
      console.log(self.videoPageAll_right);
      if(self.loadMore_left) {
        // 待发布
        if(self.videoPageAll_left > 1 && self.loadMore_left_page < self.videoPageAll_left) {
          $('#loadMore').show().siblings().hide();
          $('#loadMore').off().on('click',function() {
            loadMore('0');
          });
        }else {
          $('#noMore').show().siblings().hide();
        }
        
        // alert(self.videoPageAll_left);
      }else {
        // 已发布
        if(self.videoPageAll_right > 1 && self.loadMore_right_page < self.videoPageAll_right) {
          $('#loadMore').show().siblings().hide();
          $('#loadMore').off().on('click',function() {
            loadMore('2');
          });
        }else {
          $('#noMore').show().siblings().hide();
        }
      }

      

      function loadMore(type) {
        var videoType = type;
        var page = '';
        if(videoType === '0') {
          self.loadMore_left_page += 1;
          page = String(self.loadMore_left_page);

        }else {
          self.loadMore_right_page += 1;
          page = String(self.loadMore_right_page);
        }
        var requestUrl = $conf.api + 'video/myVideo.php';
        var requestData = {
          uid: sessionStorage.getItem('uid'),
          encpass: sessionStorage.getItem('encpass'),
          size: pageSize,
          type: videoType,
          page: page
        };
        ajaxRequest({url: requestUrl,data: requestData},function(data) {
          if(videoType === '0') {
            //待发布
            if(self.loadMore_left_page >= self.videoPageAll_left) {
              $('#noMore').show().siblings().hide();
            }
            $('#videoList').append(render(data));
          }else {
            if(self.loadMore_left_page >= self.videoPageAll_right) {
              $('#noMore').show().siblings().hide();
            }
            $('#videoList_2').append(render(data));
          }

          selectVideo();
          videoPlay();
        });

      }
    }
  };

  managerApply.init();
});

// 视频列表组模板串
function render(data) {
 'use strict';
  var videoList = data.list || [];
  var tpl = '';
  var videoStatus = '';
  var videoID = '';
  var videoTitle = '';
  var videoPoster = '';
  var videoUrl = '';
  var videoTimeLength = '';

  videoList.forEach(function(v) {
    switch(v.videoStatus) {
      case '0':
        videoStatus = '未发布';
        break;
      case '1':
        videoStatus = '待审核';
        break;
      default:
        videoStatus = '已发布';
        break;
    }
    videoID = v.videoID;
    videoTitle = v.title;
    videoPoster = v.poster;
    videoUrl = v.videoUrl;
    // videoUrl = '';
    videoTimeLength = v.videoTimeLength;
    tpl += '<li>\
              <div class="check-btn">\
                <input name="video-sel" type="radio" class="video-check none" \
                id="'+ videoID +'" data-title="'+ videoTitle +'" data-poster="'+ videoPoster +'">\
                <label for="'+ videoID +'"></label>\
              </div>\
              <div class="video-item clearfix">\
                <div class="video-left" data-url="'+ videoUrl +'">\
                  <img src="'+ videoPoster +'">\
                  <span>'+ videoTimeLength +'</span>\
                </div>\
              <div class="video-text">\
                  <p class="video-title">'+ videoTitle +'</p>\
                  <p class="video-status">'+ videoStatus +'</p>\
              </div>\
              </div>\
        </li>';
  });  
  
  return tpl;
}
// 点击列表选择视频
function selectVideo() {
 $('.video-ul li').off().on('click',function() {
   $(this).find('input').prop('checked',true);
 });
}
function videoPlay() {
  $('.video-item img').off().on('click',function(e){
    e.stopPropagation();
    try{
	    $('.video-player').show();	
    }
    catch(error)
    {
    	Console.log(error);	
    }

    $('.mask').show();
    var videoSrc = $(this).parents('.video-item').find('.video-left').attr("data-url");
    $('#videoPlay').attr("src",videoSrc);
  });
  $('.close').click(function(){
    $('.video-player').hide();
    $('.mask').hide();
    $('#videoPlay').attr("src","");
  });
}


$(function(){
	if(window.appSetTitle){
        window.appSetTitle('签约经纪公司');
    }else if(window.phonePlus.appSetTitle){
        window.phonePlus.appSetTitle('签约经纪公司');
    }
});
