<?php

include '../../../../include/init.php';
require( INCLUDE_DIR . 'User.class.php' );
use service\rule\TextService;
use service\event\EventManager;
use service\user\UserAuthService;

/**
 * 修改昵称信息
 * date 2016-1-5 14:58
 * author yandong@6rooms.com
 * version 0.1 update 2016-05-07
 */
$db = new DBHelperi_huanpeng();

function record_for_updateNick( $uid, $before, $after, $type, $db )
{
	if( empty( $uid ) || empty( $before ) || empty( $after ) || empty( $type ) )
	{
		return false;
	}
	$data=array(
		'uid'=>$uid,
		'before_name'=>$before,
		'after_name'=>$after,
		'type'=>$type
	);
	$res=$db->insert('update_nick_record',$data);
	if($res){
		return true;
	}else{
		return false;
	}
}

/**
 * start
 */
$uid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : '';
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
$nick = isset( $_POST['nick'] ) ? trim( $_POST['nick'] ) : '';
if( empty( $uid ) || empty( $encpass ) )
{
	error2( -4013 );
}
$uid = checkInt( $uid );
$encpass = checkStr( $encpass );
if( !empty( $nick ) )
{
	$checkEmoji = checkEmoji( $nick );
	if( $checkEmoji )
	{
		error2( -4091, 2 );
	}
	$nick = filterData( $nick );
	$nickLen = mb_strlen( $nick, 'utf-8' );
	if( $nickLen < 3 || $nickLen > 12 )
	{
		error2( -4010, 2 );
	}
	else
	{
		if( mb_strlen( $nick, 'latin1' ) < 3 || mb_strlen( $nick, 'latin1' ) > 36 )
		{
			error2( -4010, 2 );
		}
	}
}
else
{
	error2( -4064, 2 );
}

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);

//校验encpass、用户 登陆状态
if($auth->checkLoginStatus() !== true)
{
    //获取校验结果
    $result    = $auth->getResult();
    //错误码
    $errorCode = $result['error_code'];
    //错误消息
    $errorMsg  = $result['error_msg'];
    //假如是封禁的，可以获取禁时间
    $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
    write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
    error2(-4067,2);
}

$textService = new TextService();
$textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
//关闭后如果接请求反垃圾接口网络服务异常都会返回true,默认通过
//$textService->setCallLevel(true);
$port = 0;
$textService->addText($nick,$uid,TextService::CHANNEL_NICKNAME)->setIp(fetch_real_ip($port));
//反垃圾过滤
if(!$textService->checkStatus())
{
	write_log("error|昵称包含敏感内容;uid:{$uid}",'modify_nick');
    error2( -4035, 2 );
}

$userHelp = new UserHelp( $uid, $db );
if( $userHelp->isUserNickExist( $nick ) )
{
	error2( -4035, 2 );
}

$db->autocommit( false );
$db->query( 'begin' );
$checkNickMode = checkMode( CHECK_NICK, $db );
$isfree = checkisFreeChangeNick( $uid, $db );//是否有免费的改名机会
if( $isfree )
{
	$spend = true;
	$uptype=2;//免费修改标记
    if($spend)
		file_put_contents( LOG_DIR."modifyNick.log", json_encode(array('uid'=>$uid,'ctime'=>time(),'nick'=>$nick))."\n", FILE_APPEND );
}
else
{
	$coin = (int)$userHelp->getProperty()['hpcoin'];
	if( $coin < MODIFY_NICK_COST )
	{
		error2( -5023, 2 ); //余额不足
	}
	$spend = $userHelp->costHpCoin( MODIFY_NICK_COST, $coin );
	$uptype=1;//花钱修改标记
}
$olderInfo=$userHelp->getUsers();
record_for_updateNick( $uid, $olderInfo['nick'], $nick, $uptype, $db );//修改昵称记录
if( $checkNickMode )
{
	//先发后审
	setNickToAdmin( $uid, $nick, $db, USER_NICK_AUTO_PASS );//同步到admin_user_nick表中
	if( !$userHelp->setNick( $nick, $db ) || !$spend )
	{
		$db->rollback();
		error( -5017 ); //系统错误
	}
	else
	{
		$db->query( 'commit' );
		$db->autocommit( true );
		if( $isfree )
		{
			changeIsfreeStatus( $uid, 0, $db );
		}

		$event = new EventManager();
        $event->trigger(EventManager::ACTION_USER_INFO_UPDATE,['uid' => $uid]);
        $event = null;

		$user = $userHelp->getProperty();
		succ( array( 'hpbean' => $user['hpbean'], 'hpcoin' => $user['hpcoin'] ) );
	}
}
else
{
//先审后发
	$res = setNickToAdmin( $uid, $nick, $db, USER_NICK_WAIT );//同步到admin_user_nick表中
	if( !$res || !spend )
	{
		$db->rollback();
		error( -5017 ); //系统错误
	}
	else
	{
		$db->query( 'commit' );
		$db->autocommit( true );
		if( $isfree )
		{
			changeIsfreeStatus( $uid, 0, $db );
		}
		$user = $userHelp->getProperty();
		succ( array( 'hpbean' => $user['hpbean'], 'hpcoin' => $user['hpcoin'] ) );
	}

}


