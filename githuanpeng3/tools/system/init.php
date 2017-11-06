<?php
define( 'DEBUG', true );
define( 'SHOREW_DEBUG', false );
define( 'WEBSITE_ROOT', 'dev_user_code_dir/' );
define( 'INCLUDE_DIR', WEBSITE_ROOT . 'include/' );
define( 'LOG_DIR', '/data/logs/dev_user_name/' );
define( 'IMAGE_DIR', '/data/huanpeng-img/' );
define( 'VIDEO_TMP', '/data/tmp/' );//录像临时文件
//配置文件目录
define('CONFIG_DIR', INCLUDE_DIR.'config/');

// 域名
define( 'DOMAINNAME_PRO', 'www.huanpeng.com' );
define( 'DOMAINNAME_PRO_IMG', 'img.huanpeng.com' );
define( "DOMAINNAME_PRE", 'pre.huanpeng.com' );
define( "DOMAINNAME_PRE_IMG", 'pre-img.huanpeng.com' );
define( 'DOMAINNAME_DEV', 'dev_user_name.huanpeng.com' );
define( 'DOMAINNAME_DEV_IMG', 'dev-img.huanpeng.com' );

//域名协议
if( ( isset( $_SERVER['HTTPS'] ) && strtoupper( $_SERVER['HTTPS'] ) == 'ON' )
    || ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && strtoupper( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) == 'HTTPS' )
    || ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443 )
)
{
    define( 'DOMAIN_PROTOCOL', 'https://' );
}
else
{
    define( 'DOMAIN_PROTOCOL', 'http://' );
}

//白名单
define( "WHITE_LIST", '1815,2220,2235,2240,2250,2300,2600' );//,90,365,370
define( 'IOS_TEST_USER_LIST', '2600' );

// 日志文件
define( 'LOGFN_GENERAL', LOG_DIR . 'huanpeng_general.log' );
define( 'LOGFN_DBH_DBUG', LOG_DIR . 'huanpeng_debug_dbh.log' );
define( 'LOGFN_VIDEO_SAVE_SUC', LOG_DIR . 'huanpeng_video_save_success.log' );
define( 'LOGFN_VIDEO_SAVE_ERR', LOG_DIR . 'huanpeng_video_save_error.log' );
define( 'LOGFN_REQUEST_ERR', LOG_DIR . 'huanpeng_request.error.log' );
define( 'LOGFN_VIDEO_MERGE_ERR', LOG_DIR . 'video_merge.error.log' );
define( 'LOGFN_SEND_MSG_ERR', LOG_DIR . "huanpeng_sendMsg.log" );
define( 'LOGFN_WX_PAY', LOG_DIR . "huanpeng_wx_pay.log" );
define( 'LOGFN_SENDGIFT_LOG', LOG_DIR . 'huanpeng_send_gift.log' );
define( 'LOGFN_IOS_PUSH_LOG', LOG_DIR . 'huanpeng_ios_push.log' );


//直播状态 0:直播创建 1:未获得直播流名称  2：非正常结束 3：超时
//100：正在直播   101：直播结束  102:录像保存中  103:生成录像
/*define( 'LIVE_CREATE', 0 );
define( 'LIVE_CREATE_NOSTREAM', 1 );
define( 'LIVE_ABNOMAL_END', 2 );
define( 'LIVE_TIMEOUT', 3 );

define( 'LIVE_CLIENT_CREATE', 50 );


define( 'LIVE', 100 );
define( 'LIVE_STOP', 101 );
define( 'LIVE_SAVING', 102 );
define( 'LIVE_VIDEO', 103 );*/

/***********直播、直播流、超时**************/

define('LIVE_INDEX',80000);

define( 'LIVE_CLIENT_CREATE', 0 );
/**直播状态**/
//直播创建
define( 'LIVE_CREATE', 0 );
//直播超时
define( 'LIVE_TIMEOUT', 230 );
//直播开始
define( 'LIVE', 100 );
//直播停止
define( 'LIVE_STOP', 110 );
//直播flv片段生成
define( 'LIVE_TO_FLV', 120 );
//录像合并完成
define( 'FLV_TO_VIDEO', 130 );
//录像截图完成
define( 'VIDEO_TO_POSTER', 140 );
//直播活动完成
define( 'LIVE_COMPLETE', 200 );
//合并录像失败
define( 'VIDEO_MERGE_FAILED', 210 );
//录像截图失败
define( 'VIDEO_TO_POSTER_FAILED', 220 );

