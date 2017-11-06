
<?php

exit;
?>
<html>
<head>
    <title></title>
    <meta charset="utf-8" />
    <title>个人中心-欢朋直播-精彩手游直播平台！</title>
    <link rel="stylesheet" type="text/css" href="../../static/css/common.css?v=1.0.4">
    <link rel="stylesheet" type="text/css" href="../../static/css/home.css?v=1.0.4">
    <link rel="stylesheet" type="text/css" href="../../static/css/person.css?v=1.0.5">
    <script type="text/javascript" src="../../static/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../../static/js/common.js?v=1.0.4"></script>
	<script type="text/javascript" src="../../static/js/page.js?v=1.0.4"></script>
	<script type="text/javascript" src="../../static/js/home_data_load.js?v=1.0.4"></script>
</head>
<style type="text/css">
    body{
        background-color:  #eeeeee;
    }

    #noticeBox .theBox .box_body .inviteShareDiv{
        padding: 0px 20px;
    }
    #noticeBox .theBox .box_body .inviteShareDiv .shareOne{
        width: 150px;
        height: 132px;
        float: left;
        margin-bottom: 30px;
        margin-top: 15px;
    }
    #noticeBox .theBox .box_body .inviteShareDiv .shareOne .share_iconDiv{
        width: 96px;
        height: 96px;
        float: left;
        border-radius: 95px;
        margin-left: 27px;
        margin-bottom: 20px;
        cursor: pointer;
    }
    #noticeBox .theBox .box_body .inviteShareDiv .shareOne .share_iconDiv .share_icon{
        display: block;
        float: left;
        margin-left: 23px;
        margin-top: 28px;
        width: 50px;
        height: 40px;
    }
    .rule_item p{
        margin-top: 0;
        margin-bottom: 10px;
        color: #979696;
		line-height: 12px;
    }
    .rule_item p.notice{
        color: #f44336;
    }
    .rule_item .main_title{
        margin-bottom: 15px;
        font-size: 16px;
        color: #303031;
        font-weight: bold;
    }
    .rule_item .subTitleDiv{
        padding-left: 25px;
    }
    .bg_red{
        background-color: #fe646c;
    }
    .bg_yellow{
        background-color: #fed452;
    }
    .bg_green{
        background-color: #54d860;
    }
    .bg_blue{
        background-color: #46a6ea;
    }
    .bg_deepblue{
        background-color: #3488c5;
    }
</style>
<body>
<?php  include '../../header.html'; ?>
    <div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
        <div class="content">
            <div id="invite">
                <div class="inviteRule">
                    <div class="invite_banner">
                        <img src="../../static/img/banner_invite.png">
                    </div>
                    <div class="inviteRule_detail">
                        <div class="rule_item">
                            <p class="main_title">
                                <span class="personal_icon playingIcon"></span>
                                邀请主播
                            </p>
                            <div class="subTitleDiv">
                                <p class="sub_title">1.大王叫我来巡山，我把人间转一转，打起我的鼓，敲起我的锣，生活充满节奏感</p>
                                <p class="sub_title">2.大王叫我来巡山，抓个和尚做晚餐，这山间的水，无比的甜，不羡鸳鸯不羡仙</p>
                                <p class="sub_title">3.xxxxxxxxxxx</p>
                                <p class="sub_title">4.xxxxxxxxxxx</p>
                                <p class="sub_title notice">(xxxxxx)</p>
                            </div>
                        </div>
                        <div class="rule_item mt-20">
                            <p class="main_title">
                                <span class="personal_icon playingIcon"></span>
                                邀请主播
                            </p>
                            <div class="subTitleDiv">
                                <p class="sub_title">xxxxxxxxxxx</p>
                                <p class="sub_title notice">(xxxxxx)</p>
                            </div>
                        </div>
                    </div>
                    <div class="inviteOpt">
                        <button id="inviteNow" class="btn mt-15">立即邀请</button>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="inviteResult">
                    <ul class="select_tab">
                        <li class="selected">邀请主播</li>
                        <li>邀请观众</li>
                        <div class="clear"></div>
                    </ul>
                    <div class="tab_con mt-20">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>邀请日期</th>
                                    <th>房间号</th>
                                    <th>主播昵称</th>
                                    <th>奖励状态</th>
                                    <th>获得奖励</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2015.11.29</td>
                                    <td>0375</td>
                                    <td>大王叫我来巡山</td>
                                    <td class="unfinish">未完成</td>
                                    <td class="unfinish">--</td>
                                </tr>
                                <tr>
                                    <td>2015.11.29</td>
                                    <td>0375</td>
                                    <td>抓个和尚做晚餐</td>
                                    <td class="finish">已完成</td>
                                    <td class="finish">5000币</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab_con mt-20 none">
                          <table class="table">
                            <thead>
                                <tr>
                                    <th>邀请日期</th>
                                    <th class="blank"></th>
                                    <th>主播昵称</th>
                                    <th>奖励状态</th>
                                    <th>获得奖励</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2015.11.29</td>
                                    <td></td>
                                    <td>这个杀手不太冷</td>
                                    <td class="unfinish">未完成</td>
                                    <td class="unfinish">--</td>
                                </tr>
                                <tr>
                                    <td>2015.11.29</td>
                                    <td></td>
                                    <td>这个杀手不太冷</td>
                                    <td class="finish">已完成</td>
                                    <td class="finish">5000币</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab_con mt-20 none">
                        <div class="not_invite">
                            <span class="logo"></span>
                            <p class="noticeWord">您还没有邀请到人哦～</p>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
 <!--    <div id="noticeBox" style="position:fixed;left:50%;top:220px;z-index: 1000;">
        <div class="theBox" style="padding: 30px 20px; height: 370px;">
            <div class="box_head">
                <p class="title left">邀请好友</p>
                <div class="closeBox">
                    <span class='personal_icon close'></span>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="box_body">
                <div class="inviteShareDiv">
                    <div class="shareOne">
                        <span class="share_icon weibo bg_red"></span>
                        <div class="clear"></div>
                        <p>新浪微博</p>
                    </div>
                    <div class="shareOne">
                        <span class="share_icon qqzone bg_yellow"></span>
                        <div class="clear"></div>
                        <p>QQ空间</p>
                    </div>
                    <div class="shareOne">
                        <span class="share_icon qqfriend bg_blue"></span>
                        <div class="clear"></div>
                        <p>QQ好友</p>
                    </div>
                    <div class="shareOne">
                        <span class="share_icon wx bg_green"></span>
                        <div class="clear"></div>                    
                        <p>朋友圈</p>
                    </div>
                    <div class="shareOne">
                        <span class="share_icon wxfriend bg_green"></span>
                        <div class="clear"></div>                    
                        <p>微信好友</p>
                    </div>
                    <div class="shareOne">
                        <span class="share_icon copylink bg_deepblue"></span>
                        <div class="clear"></div>                    
                        <p>复制链接</p>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</body>
