<?php
/**
 * 老版admin配置继承
 */


//公司比率常量
define('BASE_RATE',60); //普通主播默认比率
define('OFFICIAL_RATE',60); //平台签约主播主播默认比率
define('OTHER_RATE',70); //经纪公司，工会默认比率

/*****日志管理开始********/
define( 'LOG_DIR', '/data/logs/' );
define( 'LOGFN_SEND_MSG_ERR', LOG_DIR . "huanpeng_sendMsg.log" );
/*****日志管理结束********/
// 域名
define('DOMAINNAME_DEV', 'opdev.huanpeng.com');
define('DOMAINNAME_DEV_COMPANY', 'cpsdev.huanpeng.com');
define('DOMAINNAME_OUTSIDE_DEV', 'dev.huanpeng.com');
define('DOMAINNAME_DEV_IMG', 'dev-img.huanpeng.com');

define("DOMAINNAME_ADMIN_PRE", 'oppre.huanpeng.com');
define('DOMAINNAME_PRE_COMPANY', 'cpspre.huanpeng.com');
define("DOMAINNAME_OUTSIDE_PRE", 'pre.huanpeng.com');
define("DOMAINNAME_ADMIN_PRE_IMG", 'pre-img.huanpeng.com');

define('DOMAINNAME_PRO', 'op.huanpeng.com');
define('DOMAINNAME_PRO_COMPANY', 'cps.huanpeng.com');
define('DOMAINNAME_OUTSIDE_PRO', 'www.huanpeng.com');
define('DOMAINNAME_PRO_IMG', 'img.huanpeng.com');


define( "ROOM_SILENCE_TIMEOUT", 3600 );
define( 'RECHARGE_ORDER_FINISH', 1 );
//直播状态 100 正在直播
define('LIVE', 100);
define( 'LIVE_TIMEOUT', 230 );
//录像状态 0:待发布 1:审核中 2:已发布 3:审核未通过 100:录像已被删除
define('VIDEO_WAIT', 0);
define('VIDEO_UNPUBLISH', 1);
define('VIDEO', 2);
define('VIDEO_UNPASS', 3);
define('VIDEO_DEL', 100);

//zwq add 录像状态 0:未锁定 1:锁定2:审核通过3:审核未通过
define('VIDEO_CHECK_UNLOCK', 0);
define('VIDEO_CHECK_LOCK', 1);
define('VIDEO_CHECK_PASS', 2);
define('VIDEO_CHECK_UNPASS', 3);

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

//dota  域名
define("DOTA_DOMAIN", "dota.huanpeng.com");
//dota秘钥
define("DOTA_AUTHORIZE_KEY", "DaXiaoXie!@#$88765_");

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
define("INFORMATION_ISPUBLISH", 1); //1 已发布

define("ADVERTISEMENT_RECOMMENT_NUMBER", 2);

define('MSG_ADMIN', "hehehe$81)_(*");


function get_current_env()
{
	$hostname = gethostname();
//	echo $hostname."\n";
	$rule = [
		//"PRO" => "/^HangpengW/",
		'PRE' => "/^huanp-node-[3-4].novalocal/",  //3是50，4是51
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

$GLOBALS['env'] = get_current_env();

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

// 环境相关配置
$GLOBALS['env-def'] = array(
    'DEV' => array(
        'domain' => DOMAINNAME_DEV,
		'outside-domain' =>  DOMAINNAME_OUTSIDE_DEV,
    	'company-domain' => DOMAINNAME_DEV_COMPANY,
        'domain-img' => DOMAINNAME_DEV_IMG,
        'domain-avatar' => DOMAINNAME_PRO_IMG,
        'huan-video' => DOMAIN_PROTOCOL.'img.huanpeng.com/v',
        'domain-video' => DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'domain-vposter'=>DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'img-dir' => '/data/huanpeng-img',
        'img-url' => DOMAIN_PROTOCOL.DOMAINNAME_DEV_IMG,
        'video-dir' => '/data/huanpeng-img/v',
        'stream-pub' => 'dev-urtmp.huanpeng.com/liverecord',
        'stream-watch' => 'dev-drtmp.huanpeng.com/liverecord',
        'stream-stop-notify' => 'http://222.35.75.72:9300/r?s=',
        'hls' => '222.35.75.72',
        'hls-server' => '223.203.212.30/liverepeater',
        'socket' => array('122.70.146.49:8082'),
        'cookiePath' => '/'
    ),
    'PRE' => array(
        'domain' => DOMAINNAME_ADMIN_PRE,
		'outside-domain' => DOMAINNAME_OUTSIDE_PRE,
    	'company-domain' => DOMAINNAME_PRE_COMPANY,
        'domain-img' => DOMAINNAME_ADMIN_PRE_IMG,
        'domain-avatar' => DOMAINNAME_PRO_IMG,
        'huan-video' => DOMAIN_PROTOCOL.'img.huanpeng.com/v',
        'domain-video' => DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'domain-vposter'=>DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'img-dir' => '/data/huanpeng-img',
        'img-url' => DOMAIN_PROTOCOL.DOMAINNAME_ADMIN_PRE_IMG,
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
		'outside-domain' => DOMAINNAME_OUTSIDE_PRO,
    	'company-domain' => DOMAINNAME_PRO_COMPANY,
        'domain-img' => DOMAINNAME_PRO_IMG,
        'domain-avatar' => DOMAINNAME_PRO_IMG,
        'huan-video' => DOMAIN_PROTOCOL.'img.huanpeng.com/v',
        'domain-video' => DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'domain-vposter'=>DOMAIN_PROTOCOL.'fvod.huanpeng.com',
        'img-dir' => '/leofs/i',
        'img-url' => DOMAIN_PROTOCOL.DOMAINNAME_PRO_IMG,
        'video-dir' => '/leofs/v',
        'stream-pub' => 'urtmp.huanpeng.com/liverecord',
        'stream-watch' => 'drtmp.huanpeng.com/liverecord',
        'stream-stop-notify' => 'http://222.35.74.221:9300/r?s=',
        'hls' => '222.35.74.221',
        'socket' => array('42.62.27.123:8082', '42.62.27.124:8082'),
        'cookiePath' => '/'
    )
);

//用户相关
define("DEFAULT_PIC", 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . '/static/img/userface.png'); //默认头像
//define("VERTICAL", 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/admin2/static/img/vertical_screen.jpg'); //竖屏默认图
define("CROSS", 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/op-admin/img/default/260x150.png'); //横屏默认图
define("DEFAULT_HEAD_PATH",''); //默认头像路径 （头像重审使用）
define("ADMIN_LIVE_API",'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain']) . '/';
define("DUE_CERT",'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img']) . '/';