/*****直播流状态*****/
//直播流创建
define( 'STREAM_CREATE', 0 );
//直播流开始
define( 'STREAM_START', 100 );
//直播流中断
define( 'STREAM_DISCONNECT', 200 );
//直播流超时
define( 'STREAM_TIMEOUT', 210 );
//直播流被覆盖
define( 'STREAM_COVER', 220 );
//直播流被管理员停止
define( 'STREAM_ADMIN_STOP', 230 );
//直播流被用户停止
define( 'STREAM_USER_STOP', 240 );

/***超时时间***/
/*//直播流开始回调超时
define( 'STREAM_START_TIMEOUT', 3600 );
//直播流中断超时
define( 'STREAM_DISCONNECT_TIMEOUT', 600 );
//直播生成FLV超时
define( 'LIVE_TO_FLV_TIMEOUT', 3600 );
//FLV合并录像超时
define( 'FLV_TO_VIDEO_TIMEOUT', 3600 * 6 );
//录像截图超时
define( 'VIDEO_TO_POSTER_TIMEOUT', 600 );*/

/******直播流操作来源********/
//创建操作
define( 'STREAM_CREATE_REF', 0 );
//回调操作
define( 'STREAM_START_REF', 100 );
//用户断流
define( 'STREAM_DISCONNECT_USER', 240 );
//CDN断流
define( 'STREAN_DISCONNECT_CDN', 200 );

/*******************end******************/


//保存录像调用方式 0:主动保存  1:超时保存
define( 'VIDEO_SAVETYPE_CALL', 0 );
define( 'VIDEO_SAVETYPE_TIMEOUT', 1 );
define( "VIDEO_SAVETYPE_IDLETIME", 2 );

//录像状态 0:待发布 1:审核中 2:已发布 3:审核未通过 100:录像已被删除

define( 'VIDEO_WAIT', 0 );
define( 'VIDEO_UNPUBLISH', 1 );
define( 'VIDEO', 2 );
define( 'VIDEO_UNPASS', 3 );
define( 'VIDEO_DEL', 100 );

//未发布录像过期时间[单位:天]
define( 'VIDEO_TIMEOUT', 15 );
// 直播室
define( 'LIVEROOM_ANONYMOUS', 3000000000 );
//首页直播推荐数量
define( "LIVE_RECOMMENT_NUMBER", 6 );
//邮箱认证状态 0:未填写, 1:填写未认证, 2:认证通过,
define( 'EMAIL_NOT', 0 );
define( 'EMAIL_UNPASS', 1 );
define( 'EMAIL_PASS', 2 );

//邮箱认证过期时间
define( 'EMAIL_CERT_OUTTIME', 86400 );

//实名认证状态 1:待审核, 100:审核未通过, 101:审核通过
define( "RN_MODEL", true );//实名模式 true表示需要上传证件 flase表示绑定手机即可直播
define( "RN_NOT", 0 );
define( 'RN_WAIT', 1 );
define( 'RN_UNPASS', 100 );
define( 'RN_PASS', 101 );

//银行卡认证状态 1:审核中, 100:审核未通过, 101:审核通过
define( 'BANK_CERT_NOT', 0 );
define( 'BANK_CERT_WAIT', 1 );
define( 'BANK_CERT_UNPASS', 100 );
define( "BANK_CERT_PASS", 101 );


//账单纪录状态 0:送礼以及主播的收入, 1:充值, 2:提现, 3:主播兑换
define( 'BILL_GIFT', 0 );
define( 'BILL_RECHARGE', 1 );
define( 'BILL_CASH', 2 );
define( 'BILL_EXCHANGE', 3 );
define( 'BILL_CASH_COIN', 2 );//弃用
define( 'BILL_CASH_BEAN', 3 );//弃用


define( 'BILL_BACKSTAGE_RECHARGE', 4 );
define( 'BILL_TASK_REWARD', 5 );


