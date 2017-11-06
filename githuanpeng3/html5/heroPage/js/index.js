/**
 * Created by junxiao on 2017/9/12.
 */
$(function() {
    var activityId = 0;
    var gameId = 0;
    var activityStatus = 0;
    var enrollPicSrc = '';
    var Vote = {
        init: function() {
            this._initVotePerson();
            this._initVoteEvent();
            this._initEnrollEvent();
            this._initEnrollInfo();
        },
        _initVotePerson: function(a) {

            if (a == 103) {
                var requestUrl = $conf.api + 'activity/voteActivity.php';
                ajaxRequest({ url: requestUrl }, function(responseData) {
                    var resHeros = responseData.heros;
                    for (var i = 0; i < $('.percent').length; i++) {
                        $('.percent').eq(i).text(resHeros[i].percent + '%');
                    }
                    return;
                })
                return;
            }

            //获取 投票信息
            var requestUrl = $conf.api + 'activity/voteActivity.php';
            ajaxRequest({ url: requestUrl }, function(responseData) {
                var htmlArr = [];
                var resList = responseData.heros; //英雄信息
                activityId = responseData.activity_id; //活动id
                gameId = responseData.game_id; //游戏分类id
                activityStatus = responseData.code; //活动状态id
                for (var i = 0; i < resList.length; i++) {
                    htmlArr.push(CreateLi(resList[i]));
                }
                $('#person-container').html(htmlArr);
                //点击选中
                $('.hero-tab').click(function() {
                    $('.hero-tab').removeClass('tabBG');
                    $(this).addClass('tabBG');
                });
            });

            var CreateLi = function(a) {
                var tpl = '<li class="hero-tab " data-heroID="' + a.hero_id + '" data-precent="' + a.percent + '">\
                                <div class="hero-tab1" style="background-image: url( ' + a.img + ')">\
                                <div class="hero-top">\
                                <div class="hero-name">\
                                <p class="name-top">' + a.desc + '</p>\
                                <p class="name-bottom">' + a.hero + '</p>\
                                <div class="percent">' + a.percent + '%</div>\
                                </div>\
                                </div>\
                                <div class="hero-bottom"></div>\
                                </div>\
                                </li>';
                return tpl;
            };

        },
        _initVoteEvent: function() {

            $('#vote-btn').click(function() {
                var uid = getCookie('_uid') || sessionStorage.getItem('_uid');
                var encpass = getCookie('_enc') || sessionStorage.getItem('_enc');

                var heroId = $('.tabBG').attr('data-heroID');

                if (!LinkToH5()) {
                    return;
                }

                if (!heroId || heroId == '') {
                    mobileTips('请选择英雄后,再投票哦');
                    return;
                }

                var requestUrl = $conf.api + 'activity/vote.php';
                var requestData = {
                    uid: uid,
                    encpass: encpass,
                    activity_id: activityId || '',
                    hero_id: heroId || '',
                    game_id: gameId || ''
                };
                ajaxRequest({ url: requestUrl, data: requestData }, function(responseData) {
                    // console.log(responseData);
                    mobileRight('投票成功');
                    Vote._initVotePerson(103);
                }, function(responseData) {
                    mobileTips(responseData.desc);
                })
            })
        },
        _initEnrollEvent: function() {
            $('.sign-btn').click(function() {
                if (!LinkToH5()) {
                    return;
                }
                $('.layer').addClass('show');
            });
            $('.layer .close-btn').click(function() {
                $('.layer').removeClass('show');
            });
            $('#enroll_upload').change(function() {
                var form = $('#enroll_upload');
                var uid = getCookie('_uid') || sessionStorage.getItem('_uid') || '';
                var encpass = getCookie('_enc') || sessionStorage.getItem('_enc') || '';

                if (!LinkToH5()) {
                    return;
                }
                mobileLoading(1);
                form.ajaxSubmit({
                    url: $conf.api + 'activity/activityEnroll.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        uid: uid,
                        encpass: encpass
                    },
                    success: function(d) {

                        if (d.status == '1') {
                            var data = d.content;
                            enrollPicSrc = data.img;
                            $('.screenshot-btn').css('background-image', 'url(' + data.img + ')');
                            mobileLoading(0);
                        } else {
                            mobileLoading(0);
                            console.log(d.content.desc);
                            mobileTips('图片上传失败');
                        }
                    },
                    error: function(e) {
                        mobileLoading(0);
                        console.log(e);
                        mobileTips('文件不符合标准');
                    }
                });
            })
        },
        _initEnrollInfo: function() {
            $('#enroll-btn').click(function() {
                // var hpUsername = $('#hp-username').val();
                var gameUsername = $('#game-username').val();
                var qqNum = $('#qq-username').val();
                var gameLevel = $('#game-level').val();

                // if (!hpUsername || hpUsername == '') {
                //     mobileTips('请填写平台昵称~');
                //     return;
                // } 
                if (!gameUsername || gameUsername == '') {
                    mobileTips('请填写游戏昵称~');
                    return;
                } else if (!qqNum || qqNum == '') {
                    mobileTips('请填写QQ~');
                    return;
                } else if (!gameLevel || gameLevel == '') {
                    mobileTips('请填写段位等级~');
                    return;
                } else if (!enrollPicSrc || enrollPicSrc == '') {
                    mobileTips('请上传段位截图~');
                    return;
                }

                var uid = getCookie('_uid') || sessionStorage.getItem('_uid') || '';
                var encpass = getCookie('_enc') || sessionStorage.getItem('_enc') || '';

                var requestUrl = $conf.api + 'activity/activityEnroll.php';
                var requestData = {
                    uid: uid,
                    encpass: encpass,
                    activity_id: activityId,
                    game_id: gameId,
                    game_nick: gameUsername,
                    qq: qqNum,
                    level: gameLevel,
                    img: enrollPicSrc
                };
                ajaxRequest({ url: requestUrl, data: requestData }, function(responseData) {
                    mobileRight('报名成功');
                    $('.layer').removeClass('show');
                }, function(responseData) {
                    mobileTips(responseData.desc);
                })
            })
        }
    };

    Vote.init();

});

function mobileTips(a) {
    $('#error-content').text(a);
    $('.modal-box, .error-modal').show();
    setTimeout(function() {
        $('.modal-box, .error-modal').hide();
    }, 1000)
}

function mobileRight(a) {
    $('#success-content').text(a);
    $('.modal-box, .modal-success').show();
    setTimeout(function() {
        $('.modal-box, .modal-success').hide();
    }, 1000)
}

function mobileLoading(a) {
    if (a === 1) {
        $('.modal-box, .modal-loading').show();
    } else {
        $('.modal-box, .modal-loading').hide();
    }
}

function QueryLocation() {
    var b = location.href;
    var c = new Object();
    if (b.indexOf('?') > -1) {
        var str = b.substr(b.indexOf('?') + 1);
        var strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            c[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    return c;
}

function LinkToH5() {
    //应用内
    if (window.huanpengShare || window.phonePlus || window.appAndroid || window.appCloseWebPage || window.appLogin || window.appSendCommand) {
        if (!sessionStorage.getItem('_uid') || sessionStorage.getItem('_uid') == '') {
            mobileTips('请登录后再操作!');
            return false;
        } else {
            return true;
        }

    } else {
        if (!getCookie('_uid')) {
            mobileTips('请登录后再操作!');

            setCookie('ref_url', encodeURIComponent(location.href));
            location.href = $conf.domain + '/mobile/h5login/index.html';

            return false;
        } else {
            return true;
        }
    }
}