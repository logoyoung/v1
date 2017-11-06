<?php
define('DEBUG', true);
define('WEBSITE_ROOT', '/usr/local/huanpeng/');
define('INCLUDE_DIR', WEBSITE_ROOT . 'include/');
define('LOG_DIR', '/data/logs/');
define('IMAGE_DIR', '/data/huanpeng-img/');
define('VIDEO_TMP', '/data/tmp/');//录像临时文件

// 域名
define('DOMAINNAME_PRO', 'www.huanpeng.com');
define('DOMAINNAME_PRO_IMG', 'img.huanpeng.com');
define('DOMAINNAME_DEV', 'dev.huanpeng.com');
define('DOMAINNAME_DEV_IMG', 'dev-img.huanpeng.com');

define('WEBSITE_TPL', WEBSITE_ROOT . 'tpl/');
define('WEBSITE_MAIN', WEBSITE_ROOT . 'htdocs/');
define('WEBSITE_PERSON', WEBSITE_MAIN . "personal/");

// 日志文件
define('LOGFN_GENERAL', LOG_DIR . 'huanpeng_general.log');
define('LOGFN_DBH_DBUG', LOG_DIR . 'huanpeng_debug_dbh.log');
define('LOGFN_VIDEO_SAVE_SUC', LOG_DIR . 'huanpeng_video_save_success.log');
define('LOGFN_VIDEO_SAVE_ERR', LOG_DIR . 'huanpeng_video_save_error.log');
define('LOGFN_REQUEST_ERR', LOG_DIR . 'huanpeng_request.error.log');
define('LOGFN_VIDEO_MERGE_ERR', LOG_DIR . 'video_merge.error.log');
define('LOGFN_SEND_MSG_ERR', LOG_DIR."huanpeng_sendMsg.log");
define('LOGFN_WX_PAY', LOG_DIR."huanpeng_wx_pay.log");



//直播状态 0:直播创建 1:未获得直播流名称  2：非正常结束 3：超时
//100：正在直播   101：直播结束  102:录像保存中  103:生成录像
define('LIVE_CREATE', 0);
define('LIVE_CREATE_NOSTREAM', 1);
define('LIVE_ABNOMAL_END', 2);
define('LIVE_TIMEOUT', 3);

define('LIVE_CLIENT_CREATE', 50);


define('LIVE', 100);
define('LIVE_STOP', 101);
define('LIVE_SAVING', 102);
define('LIVE_VIDEO', 103);

//保存录像调用方式 0:主动保存  1:超时保存  
define('VIDEO_SAVETYPE_CALL', 0);
define('VIDEO_SAVETYPE_TIMEOUT', 1);

//录像状态 0:待发布 1:审核中 2:已发布 3:审核未通过 100:录像已被删除 

define('VIDEO_WAIT', 0);
define('VIDEO_UNPUBLISH', 1);
define('VIDEO', 2);
define('VIDEO_UNPASS', 3);
define('VIDEO_DEL', 100);

//未发布录像过期时间[单位:天]
define('VIDEO_TIMEOUT', 15);
// 直播室
define('LIVEROOM_ANONYMOUS', 3000000000);

//首页直播推荐数量
define("LIVE_RECOMMENT_NUMBER", 6);
//邮箱认证状态 0:未填写, 1:填写未认证, 2:认证通过,
define('EMAIL_NOT', 0);
define('EMAIL_UNPASS', 1);
define('EMAIL_PASS', 2);

//邮箱认证过期时间
define('EMAIL_CERT_OUTTIME', 86400);

//实名认证状态 1:待审核, 100:审核未通过, 101:审核通过
define("RN_NOT", 0);
define('RN_WAIT', 1);
define('RN_UNPASS', 100);
define('RN_PASS', 101);

//银行卡认证状态 1:审核中, 100:审核未通过, 101:审核通过
define('BANK_CERT_NOT', 0);
define('BANK_CERT_WAIT', 1);
define('BANK_CERT_UNPASS', 100);
define("BANK_CERT_PASS", 101);