define( 'TASK_REWARD_FENGCE', 1 );
//define()
define( 'BASE_SALARY', 600 );//主播底薪
//任务状态
define( "TASK_STAT_ONLINE", 1 );
define( "TASK_STAT_STOP", 0 );
//任务纪录状态    0:未完成 , 1:已经完成, 2:已经领取
define( "TASK_UNFINISH", 0 );
define( "TASK_FINISHED", 1 );
define( "TASK_BEAN_RECEIVED", 2 );

//推送房间ID
define( "PUSH_ROOM_ID", 1 );
define( "LIVE_START_NOTICE_RECEIVE", 1 );
define( "LIVE_START_NOTICE_NOT", 0 );

//审核模式
define( "CHECK_HEAD", 1 );  //头像审核
define( "CHECK_NICK", 2 );//昵称审核
define( "CHECK_TITLE", 3 );//直播标题审核
define( "CHECK_COMMENT", 4 );//评论审核
define( "CHECK_BARRAGE", 5 );//／弹幕审核
define( "CHECK_NOTICE", 6 );//／主播公告审核

//头像审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define( "USER_PIC_WAIT", 0 );
define( "USER_PIC_PASS", 1 );
define( "USER_PIC_UNPASS", 2 );
define( "USER_PIC_AUTO_PASS", 3 );
define( "USER_PIC_AUTO_UNPASS", 4 );

//昵称审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define( 'USER_NICK_WAIT', 0 );
define( 'USER_NICK_PASS', 1 );
define( 'USER_NICK_UNPASS', 2 );
define( 'USER_NICK_AUTO_PASS', 3 );
define( 'USER_NICK_AUTO_UNPASS', 4 );

//直播标题审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define( 'LIVE_TITLE_WAIT', 0 );
define( 'LIVE_TITLE_PASS', 1 );
define( 'LIVE_TITLE_UNPASS', 2 );
define( 'LIVE_TITLE_AUTO_PASS', 3 );
define( 'LIVE_TITLE_AUTO_UNPASS', 4 );

//评论审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define( 'COMMENT_WAIT', 0 );
define( 'COMMENT_PASS', 1 );
define( 'COMMENT_UNPASS', 2 );
define( 'COMMENT_AUTO_PASS', 3 );
define( 'COMMENT_AUTO_UNPASS', 4 );
//游戏类
define( 'OTHER_GAME', 401 );

//room
define( "ROOM_SILENCE_TIMEOUT", 3600 );


define( 'RECHARGE_ORDER_FINISH', 1 );

//相关秘钥
define( 'VIDEO_CONV_SECRET_KEY', 'FTwqs%GhFSuWF6iK@TlVsb2#JIgLrn2%' );
define( 'SECRET_KEY', '@#dM&Q9%wIq1qE*y4RJKGY51CsR*tGpI' ); //三方登陆
define( 'TIMEOUT_STOPLIVE_KEY', '#1dsxg*JRSXRSPI@8831' );
define( 'CERT_EMAIL_KEY', 'HGD@*x#!)toTHeMszxAD' );
define( 'MSG_ADMIN', "hehehe$81)_(*" );
define( 'RECHARGE_SECRET', '@wxpay2016$x_!^()^!_@@@&&&!!!' );
define( 'STREAM_SECRET', 'J$wd6lMtsE*PZuhP3E5!29SRly*!0MD2' );//推流鉴权
define( 'STREAM_CALLBACK_SECRET', 'OH3CuAU&p$uL8NG$N*6!^f#jg7cqP&H1' );//推流｜断流

//极验验证码相关密钥
define( 'CAPTCHA_ID', 'e9c2936e91640bceb513ed80b5f4a5ee' );
define( 'PRIVATE_KEY', '23ff6f6e0b4e89440c3a9c7cb2d8a1b4' );
define( 'CAPTCHA_APP_ID', 'e28cd83f7c30a629b42474dbdff03b45' );
define( "PRIVATE_APP_KEY", 'bfe8ae01e569e3a971e10aeeb5a915f5' );
define("DOTA_AUTHORIZE_KEY", "DaXiaoXie!@#$88765_");

//网宿防盗链密钥与过期时间
define( 'WS_SECURITY_CHAIN', '1234!@#$abcdef' );
define( 'WS_EXPIRED', 2 * 60 * 60 );

//兑换比率
define( 'CONVERSION_RATIO', 10 / 1 );//主播欢币兑换用户欢币比率1:10

