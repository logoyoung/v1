<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/21
 * Time: 下午4:36
 */

/**this file write function that use handle huanpeng's business*/

namespace hpBizFun;
use lib\Finance;


/**
 * 为主播分配房间ID
 *
 * @param int    $uid 用户身份ID
 * @param object $db  数据库实例对象
 *
 * @return bool
 */
function setAnchorRoomId( $uid, \DBHelperi_huanpeng $db )
{
	$maxRoomid = getMax( $db );
	$roomid = checkRoomId( $maxRoomid + 1 );
	return addRoomid( $uid, $roomid, $db );
}

/**
 * 用户认证主播处理流程
 *
 * @param int                $uid
 * @param DBHelperi_huanpeng $db
 * @param bool               $model 认证模式
 *
 * @return void
 */
function applyToAuthor( $uid, \DBHelperi_huanpeng $db, $model = RN_MODEL )
{
	if( true === $model )
	{
		$status = 1;
	}
	else
	{
		$status = 0;
	}
	$cid = getCompangByUid( $uid, $db );
	if( $cid )
	{
		$cid = $cid[0]['cid'];
	}
	else
	{
		$cid = 0;
	}
	$res = $db->insert( 'anchor', array( 'uid' => $uid, 'cid' => $cid, 'cert_status' => $status, 'rate' => BASE_RATE ) );//添加到主播表
	if( false === $res )
	{
		error2( -5017, 2 );
	}
	else
	{
		setAnchorRoomId( $uid, $db );//分配房间号
		setInviteTest( $uid, $db );//内测纪录表
		$role_change_id=addAnchorRoleChangeRecord( $uid, $db );//角色变换纪录
		$result=addRateChangeRecord( $uid, $role_change_id, $db );//比率变换纪录
		if($result){
			$finObj=new Finance();
			$r=$finObj->setRate(array($uid=>$result), BASE_RATE,'由普通用户成为普通主播');
			if($r){
				update_NoticeStatus( $result, $db );//是否通知到财务系统
			}
		}
	}
}


/**
 * 更新通知状态
 *
 * @param int $rate_change_id
 * @param object $db
 *
 * @return bool
 */
function update_NoticeStatus( $rate_change_id, $db )
{
	if( empty( $rate_change_id ) )
	{
		return false;
	}
	$res = $db->where( "id in ($rate_change_id)" )->update( 'rate_change_record', array( 'status' => 1 ) );
	if( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}


/**
 * 角色改变纪录
 *
 * @param int $uid 用户id
 * @param object $db  数据库对象
 *
 * @return bool
 */
function addAnchorRoleChangeRecord( $uid, $db )
{
	if( empty( $uid ) )
	{
		return false;
	}
	$data = array(
		'uid' => $uid,
		'before_cid' => 0,
		'after_cid' => 0,
		'adminid' => 0,
		'desc' => '由普通用户成为普通主播'
	);
	$res = $db->insert( 'anchor_change_record', $data );
	if( $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}

/**
 * 比率改变纪录
 *
 * @param int $uid 用户id
 * @param int $role_change_id 角色变化纪录id
 * @param object $db  数据库对象
 *
 * @return bool
 */
function addRateChangeRecord( $uid, $role_change_id, $db )
{
	if( empty( $role_change_id ) || empty( $uid ) )
	{
		return false;
	}
	$data = array(
		'uid' => $uid,
		'before_rate' => 0,
		'after_rate' => BASE_RATE,
		'adminid' => 0,
		'type' => 1,
		'role_change_id' => $role_change_id,
		'desc' => '由普通用户成为普通主播,引起的比率改变'
	);
	$res = $db->insert( 'rate_change_record', $data );
	if( $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}


function testFunctionFile( $uid, \DBHelperi_huanpeng $db )
{
	$sql = "SELECT * FROM userstatic WHERE uid = $uid";
	$res = $db->query( $sql );
	$row = $res->fetch_assoc();

	print_r( $row );
}

function checkGeetestCode( $challenge, $validate, $seccode, $client, $redis = null )
{
	if( !$redis )
	{
		$redis = new \RedisHelp();
	}

	write_log(__FUNCTION__."::".json_encode(['cookieKey'=>"_geetest_client",'redisKey'=>$_COOKIE['_geetest_client'], 'status' => $redis->get( $_COOKIE['_geetest_client']),'client'=>$client,'domain'=>$GLOBALS['env-def'][$GLOBALS['env']]['domain']]), "geetest.log");

	$GtSdk = $client == '1' ? new \GeetestLib( CAPTCHA_APP_ID, PRIVATE_APP_KEY ) : new \GeetestLib( CAPTCHA_ID, PRIVATE_KEY );
	if( !empty( $_COOKIE['_geetest_client'] )
		&& (int)$redis->get( $_COOKIE['_geetest_client'] )
	)
	{
		write_log(__FUNCTION__."::"."success_validate", "geetest.log");
		return $GtSdk->success_validate( $challenge, $validate, $seccode );
	}
	else
	{
		return $GtSdk->fail_validate( $challenge, $validate, $seccode );
	}
}