//账单纪录状态 0:送礼以及主播的收入, 1:充值, 2:提现, 3:主播兑换
define('BILL_GIFT', 0);
define('BILL_RECHARGE', 1);
define('BILL_CASH', 2);
define('BILL_EXCHANGE', 3);
define('BILL_CASH_COIN', 2);
define('BILL_CASH_BEAN', 3);

//任务状态
define("TASK_STAT_ONLINE", 1);
define("TASK_STAT_STOP", 0);
//任务纪录状态	0:未完成 , 1:已经完成, 2:已经领取
define("TASK_UNFINISH", 0);
define("TASK_FINISHED", 1);
define("TASK_BEAN_RECEIVED", 2);

//推送房间ID
define("PUSH_ROOM_ID", 1);
define("LIVE_START_NOTICE_RECEIVE", 1);
define("LIVE_START_NOTICE_NOT", 0);

//审核模式
define("CHECK_HEAD", 1);  //头像审核
define("CHECK_NICK", 2);//昵称审核
define("CHECK_TITLE", 3);//直播标题审核
define("CHECK_COMMENT", 4);//评论审核
define("CHECK_BARRAGE", 5);//／弹幕审核
define("CHECK_NOTICE", 6);//／主播公告审核

//头像审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define("USER_PIC_WAIT", 0);
define("USER_PIC_PASS", 1);
define("USER_PIC_UNPASS",2);
define("USER_PIC_AUTO_PASS", 3);
define("USER_PIC_AUTO_UNPASS",4);

//昵称审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define('USER_NICK_WAIT', 0);
define('USER_NICK_PASS', 1);
define('USER_NICK_UNPASS',2);
define('USER_NICK_AUTO_PASS', 3);
define('USER_NICK_AUTO_UNPASS',4);

//直播标题审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define('LIVE_TITLE_WAIT', 0);
define('LIVE_TITLE_PASS', 1);
define('LIVE_TITLE_UNPASS',2);
define('LIVE_TITLE_AUTO_PASS', 3);
define('LIVE_TITLE_AUTO_UNPASS',4);

//评论审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define('COMMENT_WAIT', 0);
define('COMMENT_PASS', 1);
define('COMMENT_UNPASS',2);
define('COMMENT_AUTO_PASS', 3);
define('COMMENT_AUTO_UNPASS',4);
//游戏类
define('OTHER_GAME', 401);



//apple push
define('APPLE_PUSH_PRO',50);

define('RECHARGE_ORDER_FINISH',1);

date_default_timezone_set('Asia/Shanghai');

//协议
define('PROTOCOL', 'http://');
require_once(INCLUDE_DIR . 'functions.php');
require_once(INCLUDE_DIR . 'commonFunction.php');
require_once(INCLUDE_DIR . 'DBHelperi_huanpeng.class.php');
/* require_once(INCLUDE_DIR.'Upload.class.php'); */

// 运行环境
if (isset($_SERVER["HTTP_HOST"]) and $_SERVER["HTTP_HOST"] == DOMAINNAME_DEV)
    $GLOBALS['env'] = 'DEV';
elseif (isset($argv[1]) and $argv[1] == 'DEV')
    $GLOBALS['env'] = 'DEV';
else
    $GLOBALS['env'] = 'PRO';