define( 'BASE_RATE', 50 ); //普通主播默认比率
define( 'OFFICIAL_RATE', 50 ); //平台签约主播主播默认比率
define( 'OTHER_RATE', 70 ); //经纪公司，工会默认比率


define( 'MODIFY_NICK_COST', 600 ); //修改昵称所需的金额

//验证码图片大小
define( 'WIDTH', 80 );
define( 'HEIGHT', 35 );

define( 'TREASURE_TIME_OUT', 90 );


define( 'MAX_UPLOAD_SIZE', 2 );

//直播开始推送状态： 0：创建，1:执行， 2：完成
define( "LIVE_PUSH_CREATE", 0 );
define( "LIVE_PUSH_RUNING", 1 );
define( "LIVE_PUSH_FINISH", 2 );
define( 'IS_CLI', ( PHP_SAPI == 'cli' ? true : false ) );

date_default_timezone_set( 'Asia/Shanghai' );

//require_once(INCLUDE_DIR . 'bussiness_flow.fun.php');
/* require_once(INCLUDE_DIR.'Upload.class.php'); */

// 运行环境
//if (isset($_SERVER["HTTP_HOST"]) and $_SERVER["HTTP_HOST"] == DOMAINNAME_DEV)
//    $GLOBALS['env'] = 'DEV';
//elseif (isset($argv[1]) and $argv[1] == 'DEV')
//    $GLOBALS['env'] = 'DEV';
//else
//    $GLOBALS['env'] = 'PRO';

if (!function_exists('get_current_env')) {
    function get_current_env()
    {
        $hostname = gethostname();
    //  echo $hostname."\n";
        $rule = [
            "PRO" => "/^HangpengW/",
            'PRE' => "/^huanp-node-[3-4].novalocal/",
            "DEV" => "/^huanp-node-1.novalocal/"
        ];

        $cur_env = "PRO";

        foreach ( $rule as $env => $pattern)
        {
            if(preg_match($pattern, $hostname))
            {
                $cur_env = $env;
            }
        }

        return $cur_env;
    }
}

$GLOBALS['env'] = get_current_env();


//
if($_SERVER['HTTP_HOST'] == "www.huanpeng.com" && $GLOBALS['env'] != "PRO")
{
    define("THREE_SIDE_LOGIN_DEBUG",true);
}
else
{
    define("THREE_SIDE_LOGIN_DEBUG",false);
}

