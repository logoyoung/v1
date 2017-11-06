/**
 * by fanguang 2017/07/18
 * @file  获取签约视频h5
 * @entry managerVideo.init
 */

 $(function() {
   
    var managerVideo = {
     init: function() {
       this._getVideo();
       this._selectVideo();
       
     },
     // 获取视屏列表
     _getVideo: function() {
       var requestUrl = $conf.api + 'video/myVideo.php';
       var requestData = {
         uid: sessionStorage.getItem('uid'),
         encpass: sessionStorage.getItem('encpass')
       };
       ajaxRequest({url: requestUrl,data:requestData},function(data) {
         render(data);
         selectVideo();
       });

       function render(data) {
         'use strict';
          var videoList = data.list;
          console.log(videoList);
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
            videoTimeLength = v.videoTimeLength;
          	tpl += '<li>\
    			  <div class="check-btn">\
    			    <input name="video-sel" type="radio" class="video-check none" \
    			    id="'+ videoID +'" data-title="'+ videoTitle +'" data-poster="'+ videoPoster +'">\
    			    <label for="'+ videoID +'"></label>\
    			  </div>\
    			  <div class="video-item clearfix">\
    			    <div class="video-left" data-src="'+ videoUrl +'">\
    			      <img src="'+ videoPoster +'">\
    			      <span>'+ videoTimeLength +'</span>\
    			      <div class="video-playbox">\
    				      <video controls="controls">\
    					    <source type="video/mp4" src="'+ videoUrl +'"></source>\
    				      </video>\
    			      </div>\
    			    </div>\
    				<div class="video-text">\
    			      <p class="video-title">'+ videoTitle +'</p>\
    			      <p class="video-status">'+ videoStatus +'</p>\
    				</div>\
    			  </div>\
          			</li>';
          });
          $('#videoList').html(tpl);
          managerVideo._videoPlay();
       }
       // 点击列表选择视频
       function selectVideo() {
         $('#videoList li').on('click',function() {
           $(this).find('input').prop('checked',true);
         })
       }
     },
     // 选择视屏
     _selectVideo: function() {
       $('#selectVideoBtn').on('click',function(e) {
       	  e.preventDefault();
       	  var selectVideo = $('#videoList').find('input:checked');
       	  var videoId = selectVideo[0].id;
       	  var videoTitle = selectVideo[0].dataset.title;
          var videoPoster = selectVideo[0].dataset.poster;
       	  if(videoId) {
       	  	sessionStorage.setItem('videoId',videoId);
       	  	sessionStorage.setItem('videoTitle',videoTitle);
            sessionStorage.setItem('videoPoster',videoPoster);
            // location.search = '';
            // location.pathname = '/mobile/beAnchor/managerApply.html';
            // location.href = $conf.domain + 'mobile/beAnchor/managerApply.html';
            location.replace($conf.domain + 'mobile/beAnchor/managerApply.html'+ new Date().getTime());
       	  }else {
       	  	return null;
       	  }
       });
       // 取消选择
       $('#cancelVideoBtn').on('click',function(e) {
          e.preventDefault();
          sessionStorage.setItem('videoId','');
          sessionStorage.setItem('videoTitle','');
          sessionStorage.setItem('videoPoster','');
          // history.back();
          var href = $conf.domain + 'mobile/beAnchor/managerApply.html' + new Date().getTime();
          location.replace(href);
       })
     },
     _videoPlay: function(){
     	$('.video-item img').on('click',function(e){
        e.stopPropagation();
        $('.video-player').show();
        $('.mask').show();
        var videoSrc = $(this).parents('.video-item').find('.video-left').attr("data-src");
        $('#videoPlay').attr("src",videoSrc);
      
      });
     	$('.close').click(function(){
     		$('.video-player').hide();
     		$('.mask').hide();
     	})
     }
    };
    managerVideo.init();
 });

function styleTo(i) {
		var body  = i.contentWindow.document.querySelector('body');
		body.style.height = '100%';
        var video = i.contentWindow.document.querySelector('video');
        video.style.width = '100%';
		video.style.height = '100%';
}