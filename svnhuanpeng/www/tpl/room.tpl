<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <title></title>
    {config_load file="hpTPL.conf" section="setup"}
    {#commonMETASource#}
    {#commonCSSSource#}
    <link rel="stylesheet" href="{#hpCSS#}reset.css{#hpVersion#}">
    <link rel="stylesheet" href="{#hpCSS#}mCS_v4.css{#hpVersion#}">
    <link rel="stylesheet" href="{#hpCSS#}liveroom_v4.min.css{#hpVersion#}">
    <link rel="stylesheet" href="{#hpCSS#}pushLiveBox.css?v=1.0.5"/>
    <link rel="stylesheet" href="{#hpCSS#}loginModal.css{#hpVersion#}">
    <script type="text/javascript">
      var $head = {$header};
      var $ROOM = {$room};
      var pageUser = {$pageUser};
      document.title = $ROOM.liveTitle+'-欢朋直播－精彩手游直播平台';
    </script>
    <script async defer src="https://hm.baidu.com/hm.js?f97f114982484f9851e7c242cc1dac9b"></script>
    {#commonJSSource#}
    <script>
        if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i))  {
            location.href = '../mobile/room/room.html?luid='+$ROOM.anchorUserID;
        }
    </script>
    <script type="text/javascript" src="{#hpJS#}jquery.mCustomScrollbar.concat.min.js{#hpVersion#}" ></script>
    <script type="text/javascript" src="{#hpJS#}jquery.zclip.js{#hpVersion#}"></script>
    <script type="text/javascript" src="{#hpJS#}select.js{#hpVersion#}"></script>
    <script type="text/javascript" src="{#hpJS#}jquery.qrcode.min.js{#hpVersion#}"></script>
</head>
<body class="liveroom-body">
{include file="header.tpl"}
<div style="position:absolute;width: 1px;left: -1000px;overflow:hidden;bottom:0;">
    <div id="imProxy">
        <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" data="" type="">
            <param name="movie" value="../static/chatProxy.swf" />
            <embed src="../static/chatProxy.swf" type=""/>
        </object>
    </div>
</div>
<div style="position:absolute;width:100px;left: -1000px;overflow:hidden;bottom:0;">
    <div id="getVoiceDevice"></div>
</div>
<div id="room-hp" class="liveroom-container open-state">
    <!--直播间-左侧栏 START-->
    <div class="live-left">
        <div class="slidebar-list border-bottom">
            <a href="./LiveHall.php">
                <span class="roomIcon liveIcon"></span>
                <p class="slidebar-desc">直播</p>
            </a>
        </div>
        <div class="slidebar-list border-bottom game-kind">
            <a href="./game.php">
                <span class="roomIcon kindIcon"></span>
                <p class="slidebar-desc">分类</p>
            </a>
            <div class="game-list" id="game-list">
                <div class="gamelist-detail">

                </div>
                <a href="./game.php" class="moregame">更多游戏</a>
            </div>
        </div>
        <div class="slidebar-list border-bottom follow-list">
            <span class="roomIcon followIcon"></span>
            <p class="slidebar-desc">关注</p>
            <div class="follow-box">
                <!--没数据-->
                <div class="follow-nodata">
                        <span class="no-data-logo">
                            <img src="./static/img/home_no_login.png">
                        </span>
                    <div class="no-data-desc">
                        还没登录？点击
                        <div class="to_reg" onclick="loginFast.login(1)">注册</div>
                        <div class="line"></div>
                        <div class="to_log" onclick="loginFast.login(0)">登录</div>
                    </div>
                </div>

                <!--有数据-->
                <div class="follow-list">
                    <div class="follow-detail">
                        <div class="list-desc">当前关注的有<em class="perNum">0</em>个正在直播</div>
                        <div id="follow-content"></div>
                    </div>
                    <a href="./personal/follow/" class="viewAll">查看全部</a>
                </div>

                <!--loading-->
                <div class="follow-loading">
                    <img src="./static/img/load.gif">
                    <span>数据加载中......</span>
                </div>
            </div>
        </div>
        <div class="slidebar-list history-list">
            <span class="roomIcon historyIcon"></span>
            <p class="slidebar-desc">历史</p>
            <div class="history-box">

                <!--没数据-->
                <div class="history-nodata">
                        <span class="no-data-logo">
                            <img src="./static/img/home_no_login.png">
                        </span>
                    <div class="no-data-desc">
                        还没登录？点击
                        <div class="to-reg" onclick="loginFast.login(1)">注册</div>
                        <div class="line"></div>
                        <div class="to-log" onclick="loginFast.login(0)">登录</div>
                    </div>
                </div>

                <!--有数据-->
                <div class="hislist">

                </div>

                <!--loading-->
                <div class="history-loading">
                    <img src="./static/img/load.gif">
                    <span>数据加载中......</span>
                </div>
            </div>
        </div>
        <div class="slidebar-list user-help border-bottom">
            <a href="./help/helpReg.php">
                <span class="roomIcon helpIcon"></span>
                <p class="slidebar-desc">帮助</p>
            </a>
        </div>
        <div class="slidebar-list request-box">

        </div>
    </div>
    <!--直播间-左侧栏 END-->

    <!--直播间all - START-->
    <div class="live-allcontent">

        <!--直播间f 位置 START-->
        <div class="liveroom-container" id="liveroom-container">

            <!--直播间content START-->
            <div class="liveroom-content" id="liveroom-content">
                <div class="liveroom_nav">
                    <div class="live_nav_player">
                        {* 主播信息 *}
                        <div class="player_face">
                            <img src="./static/img/userface.png" class="mCS_img_loaded">
                        </div>
                        <div class="player_info">
                            <p class="player_gamedesc"></p>
                            <a class="anchor_report">
                                <span class="notice_word">举报房间</span>
                            </a>
                            <div class="clearfix"></div>
                            <p class="player_otherdesc">
                                   <span class="anchor_level">
                                       <span class="anchorLvl_icon"></span>
                                   </span>
                                <span class="anchor_name"></span>
                                <span class="anchor_icon gameName"></span>
                                <span class="anchor_gameName"></span>
                                <span class="anchor_icon viewerIcon2"></span>
                                <span class="viewer_count">
                                       <i>0</i>人观看
                                   </span>
                                <span class="anchor_income">
                                       <span class="anchor_icon bean"></span>
                                       <span class="income"></span>
                                   </span>
                            </p>
                        </div>
                    </div>
                    <div class="live_nav_opshow">
                        <div class="nav_attention">

                              <div class="followbtn nav_attention_right" id="follow_anchor">
                                  <span class="anchor_icon followIcon3"></span>关注
                              </div>
                              <div class="followbtn nav_attention_right followed none" id="follow_cancel">已关注</div>

                          <div class="nav_attention_left"></div>

                        </div>
                        <div class="clear"></div>
                            <div class="nav_shareopt">
                                <div class="sharegroup">
                                    <span class="anchor_icon share"></span>
                                    <span class="sharefont">分享</span>
                                    <span class="anchor_icon arrow_bt"></span>
                                    <div id="shareModal" class="sharemodal none">
                                        <div class="moreShare">
                                            <p class="title">分享直播至</p>
                                            <div class="shareBtn">
                                                <span class="share_icon sina-icon" data-cmd="tsina"></span>
                                                <span class="share_icon qq-icon" data-cmd="tqq"></span>
                                                <span class="share_icon qzone-icon" data-cmd="tqzone"></span>
                                                <span class="share_icon wx-icon" data-cmd="wx"></span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="modalBody">
                                            <div id="wx-share-qrcode" style="display: none;"></div>
                                            <input type="text" class="url_text" disabled="disabled">
                                            <input type="button" id="copyUrl" class="btn" value="复制链接">
                                        </div>
                                    </div>
                                </div>
                                <div class="sharephone">
                                    <span class="anchor_icon phone"></span>
                                    <span class="sharefont">手机观看</span>
                                    <span class="anchor_icon arrow_bt"></span>
                                    <div id="sharePhoneModal" class="none">
                                        <div id="qrCode"></div>
                                        <div class="sharephone_desc">
                                            <p>手机看直播，精彩不错过！</p>
                                            <span>扫描二维码，继续观看精彩直播～</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="liveroom_video">
                    <div class="aircraft_track"></div>
                    <div class="aircraft_track"></div>
                    <div class="aircraft_track"></div>
                    <div id="rtmpplayer_room">
                        <div id="install-flash">
                            <a target="_blank" rel="noopener" href="https://get.adobe.com/cn/flashplayer/">安装或启用FLASH播放器</a>
                        </div>
                    </div>
                    <div id="treasure_box_div"></div>
                </div>
                <!--直播用户选项-->
                <div class="liveroom_opt">
                    <div class="get_hpbean">
                      <span id="box_show" class="box_div" href="javascript:;" style="display: block; background-color:#fff;color: #fff;">
                      <span id="box" class="box_img">
                        <img src="../static/img/get_bean/not-login-1.png" alt=""/>
                      </span>
                            <span id="get_time" class="get_time">请稍候</span>
                            <div class="clear"></div>
                            <div id="receiveBean-validate"></div>
                        </span>

                        <div id="get_rem" class="get_hd to-none" style="left: -1000px;">
                            <p class="lw_notice"></p>
                            <ul class="lw_list">
                                <li>
                          <span>
                            <img src="../static/img/get_bean/waiting.png" alt=""/>
                          </span>
                                    <a href="javascript:;" class="waiting">等待中</a>
                                </li>
                                <li>
                          <span>
                            <img src="../static/img/get_bean/waiting.png" alt=""/>
                          </span>
                                    <a href="javascript:;" class="waiting">等待中</a>
                                </li>
                                <li>
                          <span>
                            <img src="../static/img/get_bean/waiting.png" alt=""/>
                          </span>
                                    <a href="javascript:;" class="waiting">等待中</a>
                                </li>
                                <li>
                          <span>
                            <img src="../static/img/get_bean/waiting.png" alt=""/>
                          </span>
                                    <a href="javascript:;" class="waiting">等待中</a>
                                </li>
                                <li>
                          <span>
                            <img src="../static/img/get_bean/waiting.png" alt=""/>
                          </span>
                                    <a href="javascript:;" class="waiting">等待中</a>
                                </li>

                                <div class="clear"></div>
                            </ul>
                            <p class="lw_app_des">
                                下载App领取欢朋豆
                                精彩手游直播，尽在欢朋！
                                <a href="download.php" class="lw_downLoad_btn">立即下载</a>
                            </p>
                            <div class="close_rem_box">
                                <span class="personal_icon"></span>
                            </div>
                        </div>
                    </div>
                        <div class="anchor_money" style="display:none;">

                          <div class="anchor_money_left">
                              <p class="money_title">我的财产：</p>
                              <div class="coin_list">
                                  <span class="anchor_icon hpcoin"></span>
                                  <span></span>
                              </div>
                              <div class="clear"></div>
                              <div class="bean_list">
                                  <span class="anchor_icon hpbean"></span>
                                  <span></span>
                              </div>
                          </div>
                          <div class="anchor_money_right">
                              <a href="#" target="_blank" rel="noopener">充值</a>
                          </div>
                      </div>

                    <div class="task task_box">
                        <span class="anchor_icon taskIcon"></span>任务
                    </div>
                    <div class="gift_box">
                        <ul class="gift">
                            <li data-giftid="35" data-gifttype="2" class="gift_item_5">
                                <div class="lw_item  airplane">
                                    <img src="./static/img/gift/gift-5.png" />

                                    <div class="shine"></div>
                                </div>
                                <div class="gift_item_hover airplane_hover">
                                    <div class="item_left">
                                        <img src="./static/img/gift/gift-5-big.gif" />
                                    </div>
                                    <div class="item_right">
                                        <p class="price">飞船(6000欢朋币)</p>

                                        <p class="contribution">贡献值 +6000 经验值 +6000</p>

                                        <p class="desc">一起去遨游太空吧</p>
                                    </div>
                                </div>
                            </li>
                            <li data-giftid="34" data-gifttype="2" class="gift_item_4">
                                <div class="lw_item  motorcycle">
                                    <img src="./static/img/gift/gift-4.png" />
                                    <div class="shine"></div>
                                </div>
                                <div class="gift_item_hover motorcycle_hover">
                                    <div class="item_left">
                                        <img src="./static/img/gift/gift-4-big.gif"/>
                                    </div>
                                    <div class="item_right">
                                        <p class="price">黄色小面包(1000欢朋币)</p>

                                        <p class="contribution">贡献值 +1000 经验值 +1000</p>

                                        <p class="desc">没时间解释了，快上车</p>
                                    </div>
                                </div>
                            </li>
                            <li data-giftid="33" data-gifttype="2" class="gift_item_3">
                                <div class="lw_item  diamond">
                                    <img src="./static/img/gift/gift-3.png"/>
                                    <div class="shine"></div>
                                </div>
                                <div class="gift_item_hover diamond_hover">
                                    <div class="item_left">
                                        <img src="./static/img/gift/gift-3-big.gif"/>
                                    </div>
                                    <div class="item_right">
                                        <p class="price">滑板(60欢朋币)</p>

                                        <p class="contribution">贡献值 +60 经验值 +60</p>

                                        <p class="desc">一步两步，一步两步</p>
                                    </div>
                                </div>
                            </li>
                            <li data-giftid="32" data-gifttype="2" class="gift_item_2">
                                <div class="lw_item like">
                                    <img src="./static/img/gift/gift-2.png"/>
                                    <div class="shine"></div>
                                </div>
                                <div class="gift_item_hover like_hover">
                                    <div class="item_left">
                                        <img src="./static/img/gift/gift-2-big.gif"/>
                                    </div>
                                    <div class="item_right">
                                        <p class="price">饮料(2欢朋币)</p>

                                        <p class="contribution">贡献值 +2 经验值 +2</p>

                                        <p class="desc">累了，困了，喝欢朋特饮</p>
                                    </div>
                                </div>
                            </li>
                            <li data-giftid="31" data-gifttype="1" class="gift_item_1">
                                <div class="lw_item hpbean">
                                    <img src="./static/img/gift/gift-1.png" alt="">
                                    <div class="shine"></div>
                                </div>
                                <div class="gift_item_hover hpbean_hover">
                                    <div class="item_left">
                                        <p class="price">欢豆</p>

                                        <p class="contribution">贡献值 ＋1 经验值 +1</p>

                                        <p class="desc">快给主播送豆子吧！</p>
                                    </div>
                                    <div class="item_right">
                                        <div class="numSetBtnGroup">
                                            <span>50</span>
                                            <span>100</span>
                                            <span>200</span>
                                            <span>520</span>
                                            <span>666</span>
                                            <span>888</span>
                                            <span>999</span>
                                            <span>1000</span>
                                            <span>1314</span>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--直播间其他信息-->
                <div class="liveroom_other">
                    <ul class="liveRoomother_tab">
                        <li class="selected">推荐直播</li>
                        <li>主播视频</li>
                        <li>主播公告</li>
                    </ul>
                    <div class="clear"></div>
                    <div class="tab_con videoList"></div>
                    <div class="tab_con videoList none"></div>
                    <div class="tab_con bulletin none"></div>
                    <div class="clear"></div>
                </div>
            </div>
            <!--直播间content END-->

            <!--直播间右侧栏 START-->
            <div class="liveroom-right">
                <div class="enlarge-block">
                    <div class="arrow_right"></div>
                </div>
                <div class="right_block_div">
                    <!--bananer-->
                    <div class="banner">
                        <a href="./activity/recruitAnchor/recruitAnchor.php" target="_blank">
                            <img src="../static/img/src/adv/recruitAnchor.png">
                        </a>
                        <button class="closeBanner">X</button>
                    </div>
                    <!--贡献榜-->
                    <div class="lr_contribution">
                        <div class="contribution_title">
                            <div class="title">
                                <span class="anchor_icon contribuIcon"></span>
                                <span>排行榜</span>
                            </div>
                            <ul class="lr_contribution_tab">
                                <li class="selected">日</li>
                                <li>周</li>
                                <li>总</li>
                            </ul>
                        </div>
                        <div class="border"></div>
                        <!--日贡献-->
                        <div class="tabCon orderList ">
                            <ul>
                                <li>
                                    <span class="orderIcon anchor_icon num_1"></span>
                                    <span>虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon anchor_icon num_2"></span>
                                    <span>虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon anchor_icon num_3"></span>
                                    <span>虚位以待</span>
                                </li>
                            </ul>
                        </div>
                        <!--周贡献-->
                        <div class="tabCon orderList none">
                            <ul>
                                <li>
                                    <span class="orderIcon anchor_icon num_1"></span>
                                    <span>虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon anchor_icon num_2"></span>
                                    <span>虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon anchor_icon num_3"></span>
                                    <span>虚位以待</span>
                                </li>
                            </ul>
                        </div>
                        <!--总贡献-->
                        <div class="tabCon orderList none">
                            <ul>
                              <li>
                                  <span class="orderIcon anchor_icon num_1"></span>
                                  <span>虚位以待</span>
                              </li>
                              <li>
                                  <span class="orderIcon anchor_icon num_2"></span>
                                  <span>虚位以待</span>
                              </li>
                              <li>
                                  <span class="orderIcon anchor_icon num_3"></span>
                                  <span>虚位以待</span>
                              </li>
                                {* <li>
                                    <span class="orderIcon anchor_icon num_1"></span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">6,666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon anchor_icon num_2"></span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">6,666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon anchor_icon num_3"></span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">6,666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon">4</span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon">5</span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon">6</span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon">7</span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon">8</span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon">9</span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li>
                                <li>
                                    <span class="orderIcon">10</span>
                                    <span class="icon icon_money anchor_icon hpcoin"></span>
                                    <span class="point">666</span>
                                    <span class="uNickname">虚位以待</span>
                                </li> *}
                            </ul>
                        </div>
                    </div>
                    <!--聊天-->
                    <div class="lr_online">
                        <div id="giftBetter_item1" class="giftBetter_item_container"></div>
                        <div id="giftBetter_item2" class="giftBetter_item_container"></div>
                        <div id="giftBetter_item3" class="giftBetter_item_container"></div>
                        <div id="giftBetter_item4" class="giftBetter_item_container"></div>
                        <div class="lr_chat hp_trans">
                            <ul id='lr_chat_ul' class="lr_chat_ul hp_trans"></ul>
                        </div>

                        <div class="chatopt">
                            <div class="opt_left">
                                <span class="anchor_icon emoji" id="emojiBtn" title="表情"></span>
                                <span class="anchor_icon clearMsg" title="清屏"></span>
                                <span class="anchor_icon blockScreen" title="锁屏"></span>

                            </div>
                            <div class="opt_right">
                                <div class="shieldDiv">
                                    <label for="shieldGift" class="checkbox-label">
                                        <input id="shieldGift" type="checkbox">
                                    </label>

                                    <p>屏蔽礼物</p>
                                </div>
                                <div id="setup" class="anchor_icon setIcon">
                                    <div class="opt-panel ">
                                        <div class="header">
                                            <span>选择屏蔽礼物展示</span>
                                        </div>
                                        <div class="body">
                                            <div id="shieldChatBanner" class="checkbox-div ">
                                                <label class="checkbox-label checked"></label>
                                                <span>屏蔽聊天横幅</span>
                                                <div class="clear"></div>
                                            </div>
                                            <div id="shieldPlayerScroll" class="checkbox-div ">
                                                <label class="checkbox-label checked"></label>
                                                <span>屏蔽播放器礼物滚动</span>
                                                <div class="clear"></div>
                                            </div>
                                            <div id="shieldGiftNotice" class="checkbox-div ">
                                                <label class="checkbox-label checked"></label>
                                                <span>屏蔽聊天框消息通知</span>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lr_inwrite">
                            <div class="lr_incon"></div>
                            <a class="lr_insend">
                                <span>发送</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!--直播间右侧栏 END-->

        </div>
        <!--直播间f 位置 END-->

    </div>
    <!--直播间all - END-->
</div>
<script type="text/javascript" src="{#hpJS#}head_v4.js{#hpVersion#}"></script>
<script type="text/javascript" src="{#hpJS#}room_v4.js{#hpVersion#}"></script>
<div class="hp-tools">
  {include file="loginModal.tpl"}
  {include file="report.tpl"}
  {include file="webChat.tpl"}
</div>
</body>
</html>
