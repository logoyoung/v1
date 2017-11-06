<?php

header("Content-Type: text/html;charset=utf-8");
//include '../../../../include/init.php';
//$db = new DBHelperi_huanpeng();
include '../../includeAdmin/init.php';
$db = new DBHelperi_admin();

$tables = array(
    'admin_advertisement',//广告表
    'admin_app_break_report',//安卓日志表
    'admin_certRealName',//实名认证锁定表
//    'admin_information',//新闻资讯
    'admin_ip',//用户ip
    'admin_liveReview ',
    'admin_liveReviewReason',
    'admin_liveReviewResult',
    'admin_position',
    'admin_user_position',
    'anchor_most_popular',
    'livemsg',
    'liveuser',
    'push_notify_set',
    'videoconverted',
    'videosave_queue',
    'withdrawRecord',
    'admin_live_title',//直播标题审核表
    'admin_livebulletin',//公告审核表
    'admin_recommend_live',//待推荐主播表
    'admin_unpass_video',//未通过录像表
    'admin_user_nick',//昵称审核表
    'admin_user_pic',//头像审核表
    'admin_videomerge_failed',//合并失败录像表
    'admin_wait_live_title',//锁定中的审核直播标题表
    'admin_wait_pass_video',//录像审核（锁定表）
    'admin_wait_user_nick',//昵称审核（锁定表）
    'admin_wait_user_pic',  //头像审核（锁定表）
    'admin_wait_video_comment',//评论审核（锁定表）
    'anchor_blackList',//黑名单表
    'anchortag',//主播标签
    'billdetail',//送礼记录表
    'feedback',//反馈表
    'gamefollow',//游戏关注
    'giftrecord', // 免费礼物记录表
    'giftrecordcoin',//花钱礼物记录表
    'history',//历史记录
    'invite_record',//邀请记录表
    'invite_reward_record',//邀请奖励记录表
    'isupvideo',//是否点赞表
   'live',//直播表
   'liveStreamRecord',//直播流记录表
    'live_notice',//开播提醒表
    'livebulletin',//主播公告
    'liveroom',//直播间在线用户表
    'pickTreasure',//宝箱记录表
    'pickupHpbean',//欢豆记录表
    'rank_all',//总榜
    'rank_day',//日榜
    'rank_week',//周榜
    'recharge_order',//微信支付记录表
    'recommend_advertisement',//推荐的广告
//    'recommend_information',//推荐的资讯
    'recommend_live',//推荐的主播
    'report',//举报
    'reportLiveMsg',//举报弹幕
    'roommanager',//房管表
    'silencedlist',//用户禁言
    'task',//任务完成记录表
    'treasurebox',//宝箱表
    'userbankcard',//用户银行卡
    'userfollow',//用户关注
    'usermessage',//用户消息表
    'sysmessag',
    'dong_log',
    'log_for_chinaNet',
     ////'video',//录像表
    'video_download_record',
    'video_merge_record',
    'videocomment',//录像评论
    'videofollow',//录像收藏
    'three_side_user'

);

$need=array(
    'admin_check_mode',//审核模式表
    'admin_information_type',//资讯类型
    'admin_recommend_game', //游戏推荐
    'admin_user',// 管理员表
    'admin_user_right',//管理员权限表
    'anchor',//主播表
    'anchorlevel',//主播等级表
    'city',//城市
    'product_info',
    'province',//省份
    'game',// 游戏表
    'game_zone',//游戏辅助表
    'gametype',//游戏类型表
    'gift',//礼物表
    'pickupRule',//领豆规则表
    'taskinfo',//任务表
    'three_side_user',//三方账号绑定表
    'useractive',//用户动态表
    'userlevel',//用户等级表
    'userrealname',//实名认证表
    'userstatic'//用户静态表
);

//for ($i = 0, $k = count($tables); $i < $k; $i++) {
//    $sql = "truncate  table  $tables[$i]";
////$sql = "truncate  table  report";
//   $res = $db->query($sql);
//    if (true !== $res) {
//        echo $tables[$i] . '表,数据清空失败!'.PHP_EOL;
//    } else {
//        echo $tables[$i] . '表,数据清空完毕!'.PHP_EOL;
//    }
//}