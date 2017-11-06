/**
 * Created by dell on 2016/12/6.
 */
$(function () {

    //modal事件
    $('#getBtn,#openList,.mobileBtn-center,.mobile-right').click(function () {
        if(!check_login()){
            return;
        }
        $('#listModal,.modalBg').show();
    });

    $('#closeModal,.modalBg').click(function () {
        $('#listModal,.modalBg').hide();
    });

    $('#getBtn').click(function () {
        modalTest();
    });
    listTest();
    awardTest();

    //300秒 请求奖励榜单数据
    setInterval(listTest,300000);

});

//奖励榜单 测试
function listTest() {
    try{

        var requestUrl = $conf.api + 'user/invite/ranking.php';
        var requestData = {size:6};

        ajaxRequest({url:requestUrl, data:requestData},function(responseData){
            var content = [];
            var resList = responseData.list;
            for(var i in resList){
                content.push(CreateTable(resList[i]));
            }
            $("#leftTable").html(content);

        },function(d){});
        
    }catch (e){
        return false;
    }
}
//领奖  测试
function awardTest() {
    try {

        var localCache = [];
        var markValue = '';
        awardFirst();


        function awardFirst() {
            var requestUrl = $conf.api + 'user/invite/dynamic.php';
            ajaxRequest({url: requestUrl}, function (responseData) {
                markValue = responseData.mark;
                var resFirData = responseData.list;
                for (var i = 0; i < resFirData.length; i++) {
                    localCache.push(resFirData[i]);
                }

                rollRequest();
            });
        }


        function rollRequest() {
            ajaxRoll();
            rollTemplate();
            function ajaxRoll() {
                var requestUrl = $conf.api + 'user/invite/dynamic.php';
                var requestData = {
                    mark: markValue
                };
                ajaxRequest({url: requestUrl, data: requestData}, function (responseData) {
                    markValue = responseData.mark;
                    var resList = responseData.list;
                    for (var i in resList) {
                        localCache.push(resList[i]);
                    }
                    setTimeout(ajaxRoll, 1000);
                })
            }
        }

        function rollTemplate() {
            var iCount = 0;

            var timer;
            $('#listSc').hover(function () {
                clearInterval(timer);
            }, function () {

                timer = setInterval(function () {

                    if (iCount < localCache.length) {

                        if ($('#listSc>li').length <= 6 && $('#listSc>li').length > 0) {

                            $('#listSc').stop().animate({marginTop: 0}, 1000, function () {
                                $(CreateAward(localCache[iCount])).prependTo('#listSc');

                                if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i)) {
                                    $('#listSc').css({marginTop: '-2.3125rem'});
                                } else {
                                    $('#listSc').css({marginTop: '-60px'});
                                }

                            });

                        } else {

                            $('#listSc').stop().animate({marginTop: 0}, 1000, function () {
                                $(CreateAward(localCache[iCount])).prependTo('#listSc');
                                $('#listSc>li:last').remove();
                                if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i)) {
                                    $('#listSc').css({marginTop: '-2.3125rem'});
                                } else {
                                    $('#listSc').css({marginTop: '-60px'});
                                }
                            });

                        }

                    } else {
                        iCount = 0;
                    }

                    iCount++;
                }, 2000);

            }).trigger('mouseleave');
        }
    }catch (e){

    }
}
//modal 测试 (需要uid和encpass)
function modalTest() {
    try{
        var requestUrl = $conf.api + 'user/invite/recordList.php';
        var requestData = {size:6,uid:getCookie('_uid'),encpass:getCookie('_enc')};
        ajaxRequest({url:requestUrl,data:requestData},function(responseData){

            $('#total,#mobileTotal').html(responseData.total);

            var resList = responseData.list;

            var listModal = [];

            if(resList.length == 0){
                $('#modalCotent').html('暂时没有邀请人哦');
            }else{
                for(var k in resList){
                    listModal.push(CreateModal(resList[k]));
                }
            }
            $('#modalCotent').append(listModal);

            //领取按钮 点击 发送事件=>服务器
            $('.getImg').click(function () {

                if(!check_login()) return;

                var self = this;

                var requestUrl = $conf.api + 'user/invite/recive.php';
                var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc')};

                ajaxRequest({url:requestUrl,data:requestData}, function (responseData) {
                    alert('领取成功');
                    $(self).addClass('doneImg');
                },function (responseData) {
                    if(responseData.type == 1){
                        alert('领取失败');
                    }else if(responseData.type == 2){
                        alert(responseData.desc);
                    }
                });
            })

        },function(respsonseData){
            $('#modalCotent').html('您还没有登录哦');
        });
    }catch (e){
        return false;
    }
}
//奖励榜单Template
function CreateTable(obj) {

    if (obj && obj.nick && obj.total){
        var tr = '<tr>\
        <td>'+obj.nick+'</td>\
        <td>共获得'+numTotype(obj.total)+'个欢朋币</td>\
        </tr>';
        return tr;
    }
}
//领奖Template
function CreateAward(obj) {
    if (obj && obj.nick && obj.total){

        var li = '<li>\
                    <p>\
                        <span class="userName">'+obj.nick+'</span>\
                        <span>共获得</span>\
                        <span class="money">'+numTotype(obj.total)+'个欢朋币</span>\
                    </p>\
                </li>';

        return li;
    }
}
//modal Template
function CreateModal(obj) {
    if(obj){

        if(obj.record && obj.record == 500){
            var objTime = obj.ctime.substr(0,16);
            var status = '';
            if(obj.status == 0){
                status = 'getImg';
            }else if (obj.status == 1){
                status = 'doneImg';
            }

            var trModal = '<tr>\
                        <td>'+obj.nick+'</td>\
                        <td>'+objTime+'</td>\
                        <td>'+numTotype(obj.record)+'个欢朋币</td>\
                        <td><button class="'+status+'"></button></td>\
                    </tr>';
            return trModal;
        }

    }
}

function numTotype(num){
    if(typeof num == 'number'){     
        if(num > 10000){
            var parseNum = parseInt(num);
            var iNum = parseNum % 10000;
            var reverseParseNum = parseNum.toString().split('').reverse().join('');
            var reIndex = iNum.toString().split('').reverse().join('');
            var reg = new RegExp(reIndex,'');
            var needStr = reverseParseNum.replace(reg,'万').split('').reverse().join('');
            delete reIndex;
            return needStr;
        }else{
            return num;
        }
    }else{
        return num;
    }
}