// 环境相关配置
$GLOBALS['env-def'] = array(
    'DEV' => array(
        'domain'     => THREE_SIDE_LOGIN_DEBUG ? DOMAINNAME_PRO : DOMAINNAME_DEV,
        'domain-img' => DOMAINNAME_DEV_IMG,
//        'domain-video' => DOMAIN_PROTOCOL.'dev-img.huanpeng.com/v',
        'huan-video' => DOMAIN_PROTOCOL . 'img.huanpeng.com/v',
//        'domain-vposter'=>DOMAIN_PROTOCOL.'dev-img.huanpeng.com/v',

        'domain-video'       => DOMAIN_PROTOCOL . 'fvod.huanpeng.com',
        'domain-vposter'     => DOMAIN_PROTOCOL . 'fvod.huanpeng.com',
        'domain-lposter'     => DOMAIN_PROTOCOL . 'screens.huanpeng.com/dev-urtmp.huanpeng.com',
        'domain-wsapi'       => '122.70.146.49:9091',
        'img-dir'            => '/data/huanpeng-img',
        'video-dir'          => '/data/huanpeng-img/v',

        //'stream-pub' => '222.35.75.72:8080/liverecord',
        //'stream-watch' => '222.35.75.72:4000/liverepeater',
        'stream-pub'         => 'dev-urtmp.huanpeng.com/liverecord',
        'stream-watch'       => 'dev-drtmp.huanpeng.com/liverecord',
        'stream-stop-notify' => 'http://222.35.75.72:9300/r?s=',
        //'http://223.203.212.30:9300/r?s=',
        'hls'                => '222.35.75.72',
        //'223.203.212.30',
        'hls-server'         => '223.203.212.30/liverepeater',
        'socket'             => array( '122.70.146.49:8082' ),
        'web-socket'=> array( '122.70.146.49:4000' ),
        'cookiePath'         => '/'
    ),
    'PRE' => array(
        'domain'     => THREE_SIDE_LOGIN_DEBUG ? DOMAINNAME_PRO : DOMAINNAME_PRE,
        'domain-img'         => DOMAINNAME_PRE_IMG,
        'huan-video'         => DOMAIN_PROTOCOL . 'pre-img.huanpeng.com/v',
        'domain-video'       => DOMAIN_PROTOCOL . 'fvod.huanpeng.com',
        'domain-vposter'     => DOMAIN_PROTOCOL . 'fvod.huanpeng.com',
        'domain-lposter'     => DOMAIN_PROTOCOL . 'screens.huanpeng.com/pre-urtmp.huanpeng.com',
        'domain-wsapi'       => '122.70.146.50:9091',
        'img-dir'            => '/data/huanpeng-img',
        'video-dir'          => '/data/huanpeng-img/v',
        'stream-pub'         => 'pre-urtmp.huanpeng.com/liverecord',
        'stream-watch'       => 'pre-drtmp.huanpeng.com/liverecord',
        'stream-stop-notify' => 'http://222.35.74.221:9300/r?s=',
        'hls'                => '222.35.74.221',
        'socket'             => array( '122.70.146.50:8082' ),
        'web-socket'             => array( '122.70.146.50:4000' ),
        'cookiePath'         => '/'
    ),
    'PRO' => array(
        'domain'             => DOMAINNAME_PRO,
        'domain-img'         => DOMAINNAME_PRO_IMG,
        'huan-video'         => DOMAIN_PROTOCOL . 'img.huanpeng.com/v',
        'domain-video'       => DOMAIN_PROTOCOL . 'fvod.huanpeng.com',
        'domain-vposter'     => DOMAIN_PROTOCOL . 'fvod.huanpeng.com',
        'domain-lposter'     => DOMAIN_PROTOCOL . 'screens.huanpeng.com/urtmp.huanpeng.com',
        'domain-wsapi'       => DOMAINNAME_PRO . '/api/server',
        'img-dir'            => '/leofs/i',
        'video-dir'          => '/leofs/v',
//        'stream-pub' => '222.35.74.221:8080/liverecord',
//        'stream-watch' => '222.35.74.221:8080/liverecord',
        'stream-pub'         => 'urtmp.huanpeng.com/liverecord',
        'stream-watch'       => 'drtmp.huanpeng.com/liverecord',
        'stream-stop-notify' => 'http://222.35.74.221:9300/r?s=',
        'hls'                => '222.35.74.221',
        'socket'             => array( '42.62.27.123:8082', '42.62.27.124:8082' ),
        'web-socket'             => array( '42.62.27.123:4000', '42.62.27.124:4000' ),
        'cookiePath'         => '/'
    )
);

//define('WEBSITE_TPL', WEBSITE_ROOT . 'tpl/');
//define('WEBSITE_MAIN', WEBSITE_ROOT . 'htdocs/main/');
//define('WEBSITE_PERSON', WEBSITE_MAIN . "personal/");


if( $GLOBALS['env'] == "DEV" )
{
    //define("WEBSITE_MAIN",WEBSITE_ROOT."htdocs/main/");
    define( "WEBSITE_MAIN", WEBSITE_ROOT . "htdocs/" );
}
else
{
    define( "WEBSITE_MAIN", WEBSITE_ROOT . "htdocs/" );
}
define( 'WEBSITE_TPL', WEBSITE_MAIN . "tpl/" );
define( "WEBSITE_PERSON", WEBSITE_MAIN . 'personal/' );


define( "WEB_ROOT_URL",'dev_user_domain');
if( THREE_SIDE_LOGIN_DEBUG )
{
    define("WEB_ROOT_URL", DOMAIN_PROTOCOL.DOMAINNAME_PRO."/");
}

if($GLOBALS['env'] != "PRO")
{
    define("PAY_API_NOTIFY_URL", DOMAIN_PROTOCOL.strtolower($GLOBALS['env'])."-pay.huanpeng.com/");
}
else
{
    define("PAY_API_NOTIFY_URL", WEB_ROOT_URL."api/");
}


