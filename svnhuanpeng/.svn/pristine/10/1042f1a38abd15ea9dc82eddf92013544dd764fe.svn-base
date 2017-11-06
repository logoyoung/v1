<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/15
 * Time: 下午5:33
 */
define('WEBSITE_ROOT', '/usr/local/huanpeng/');
define('INCLUDE_DIR', WEBSITE_ROOT . 'includeAdmin/');
define('LOG_DIR', '/data/logs/');

define("ADMIN_ROOT", WEBSITE_ROOT . '/htdocs/admin2/');
define("ADMIN_MODULE", ADMIN_ROOT . 'module/');
// 域名
define('DOMAINNAME_PRO', 'www.huanpeng.com');
define('DOMAINNAME_PRO_IMG', 'img.huanpeng.com');
define('DOMAINNAME_DEV', 'dev.huanpeng.com');
define('DOMAINNAME_DEV_IMG', 'dev-img.huanpeng.com');

//日志
define('LOGFN_SEND_MSG_ERR', LOG_DIR."huanpeng_sendMsg.log");

date_default_timezone_set('Asia/Shanghai');
require_once(INCLUDE_DIR . "function.php");
require_once(INCLUDE_DIR . "commonFunction.php");
require_once(INCLUDE_DIR . "DBHelps_admin.class.php");
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


if (isset($_SERVER["HTTP_HOST"]) and $_SERVER["HTTP_HOST"] == DOMAINNAME_DEV )
    $GLOBALS['env'] = 'DEV';
elseif (isset($argv[1]) and $argv[1] == 'DEV')
    $GLOBALS['env'] = 'DEV';
else
    $GLOBALS['env'] = 'PRO';


// 环境相关配置
$GLOBALS['env-def'] = array(
    'DEV' => array(
        'domain' => DOMAINNAME_DEV,
//        'domain-img' => DOMAINNAME_DEV_IMG,
//        'domain-video' => 'http://dev-img.huanpeng.com/v',
//        'img-dir' => '/data/huanpeng-img'
    ),
    'PRO' => array(
        'domain' => DOMAINNAME_PRO,
//        'domain-img' => DOMAINNAME_PRO_IMG,
//        'domain-video' => 'http://img.huanpeng.com/v',
//        'img-dir' => '/leofs/i/huanpeng'
    ),
);


if(strtoupper($_COOKIE['currentt_admin_workplace']) !== "DEV"){
	$GLOBALS['env-def']['DEV'] = array_merge($GLOBALS['env-def']['DEV'],[
		'domain-img' => DOMAINNAME_PRO_IMG,
//		'domain-video' => 'http://img.huanpeng.com/v',
        'domain-video' => 'http://fvod.huanpeng.com',
        'domain-vposter'=>'http://fvod.huanpeng.com',//录像海报
		'img-dir' => '/leofs/i/huanpeng'
	]);
	$GLOBALS['admin_db_env'] = "PRO";
}else{
	$GLOBALS['env-def']['DEV'] = array_merge($GLOBALS['env-def']['DEV'],[
		'domain-img' => DOMAINNAME_DEV_IMG,
//		'domain-video' => 'http://dev-img.huanpeng.com/v',
        'domain-video' => 'http://fvod.huanpeng.com',
        'domain-vposter'=>'http://fvod.huanpeng.com',//录像海报
		'img-dir' => '/data/huanpeng-img'
	]);
	$GLOBALS['admin_db_env'] = "DEV";
}


//用户相关
define("DEFAULT_PIC", 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/admin2/common/global/userface.png'); //默认头像
define("VERTICAL", 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/main/static/img/vertical_screen.jpg'); //竖屏默认图
define("CROSS", 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/main/static/img/src/default/260x150.png'); //横屏默认图
define("DEFAULT_HEAD_PATH",'defaultPic/09430c53187f9cf809cbcca965aba90e.png'); //默认头像路径 （头像重审使用）



define('ADMIN_URL_ROOT', 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/admin2/');
define("__ADMIN_STATIC__", ADMIN_URL_ROOT . 'common/');
?>