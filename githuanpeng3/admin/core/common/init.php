<?php

/**
 * Created by PhpStorm.
 * User: Dylan
 * Date: 17/03/06
 * Time: 下午4:17
 */
define('ADMIN_PATH', dirname(dirname(dirname(__FILE__))));
define('INCLUDE_PATH', ADMIN_PATH . '/core/common/');
define('LIB_PATH', ADMIN_PATH . '/lib/');
define('SYSTEM_PATH', ADMIN_PATH . '/system/');
define('DB_PATH', INCLUDE_PATH . 'DB/');


//公共的css、js、img路径
define('CSS', ADMIN_PATH . '/lib/css/');
define('JS', ADMIN_PATH . '/lib/js/');
define('IMG', ADMIN_PATH . '/lib/img/');
//子系统路径常量
define('BROKER',SYSTEM_PATH. '/broker/'); //经纪公司系统
define('POWER',SYSTEM_PATH. '/power/'); //权限管理系统
define('MANAGE',SYSTEM_PATH. '/manage/'); //网站管理系统
define('REVIEW',SYSTEM_PATH. '/review/'); //审核系统
define('STATISTIC',SYSTEM_PATH. '/statistic/'); //统计系统
define('LOG',SYSTEM_PATH. '/log/'); //日志系统


// 域名
define('DOMAINNAME_PRO', 'www.huanpeng.com');
define('DOMAINNAME_PRO_IMG', 'img.huanpeng.com');
define("DOMAINNAME_ADMIN_PRE", 'admin-pre.huanpeng.com');
define("DOMAINNAME_ADMIN_PRE_IMG", 'pre-img.huanpeng.com');
define('DOMAINNAME_DEV', 'admin-dev.huanpeng.com');
define('DOMAINNAME_DEV_IMG', 'dev-img.huanpeng.com');

date_default_timezone_set('Asia/Shanghai');

//域名协议
if( ( isset( $_SERVER['HTTPS'] ) && strtoupper($_SERVER['HTTPS'] ) == 'ON' )
    || ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&  strtoupper( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) == 'HTTPS' )
    || ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443 ) )
{
    define('DOMAIN_PROTOCOL','https://');
}
else
{
    define('DOMAIN_PROTOCOL','http://');
}

require(INCLUDE_PATH . "function.php");
require(DB_PATH . "DBHelps_admin.class.php");
//直播状态 100 正在直播
define('LIVE', 100);
//录像状态 0:待发布 1:审核中 2:已发布 3:审核未通过 100:录像已被删除
define('VIDEO_WAIT', 0);
define('VIDEO_UNPUBLISH', 1);
define('VIDEO', 2);
define('VIDEO_UNPASS', 3);
define('VIDEO_DEL', 100);

//实名认证状态 1:待审核, 100:审核未通过, 101:审核通过
define("RN_MODEL", false);//实名模式 true表示需要上传证件 flase表示绑定手机即可直播
define("RN_NOT", 0);
define('RN_WAIT', 1);
define('RN_UNPASS', 100);
define('RN_PASS', 101);

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

//审核模式
define("CHECK_HEAD", 1);  //头像审核
define("CHECK_NICK", 2);//昵称审核
define("CHECK_TITLE", 3);//直播标题审核
define("CHECK_COMMENT", 4);//评论审核
define("CHECK_BARRAGE", 5);//／弹幕审核
define("CHECK_NOTICE", 6);//／主播公告审核

//网宿防盗链密钥与过期时间
define('WS_SECURITY_CHAIN','1234!@#$abcdef');
define('WS_EXPIRED',2*60*60);

//任务状态
define("TASK_STAT_ONLINE", 1);
define("TASK_STAT_STOP", 0);
//任务纪录状态	0:未完成 , 1:已经完成, 2:已经领取
define("TASK_UNFINISH", 0);
define("TASK_FINISHED", 1);
define("TASK_BEAN_RECEIVED", 2);

//首页直播推荐数量
define("LIVE_RECOMMENT_NUMBER", 6);
//首页资讯推荐数量
define("INFORMATION_RECOMMENT_NUMBER", 5);

