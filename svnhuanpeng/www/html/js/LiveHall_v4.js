$(function () {
    angleImage($conf.angleImage);

    if($('.hp_hotList').find('.h_item').length < 24){
        $('.more_live').hide();
    }else{
        $('.more_live').show();
    }

    var i = 1;
    var m = 25;
    $('.block_title .fr').click(function () {
        i = 1;
        angleImage($conf.angleImage);
        $('.block_title .fr').removeClass('cur');
        $(this).addClass('cur');
        var type = 4- parseInt($(this).index());
        $('.more_live').attr('data-type',type);
        $('.block_list').addClass('none');
        $('.block_list').eq(type).removeClass('none');
        $('.more_live').text('加载更多直播');
    });
    $('.more_live').click(function(){

        var index = parseInt($('.more_live').attr('data-type'));

        var len = $('.block_list').eq(index).find('li').length;

        var requestData = {
            type:'post',
            dataType: 'json',
            data:{
                uid : getCookie('_uid') ? getCookie('_uid') : '',
                encpass : getCookie('_enc') ? getCookie('_enc') : '',
                size    : 24,
                lastId  :  i,
                type    :  0
            },
            url: $conf.api+'other/homePageGameList.php'
        };
        if(len < m){
            i++;
            ajaxRequest(requestData,function (responseData) {
                m = responseData.total;
                $('.num').text(responseData.total);
                var responseList = responseData.list;
                var html = [];
                for(var i = 0; i < responseList.length; i++){
                    html.push(Create_li(responseList[i]));
                }
                $('.block_list').eq(index).append(html);
                angleImage($conf.angleImage);
            });
        }else{
            $(this).text('已经到底了～^～');
        }

    });
    function Create_li(obj) {
        if(obj.orientation == 0){
            var tpl = '<li class="h_item">\
                        <a href="./'+obj.roomID+'" target="_blank">\
                            <div class="img_block"><i></i><b></b>\
                                <img class="angle_class" src="'+obj.poster+'">\
                            </div>\
                            <div class="liveinfo">\
                                <p>'+obj.title+'</p>\
                                <div class="icon1"></div>\
                                <span class="fl nick" data-uid='+obj.uid+'>'+obj.nick+'</span>\
                                <div class="icon2"></div>\
                                <span class="fl">'+obj.viewCount+'</span>\
                                <span class="fr last">'+obj.gameName+'</span>\
                            </div>\
                        </a></li>';
            return tpl;
        }else {
            var tpl = '<li class="h_item">\
                        <a href="./'+obj.roomID+'" target="_blank">\
                            <div class="img_block"><i></i><b></b>\
                                <img class="" src="'+obj.poster+'">\
                            </div>\
                            <div class="liveinfo">\
                                <p>'+obj.title+'</p>\
                                <div class="icon1"></div>\
                                <span class="fl nick" data-uid='+obj.uid+'>'+obj.nick+'</span>\
                                <div class="icon2"></div>\
                                <span class="fl">'+obj.viewCount+'</span>\
                                <span class="fr last">'+obj.gameName+'</span>\
                            </div>\
                        </a></li>';
            return tpl;
        }

    };
    window.onscroll = function () {
        if($(window).scrollTop() >= 50){
            $('.to_top').show();
        }else{
            $('.to_top').hide();
        }
    };

    //当窗口大小发生改变
    $(window).resize(function(){
        _Width_resize = $(window).width();
        if (_Width_resize >= 1180){
            w1180();
        }else{
            w980();
        }
    });

    function w1180(){
        $("#liveHall").removeClass("w980").addClass("w1180");
    }
    function w980(){
        $("#liveHall").removeClass("w1180").addClass("w980");
    }
    setTimeout(function () {
        if ($(window).width() >= 1180){
            w1180();
        }else{
            w980();
        }
    },1000/60);

    $(".to_top").click(function () {
        var speed=200;
        $('body,html').animate({ scrollTop: 0 }, speed,function(){
            return;
        });

    });
});