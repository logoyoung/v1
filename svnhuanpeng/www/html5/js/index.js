/**
 * Created by junxiao on 2017/3/24.
 */
$(function () {
    var mobile = {
        init : function () {
            mobile._initSize();
            mobile._initSwiper();
            mobile._initGuessYouLike();
            mobile._initBindBtn();
        },
        _initSize : function () {
            remSize();
            window.onresize = function (){
                remSize();
            };
        },
        _initSwiper : function () {
            //swiper PIC
            /*$.post('../../api/information/getInformation.php',{type:0},function (d) {
             var res = JSON.parse(d);
             if(res.status == 1){
             var resPlist = res.content.plist;
             var picHtml = [];
             for(var i = 0; i < resPlist.length; i++){
             picHtml.push(CreateSlide(resPlist[i]));
             }
             $('#swiper-wrapper').html(picHtml);

             if($('.swiper-slide').length != 1){
             var swiper = new Swiper('.swiper-container', {
             autoplay: 3000,
             loop: true,
             // 如果需要分页器
             pagination: '.swiper-pagination'
             });
             }
             }
             });*/

            var swiper = new Swiper('.swiper-container', {
             autoplay: 3000,
             loop: true,
             // 如果需要分页器
             pagination: '.swiper-pagination'
             });
        },
        _initGuessYouLike : function () {
            //Hot推荐
            var index = 0;
            var initTotal = 16;
            var moreDom = '<div class="clickMore">\
                                    <div class="moreStatus" style="display:none;">\
                                        <div class="dot1"></div>\
                                        <div class="dot2"></div>\
                                    </div>\
                                    <p id="moreBtn">点击加载更多</p>\
                                </div>\
                            <div class="more-div"></div>';
            var rqUrl = $conf.api + 'other/homePageGameList.php';
            var rqData = { 
                    size : initTotal ,
                    lastId : index,
                    type : 0
            };
            ajaxRequest({url:rqUrl,data:rqData},function (res) {
            
                if(!res.list || res.list == ''){
                    return;
                }
                if(res.total <= initTotal){
                // if(0){
                    var ulHtml = [];
                    var resList = res.list;
                    for(var i = 0; i < resList.length; i++){
                        ulHtml.push(CreateLi(resList[i]));
                    }
                    $('#Live-list').html(ulHtml).append('<div class="more-div"></div>');
                    setTimeout(function () {
                        $('.loadingMask').css('display','none');
                        $('.visibile').css('visibility','visible');
                    },1500);
                }else{
                    index++;
                    var ulHtml = [];
                    var resList = res.list;
                    for(var i = 0; i < resList.length; i++){
                        ulHtml.push(CreateLi(resList[i]));
                    }
                    
                    $('#Live-list').html(ulHtml).append(moreDom);
                    setTimeout(function () {
                        $('.loadingMask').css('display','none');
                        $('.visibile').css('visibility','visible');
                    },1500);

                    $('#moreBtn').on('click', function() {
                        $('#moreBtn').hide();
                        $('.moreStatus').show();
                        ajaxRequest({url:rqUrl,data:{ size : initTotal , lastId : index, type : 0 }},function (res) {
                            var len = $('#Live-list').find('.liveOne').length;
                            if(res.total == 0){
                                $('#moreBtn').text('没有更多了').attr('id','endBtn');
                                $('#endBtn').css('display','block');
                                $('.moreStatus').hide();
                                return false;
                            }

                            if(len < res.total){
                                index++;
                                var moreHtml = [];
                                var resList = res.list;
                                for(var i = 0; i < resList.length; i++){
                                    moreHtml.push(CreateLi(resList[i]));
                                }
                                setTimeout(function () {
                                    $('#moreBtn').show();
                                    $('.moreStatus').hide();
                                    $('#Live-list .clickMore').before(moreHtml);
                                },1500)
                            }else{
                                var moreHtml = [];
                                var resList = res.list;
                                for(var i = 0; i < resList.length; i++){
                                    moreHtml.push(CreateLi(resList[i]));
                                }
                                setTimeout(function () {
                                    $('#moreBtn').show();
                                    $('.moreStatus').hide();
                                    $('#Live-list .clickMore').before(moreHtml);
                                },1500)
                            }

                        })
                    });
                }
            
            });
        },
        _initBindBtn : function () {
            var downApp = $('.openApp');
            var closeBtn = $('.close');
            var hpDownload = $('.hp_download');

            downApp.ontouchstart = function(){
                this.style.backgroundColor= '#ff5a00';
            };
            downApp.ontouchend = function(){
                this.style.backgroundColor= '#ff7800';
            };
            closeBtn.onclick= function(){
                hpDownload.style.display = 'none';
            };
        }
    };
    mobile.init();
});

function remSize(){
    var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;
    document.documentElement.style.fontSize = winW / 23.4375 +'px';
}
function CreateLi(obj) {
    var roomLink = '../h5share/live.php?u='+obj.uid;
    var tpl      = '<li class="liveOne">\
                            <a href="'+roomLink+'">\
                            <div class="div-poster">\
                            <img class="img_poster" src="'+obj.poster+'">\
                            <p class="img_title">'+obj.gameName+'</p>\
                            <div class="img_author">\
                            <img src="'+obj.head+'">\
                            </div>\
                            </div>\
                            <div class="author-desc">\
                            <p class="author-name">'+obj.nick+'</p>\
                            <p class="author-person">'+numberFormat(obj.viewCount)+'人</p>\
                        </div>\
                            <section class="room-name">'+obj.title+'</section>\
                            </a>\
                        </li>';
    return tpl;
}
