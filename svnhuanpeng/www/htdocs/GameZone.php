<?php
/**
 *
 *   */
include 'init.php';
$path = realpath(__DIR__);
$gid = $_GET['gid'] ? (int) $_GET['gid'] : 0;
if (!$gid)
    exit;
$db = new DBHelperi_huanpeng();
$sql = "SELECT * FROM `game_zone` WHERE `gameid`= {$gid}";
$res = $db->query($sql);
$row = $res->fetch_assoc();

$sql = "SELECT `name` FROM `game` WHERE `gameid`={$gid}";
$res2 = $db->query($sql);
$row2 = $res2->fetch_row();
$row['gamename'] = $row2[0];
$game = json_encode($row);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo "{$row['gamename']}-欢朋直播-精彩手游直播平台！"; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php include $path . '/tpl/commSource.php'; ?>
        <link rel="stylesheet" type="text/css" href="./static/css/common.css??v=1.0.4">
        <link rel="stylesheet" type="text/css" href="./static/css/home_v3.css??v=1.0.4">
        <link rel="stylesheet" type="text/css" href="./static/css/person.css??v=1.0.4">
        <link rel="stylesheet" type="text/css" href="./static/css/head.css??v=1.0.4">
        <link rel="stylesheet" type="text/css" href="./static/css/GameZone.css??v=1.0.4">
        <script type="text/javascript" src="./static/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="static/js/hover.js?v=1.0.4"></script>
        <script type="text/javascript" src="./static/js/common.js?v=1.0.4"></script>
        <script type="text/javascript" src="./static/js/page.js?v=1.0.4"></script>
        <script type="text/javascript" src="static/js/home_data_load.js?v=1.0.4"></script>
        <script type="text/javascript" src="static/js/head.js?v=1.0.4"></script>
        <style type="text/css">
            .footer .footer-top{
                background: #f5f5f5;
            }
        </style>
    </head>

    <body>
        <?php include $path . '/head.php' ?>
        <div class="game_zone_contain" >

            <div id="gameZone">
                <div class="head_zone" style="visibility:hidden;">
                    <div class="right_block">
                        <div class="game_intro_title">
                            <div class="titletxt">游戏介绍</div>
                            <!--<div class="zone_icon_set d_btn">
                            <div class="txt">下载</div>
                            <div class="down_pop">
                            <div class="imgcode">
                            <div class="codepic">
                            <img src="http://kascdn.kascend.com/jellyfish/game/qrcode/151204/1449230816553.jpg"/>
                            </div>
                            <div class="down_block">
                            <div class="zone_icon_set app_btn android">安卓版</div>
                            <div class="zone_icon_set app_btn ios">苹果版</div>
                            </div>
                            </div>
                            </div>
                            </div>-->
                        </div>
                        <div class="txt_intro">
                            <div class="txt_text">
                                <?php echo htmlspecialchars(mb_substr(preg_replace('/\s+/', '', $row['description']), 0, 70, 'UTF-8')), '...'; ?>
                            </div>
                            <!--<div id="detail" class="zone_icon_set detail">详情</div>-->
                        </div>
                    </div>
                </div>
                <div class="g_block">
                    <div class="title">
                        <div class="game_name"></div>
                        <ul>
                            <li id="live_tab" class="cur l">
                            直播<!-- <span id="liveCount">(0)</span> -->
                            </li>
                            <li id="video_tab" class="r">
                            视频<!-- <span id="videoCount">(0)</span> -->
                            </li>
                        </ul>
                        <div class="right_block">
                            <span class="cur">最热</span>
                            <span>最新</span>
                            <span>最多关注</span>
                        </div>
                    </div>

                    <div  class="livevideo_block">
                        <div id="live" class="tab_block cur">
                            <div class="block_live">
                                <ul>


                                </ul>
                            </div>
                            <div class="pageIndex"></div>
                        </div>
                        <div id = "video"class="tab_block">
                            <div class="block_live">
                                <ul>

                                </ul>
                            </div>
                            <div class="pageIndex"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="intro_div">
            <div>
                <div id="intro" >
                    <!-- <div class="intro_block"> -->
                    <div class="zone_icon_set x"></div>
                    <div class="gamePic_title">
                        <?php echo $row['gamename'] ?>
                    </div>

                    <div id="movePic">
                        <div id="btn_left" class="zone_icon_set lr_btn"></div>
                        <div id="btn_right" class="zone_icon_set lr_btn"></div>
                        <div class="intro_pic">
                            <ul>
                                <li><img src="./static/img/src/zone/240x400-a.png"></li>
                                <li><img src="./static/img/src/zone/240x400-b.png"></li>
                                <li><img src="./static/img/src/zone/240x400-c.png"></li>
                                <li><img src="./static/img/src/zone/240x400-d.png"></li>
                                <li><img src="./static/img/src/zone/240x400-a.png"></li>
                                <li><img src="./static/img/src/zone/240x400-b.png"></li>
                                <li></li>
                            </ul>
                        </div>
                    </div>

                    <div id="introduce">
                        <?php echo htmlspecialchars($row['description']); ?>
                    </div>
                    <!-- </div> -->
                </div></div>
        </div>

        <?php include $path . '/footer.php'; ?>
        <?php include $path . '/tpl/toTop.php'; ?>
        <script>
            var game = <?php echo $game; ?>;

            var bgdom = document.getElementsByClassName('game_zone_contain')[0];
            if (game['bgpic']) {
                bgdom.style.background = 'url(' + $conf['img'] + '/' + game['bgpic'] + ') top center no-repeat';
            } else {
                bgdom.style.background = 'url(static/img/src/zone/bg-game-zone.png) top center no-repeat';
            }
            var preLoadLive = false;
            var preLoadVideo = false;
            var jsCode = '';
            function load(data) {
                data = API_MAP.liveList(data);
                var liveCount = data['liveCount'];
                var ref = data['ref'];
                var data = data['liveList'];
                var o = arguments[1];
                var blocki = arguments[2];
                if (typeof (data) != 'object' || data == null) {
                    var domUl = $('#live .block_live:eq(' + blocki + ') ul');
                    var domUlStr = '<div style="width:260px;height: 260px;margin: 50px auto 50px auto;"><img src="static/img/src/zone/no-live.png"></div>';
                
                    $(domUl).css('width', '100%').html(domUlStr);
                    hoveritem();
                    angleImage($('#live'));//设置高度
                    $("#live").data('liveCount', liveCount);
                    $(".game_name").text(ref);
                    if (!preLoadLive) {
                        pageFn(0);
                        preLoadLive = true;
                    }
                    return;
                }

                var domUl = $('#live .block_live:eq(' + blocki + ') ul');
                var domUlStr = '';
                var domLiStr = '';
                if (data.length > 0) {
                    for (var key = 0; key < data.length; key++) {
                        data[key].posterUrl = data[key].posterUrl ? data[key].posterUrl : '';
                        var angleStr = (parseInt(data[key].angle) == 0 && parseInt(data[key].ispic)) ? $conf.angleImage : '';
                        domLiStr = '<li class="h_item"><a href="' + o._ROOT  + data[key].roomID + '"><i></i><b></b>'
                                + '<div class="img_block"><img  class= "' + angleStr + '" src="' + data[key].posterUrl + '" onerror="' + jsCode + '">'
                                + '</div><div class="liveinfo">'
                                + '<p>' + data[key].liveTitle + '</p>'
                                + '<div class="icon1"></div>'
                                + '<span class="fl" style="width:65px;">' + data[key].nick + '</span>'
                                + '<div class="icon2"></div>'
                                + '<span class="fl">' + data[key].viewCount + '</span>'
                                + '<span class="fr last">' + data[key].gameName + '</span></div></a></li>';
                        domUlStr += domLiStr;
                    }
                } else {
                    domUlStr += '<div style="width:260px;height: 260px;margin: 50px auto 50px auto;"><img src="static/img/src/zone/no-live.png"></div>';
                }
                $(domUl).css('width', '100%').html(domUlStr);
                hoveritem();
                angleImage($('#live'));//设置高度
                $("#live").data('liveCount', liveCount);
                $(".game_name").text(ref);
                if (!preLoadLive) {
                    pageFn(0);
                    preLoadLive = true;
                }
            }
            function getVideoLen(utime) {
                var H = Math.floor(utime / 3600);
                var M = Math.floor((utime - H * 3600) / 60);
                var S = utime - H * 3600 - M * 60;
                H = H < 9 ? ('0' + H) : H;
                M = M < 9 ? ('0' + M) : M;
                S = S < 9 ? ('0' + S) : S;
                return H + ':' + M + ':' + S;
            }
            function loadVideo(data) {
                data = API_MAP.videoList(data);
                var liveCount = data['liveCount'];
                var ref = data['ref'];
                var data = data['liveList'];
                var o = arguments[1];
                var blocki = arguments[2];
                if (typeof (data) != 'object' || data == null) {
                    return;
                }

                var domUl = $('#video .block_live:eq(' + blocki + ') ul');
                var domUlStr = '';
                var domLiStr = '';
                if (data.length > 0) {
                    for (var key = 0; key < data.length; key++) {
                        data[key].posterUrl = data[key].posterUrl ? data[key].posterUrl : 'static/img/default/260x150.png';
                        var angleStr = (parseInt(data[key].angle) == 0) ? $conf.angleImage : '';
                        domLiStr = '<li class="h_item"><a href="' + o._ROOT + 'videoRoom.php?videoid=' + data[key].videoId + '"><i></i><b></b>'
                                + '<div class="img_block"><img class= "' + angleStr + '" src="' + data[key].posterUrl + '" onerror="' + jsCode + '">'
                                + '<div class="video-length">' + getVideoLen(data[key].videoTimeLength) + '</div></div><div class="liveinfo">'
                                + '<p>' + data[key].videoTitle + '</p>'
                                + '<div class="icon1"></div>'
                                + '<span class="fl">' + data[key].viewCount + '</span>'
                                + '<div class="icon2"></div>'
                                + '<span class="fl">' + data[key].commentCount + '</span>'
                                + '<span class="fr last">' + data[key].gameName + '</span></div></a></li>';
                        domUlStr += domLiStr;
                    }
                } else {
                    domUlStr += '<div style="width:260px;height: 260px;margin: 50px auto 50px auto;"><img src="static/img/src/zone/no-video.png"></div>';
                }
                $(domUl).css('width', '100%').html(domUlStr);
                hoveritem();
                angleImage($('#video'));//设置高度
                $("#video").data('videoCount', liveCount);
                if (!preLoadVideo) {
                    pageFn(1);
                    preLoadVideo = true;
                }
            }
            function error() {
                console.log('error');
            }
            var page = new page();
            page.init();
            var head = new head();
            var hd = new hd();

            var loadSize = 16;
            var type = 0;//默认0  最热排序
            var gid = <?php echo json_encode(isset($_GET['gid']) ? (int) $_GET['gid'] : ''); ?>;
            var liveReq = {
                uid: hd._uid,
                encpass: hd._enc,
                size: loadSize,
                gameID: gid,
                type: type
            };
            var url = './api/other/homePageGameList.php';
            hd._ajax(url, liveReq, load, error);

            function pageLoadFn(e, x, t) {
                var req = {
                    uid: hd._uid,
                    encpass: hd._enc,
                    size: loadSize,
                    gameID: gid,
                    page: e,
                    type: t
                };
                if (!x) {
                    var fn = load;
                    var url = './api/other/homePageGameList.php';
                    hd._ajax(url, req, fn, error);
                } else {
                    var fn = loadVideo;
                    var url = './api/video/getVideoPageList.php';
                    hd._ajax(url, req, fn, error);
                }
            }
            function refresh(p) {
                p.replaceWith('<div class="pageIndex"></div>');
            }
            function pageFn(x) {
                var count = 0;
                if (!x)
                    count = parseInt($('#live').data('liveCount'));
                else
                    count = parseInt($('#video').data('videoCount'));
                var pageSize = loadSize;
                count = Math.ceil(count / pageSize);
                var t = parseInt($('.g_block .right_block span.cur').index());
                var d = {pageCount: count,
                    current: 1,
                    pageMax: 6,
                    backFn: function (e) {
                        console.log(e);
                        pageLoadFn(e, x, t)
                    }
                }
                if (!x) {
                    refresh($("#live .pageIndex"));
                    $("#live .pageIndex").createPage(d);
                } else {
                    refresh($("#video .pageIndex"));
                    $("#video .pageIndex").createPage(d);
                }
            }

            $(function () {
                $(".block_title .fr").click(function () {
                    $(".block_title .fr.cur").removeClass("cur");
                    $(this).addClass("cur");
                });
                $(".title ul li").click(function () {
                    $(".title ul li.cur").removeClass("cur");
                    $(this).addClass("cur");
                    var indexi = $(this).index();
                    if (indexi < 2) {
                        $(".tab_block.cur").removeClass("cur");
                        $(".tab_block").eq(indexi).addClass("cur");
                    }
                });
                function upbtn(degi, rightIndex) {
                    if (degi == 0) {
                        $("#movePic #btn_right").addClass("cur");
                        $("#movePic #btn_left").removeClass("cur");
                    } else if (degi == -3) {
                        $("#movePic #btn_left").addClass("cur");
                        $("#movePic #btn_right").removeClass("cur");

                    } else {
                        $("#movePic #btn_left").addClass("cur");
                        $("#movePic #btn_right").addClass("cur");
                    }
                    $('.g_block .right_block span').click(function () {
                        $('.g_block .right_block span.cur').removeClass('cur');
                        $(this).addClass('cur');
                        var x = parseInt($('.g_block .title ul li.cur').index());
                        var t = parseInt($(this).index());
                        pageLoadFn(1, x, t);
                        var pageObj = x ? $('#video .pageIndex') : $('#live .pageIndex');
                        refresh(pageObj);
                        pageFn(x);
                    });
                }

                (function () {
                    var p = 255;
                    var degi = 0;
                    var len = $("#movePic ul li").length;
                    var dislen = 4;
                    upbtn(degi, dislen - len);
                    (function () {
                        $("#movePic #btn_right").click(function () {

                            if (degi > (dislen - len)) {

                                $("#movePic ul").css("transform", "translateX(" + (--degi * p) + "px)");
                            }
                            upbtn(degi, dislen - len);
                        });
                        $("#movePic #btn_left").click(function () {

                            if (degi < 0)
                                $("#movePic ul").css("transform", "translateX(" + (++degi * p) + "px)");
                            upbtn(degi, dislen - len);
                        });
                        $('#detail').click(function () {
                            $('#intro_div').css('display', 'block');
                            $('body').css('overflow', 'hidden');
                        });
                        $('#intro_div .x').click(function () {
                            $('#intro_div').css('display', 'none');
                            $('body').css('overflow', 'auto');
                        });

                    })()
                })();
                (function () {

                })();
                //初始化录像
                $('#video_tab').click(function () {
                    $('.g_block .right_block span:eq(0)').trigger('click');
                });

            })
        </script>
    </body>
</html>