if($GLOBALS['env'] !="PRO")
{
    define("ADMIN_HOST_URL",'admin-'.strtolower($GLOBALS['env']).".huanpeng.com/");
}else
{
    define("ADMIN_HOST_URL","admin.huanpeng.com/");
}
//if( $GLOBALS['env'] == "DEV" )
//{
//  define('WEB_ROOT_URL',DOMAIN_PROTOCOL.DOMAINNAME_DEV."/");
//}
//elseif( $GLOBALS['env'] == "PRE" )
//{
//  define("WEB_ROOT_URL",DOMAIN_PROTOCOL.DOMAINNAME_PRE."/");
//}
//else
//{
//  define("WEB_ROOT_URL",DOMAIN_PROTOCOL.DOMAINNAME_PRO."/");
//}

define( "WEB_PERSONAL_URL", WEB_ROOT_URL . 'personal/' );
define( "WEB_MEDIA_URL", WEB_ROOT_URL . "static/" );
define( "__JS__", WEB_MEDIA_URL . "js/" );
define( "__CSS__", WEB_MEDIA_URL . "css/" );
define( "__IMG__", WEB_MEDIA_URL . "img/" );

define( "STATIC_JS_PATH", WEB_MEDIA_URL . "js/" );
define( "STATIC_CSS_PATH", WEB_MEDIA_URL . "css/" );
define( "STATIC_IMG_PATH", WEB_MEDIA_URL . 'img/' );


//用户相关
define( "DEFAULT_PIC", STATIC_IMG_PATH . 'userface.png' ); //默认头像
define( "VERTICAL", STATIC_IMG_PATH . '/vertical_screen.jpg' ); //竖屏默认图
define( "CROSS", STATIC_IMG_PATH . '/src/default/260x150.png' ); //横屏默认图
//直播教程相关
define( "OFFICIALVIDEO_CLIENT", $GLOBALS['env-def']['PRO']['domain-video'] . '/officialVideo/client.mp4' );
define( "OFFICIALVIDEO_IOS", $GLOBALS['env-def']['PRO']['domain-video'] . '/officialVideo/IOS.mov' );
define( "OFFICIALVIDEO_ANDROID", $GLOBALS['env-def']['PRO']['domain-video'] . '/officialVideo/Android.mp4' );

//  define('DEFAULT_USER_FACE', 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domian'] .'/main/static/img/userface_notlogin.png');


//pay env var
define( 'ALIPAY_APP_ID', '2016121304220612' );

define( "LOGIN_CHANNEL_QQ", 'qq' );
define( "LOGIN_CHANNEL_WECHAT", 'wechat' );
define( "LOGIN_CHANNEL_WEIBO", 'weibo' );
define( "LOGIN_CLIENT_WEB", 'web' );
define( "LOGIN_CLIENT_IOS", 'ios' );
define( "LOGIN_CLIENT_ANDROID", 'android' );

//apple push
if( $GLOBALS['env'] == "DEV" )
{
    define( 'APPLE_PUSH_PRO', 50 );
}
else
{
    define( "APPLE_PUSH_PRO", 51 );
}

//教学视频
define( "HUANPENG_VIDEO", '14835,14840,14845' );
//房间url
define( "ROOM_URL", WEB_ROOT_URL . 'room.php?luid=' );

define( "LOGIN_COOKIE_TIMEOUT", 30 * 24 * 3600 );
define("MYSQL_DATABASE_DUE",'due');
define("MYSQL_DATABASE_HUANPENG",'huanpeng');

define("REDIS_DATABASE_HUANPENG",'huanpeng');

require_once( INCLUDE_DIR . 'functions.php' );
require_once( INCLUDE_DIR . 'commonFunction.php' );
require_once( INCLUDE_DIR . 'DBHelperi_huanpeng.class.php' );
require_once(INCLUDE_DIR . "redis.class.php");

require __DIR__.'/system/ClassLoader.php';
chdir(__DIR__);
$loader = new \system\ClassLoader();
$loader->register();
$loader->setUseIncludePath(true);

//require_once INCLUDE_DIR . 'BaseHpFrame.php';
//
//class Hp extends \hp\BaseHp
//{
//
//}
//
//spl_autoload_register( [ 'Hp', 'autoload' ], true, true );
//
//Hp::$classMap = require( INCLUDE_DIR . 'classes.php' );