define("ADVERTISEMENT_RECOMMENT_NUMBER", 2);

define('MSG_ADMIN', "hehehe$81)_(*");

if( isset( $_SERVER['HTTP_HOST'] ) )
{
    if( $_SERVER['HTTP_HOST'] == DOMAINNAME_DEV )
        $GLOBALS['env'] = "DEV";
    elseif ($_SERVER["HTTP_HOST"] == DOMAINNAME_ADMIN_PRE )
        $GLOBALS['env'] = "PRE";
    else
        $GLOBALS['env'] = "PRO";
}
elseif( isset( $argv[1] ) )
{
    if(strtoupper($argv[1]) == "DEV")
        $GLOBALS['env'] = "DEV";
    elseif (strtoupper($argv[1]) == "PRE" )
        $GLOBALS['env'] = "PRE";
    else
        $GLOBALS['env'] = "PRO";
}

// 环境相关配置
$GLOBALS['env-def'] = array(
    'DEV' => array(
        'domain' => DOMAINNAME_DEV,
        'domain-img' => DOMAINNAME_DEV_IMG,
//        'domain-video' => DOMAIN_PROTOCOL.'dev-img.huanpeng.com/v',
        'huan-video' => DOMAIN_PROTOCOL.'img.huanpeng.com/v',
//        'domain-vposter'=>DOMAIN_PROTOCOL.'dev-img.huanpeng.com/v',

        'domain-video' => DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'domain-vposter'=>DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'img-dir' => '/data/huanpeng-img',
        'video-dir' => '/data/huanpeng-img/v',

        //'stream-pub' => '222.35.75.72:8080/liverecord',
        //'stream-watch' => '222.35.75.72:4000/liverepeater',
        'stream-pub' => 'dev-urtmp.huanpeng.com/liverecord',
        'stream-watch' => 'dev-drtmp.huanpeng.com/liverecord',
        'stream-stop-notify' => 'http://222.35.75.72:9300/r?s=',
        //'http://223.203.212.30:9300/r?s=',
        'hls' => '222.35.75.72',
        //'223.203.212.30',
        'hls-server' => '223.203.212.30/liverepeater',
        'socket' => array('122.70.146.49:8082'),
        'cookiePath' => '/'
    ),
    'PRE' => array(
        'domain' => DOMAINNAME_ADMIN_PRE,
        'domain-img' => DOMAINNAME_ADMIN_PRE_IMG,
        'huan-video' => DOMAIN_PROTOCOL.'pre-img.huanpeng.com/v',
        'domain-video' => DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'domain-vposter'=>DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'img-dir' => '/data/huanpeng-img',
        'video-dir' => '/data/huanpeng-img/v',
        'stream-pub' => 'pre-urtmp.huanpeng.com/liverecord',
        'stream-watch' => 'pre-drtmp.huanpeng.com/liverecord',
        'stream-stop-notify' => 'http://222.35.74.221:9300/r?s=',
        'hls' => '222.35.74.221',
        'socket' => array('42.62.27.112:8082'),
        'cookiePath' => '/'

    ),
    'PRO' => array(
        'domain' => DOMAINNAME_PRO,
        'domain-img' => DOMAINNAME_PRO_IMG,
        'huan-video' => DOMAIN_PROTOCOL.'img.huanpeng.com/v',
        'domain-video' => DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'domain-vposter'=>DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'img-dir' => '/leofs/i',
        'video-dir' => '/leofs/v',
//        'stream-pub' => '222.35.74.221:8080/liverecord',
//        'stream-watch' => '222.35.74.221:8080/liverecord',
        'stream-pub' => 'urtmp.huanpeng.com/liverecord',
        'stream-watch' => 'drtmp.huanpeng.com/liverecord',
        'stream-stop-notify' => 'http://222.35.74.221:9300/r?s=',
        'hls' => '222.35.74.221',
        'socket' => array('42.62.27.123:8082', '42.62.27.124:8082'),
        'cookiePath' => '/'
    )
);