// 环境相关配置
$GLOBALS['env-def'] = array(
    'DEV' => array(
        'domain' => DOMAINNAME_DEV,
        'domain-img' => DOMAINNAME_DEV_IMG,
        'domain-video' => PROTOCOL.DOMAINNAME_DEV_IMG.'/v',
        'img-dir' => IMAGE_DIR,
        'video-dir' => IMAGE_DIR.'v',

//        'stream-pub' => '222.35.74.221:8080/liverecord',
//        'stream-watch' => '222.35.74.221:8080/liverecord',
//        'stream-stop-notify' => 'http://222.35.74.221:9300/r?s=',
//        'hls' => '222.35.74.221',

        'stream-pub' => '222.35.75.72:8080/liverecord',
		//'223.203.212.30:8080/liverecord',

        'stream-watch' => '222.35.75.72:4000/liverepeater',
		//'223.203.212.30:8080/liverecord',
        //'223.203.212.30:4000/liverepeater',

        'stream-stop-notify' => 'http://222.35.75.72:9300/r?s=',
		//'http://223.203.212.30:9300/r?s=',
        'hls' => '222.35.75.72',
		//'223.203.212.30',
        'hls-server' => '223.203.212.30/liverepeater',
        'socket' => array('42.62.27.112:8082'),
    ),
    'PRO' => array(
        'domain' => DOMAINNAME_PRO,
        'domain-img' => DOMAINNAME_PRO_IMG,
        'domain-video' => PROTOCOL.DOMAINNAME_DEV_IMG.'/v',
        'img-dir' => '/leofs/i/huanpeng',
        'video-dir' => '/leofs/v/huanpeng',
        'stream-pub' => '222.35.74.221:8080/liverecord',
        'stream-watch' => '222.35.74.221:8080/liverecord',
        'stream-stop-notify' => 'http://222.35.74.221:9300/r?s=',
        'hls' => '222.35.74.221',
        'socket' => array('42.62.27.123:8082', '42.62.27.124:8082'),
    ),
);

define("WEB_ROOT_URL", PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . "/");
define("WEB_PERSONAL_URL", WEB_ROOT_URL . 'personal/');
define("WEB_MEDIA_URL", WEB_ROOT_URL."static/");
define("__JS__", WEB_MEDIA_URL."js/");
define("__CSS__", WEB_MEDIA_URL."css/");
define("__IMG__", WEB_MEDIA_URL."img/");


//room
define("ROOM_SILENCE_TIMEOUT", 3600);
//直播间地址
define("LIVE_ROOM_URL", PROTOCOL.DOMAINNAME_DEV.'/room.php?luid=');
//直播大厅
define("LIVE_HALL", PROTOCOL.DOMAINNAME_DEV.'/LiveHall.php');

//用户相关
define("DEFAULT_PIC", PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/static/img/userface.png'); //默认头像
define("VERTICAL", PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/static/img/vertical_screen.jpg'); //竖屏默认图
define("CROSS", PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/static/img/src/default/260x150.png'); //横屏默认图
define('MODIFY_NICK_COST', 600); //修改昵称所需的金额
//	define('DEFAULT_USER_FACE', 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domian'] .'/main/static/img/userface_notlogin.png');
//相关秘钥
define('VIDEO_CONV_SECRET_KEY', 'FTwqs%GhFSuWF6iK@TlVsb2#JIgLrn2%');
define('SECRET_KEY', '@#dM&Q9%wIq1qE*y4RJKGY51CsR*tGpI'); //三方登陆
define('TIMEOUT_STOPLIVE_KEY', '#1dsxg*JRSXRSPI@8831');
define('CERT_EMAIL_KEY', 'HGD@*x#!)toTHeMszxAD');
define('MSG_ADMIN', "hehehe$81)_(*");
define('RECHARGE_SECRET','@wxpay2016$x_!^()^!_@@@&&&!!!');

define('TREASURE_TIME_OUT', 90);
//验证码图片大小
define('WIDTH', 80);
define('HEIGHT', 35);


//geetest id and key
define('CAPTCHA_ID','3bd401b74723d738f969c3706d1d8e3d');
define('PRIVATE_KEY', '370cf653366e1a5d5056a75fea62899c');
define('CAPTCHA_APP_ID','d44caaa5beddd4ae059d88ef317ad251');
define("PRIVATE_APP_KEY",'f1b9caa7738739f2f24fdb9ae676d83f');

?>