<script type="text/javascript" src="../../static/js/jquery.qrcode.min.js"></script>
<script type="text/javascript" src="invite.js?v=1.0.4"></script>
<script type="text/javascript">
	var page=new page();
	page.init();
	personalCenter_sidebar('invite');
    $('.sidebar_center ul li').eq(3).addClass('currentpage');
    $(document).ready(function(){
        (function(){
            var select = $('.inviteResult .select_tab li');
            select.bind('click', function(){
                select.removeClass('selected');
                $(this).addClass('selected');

                var i = $(this).index();

                $('.inviteResult .tab_con').addClass('none');
                $('.inviteResult .tab_con').eq(i).removeClass('none');
            })
        }());
		InviteFriend.init();
//        (function(){
//            function inviteBoxHtml(){
//                function headHtml(){
//                    var htmlstr = '';
//                    htmlstr += '<div class="box_head">';
//                    htmlstr += '<p class="title left">邀请好友</p>';
//                    htmlstr += '<div class="closeBox">';
//                    htmlstr += '<span class="personal_icon close"></span>';
//                    htmlstr += '<div class="clear"></div>';
//                    htmlstr += '</div>';
//                    htmlstr += '</div>';
//
//                    return htmlstr;
//                }
//                function bodyHtml(){
//                    var htmlstr = '';
//                    htmlstr += '<div class="box_body">';
//                    htmlstr += '<div class="inviteShareDiv">';
//                    htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_red"><span class="share_icon weibo"></span></div><div class="clear"></div><p>新浪微博</p></div>';
//                    htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_yellow"><span class="share_icon qzone"></span></div><div class="clear"></div><p>QQ空间</p></div>';
//                    htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_blue"><span class="share_icon qqfriend"></span></div><div class="clear"></div><p>QQ好友</p></div>';
//                    htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_blue"><span class="share_icon qqwb"></span></div><div class="clear"></div><p>腾讯微博</p></div>';
//                    htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_green"><span class="share_icon wxfriend"></span></div><div class="clear"></div><p>微信</p></div>';
//                    htmlstr += '<div class="shareOne"><div class="share_iconDiv bg_deepblue"><span class="share_icon copylink"></span></div><div class="clear"></div><p>复制链接</p></div>';
//                    htmlstr += '</div>';
//                    htmlstr += '</div>';
//
//                    return htmlstr;
//                }
//
//                var htmlstr = '';
//                htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:220px;z-index: 1000;">';
//                htmlstr += '<div class="theBox" style="padding: 30px 20px; height: 370px;">';
//                htmlstr = htmlstr + headHtml() + bodyHtml() + "</div> </div>" ;
//
//                return htmlstr;
//            }
//            function inviteBtnClikcEvent(){
//                NoticeBox.create(inviteBoxHtml());
//                var closeBtn = $("#noticeBox .box_head .close");
//                closeBtn.bind('click', NoticeBox.remove);
//            }
//            var inviteBtn = $('#inviteNow');
//            inviteBtn.bind('click',inviteBtnClikcEvent);
//        }());

    });;
</script>
</html>