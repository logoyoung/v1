<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/28
 * Time: 下午4:18
 */


require_once '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
include_once INCLUDE_DIR . 'Anchor/Review.class.php';
require '../../includeAdmin/publicRequist.class.php';

$db = new DBHelperi_admin();

function passAnchor( $id, $type, $db )
{
	if( empty( $id ) )
	{
		return false;
	}
	if( $type == 1 )
	{
		$status = RN_PASS;//通过
	}
	if( $type == 2 )
	{
		$status = RN_UNPASS;//驳回
	}
	$data = array(
		'status' => $status,
		'passtime' => date( 'Y-m-d h:i:s' )
	);
	$res = $db->where( 'id=' . $id . '' )->update( 'userrealname', $data );
	if( $res !== false )
	{
		$succ = array( 'isSuccess' => '1' );
	}
	else
	{
		$succ = array( 'isSuccess' => '0' );
	}
	return $succ;
}

/**剔除靓号
 *
 * @param $roomid  房间id
 *
 * @return bool
 */
function checkRoomId( $roomid )
{
	if( empty( $roomid ) )
	{
		return false;
	}
	$date = file_get_contents( './roomid.txt' );
	if( strstr( $date, "$roomid" ) )
	{
		$roomid++;
		return checkRoomId( "$roomid" );
	}
	else
	{
		return $roomid;
	}
}

/**获取最后一条的房间id
 *
 * @param $db
 *
 * @return int
 */
function getMax( $db )
{
	$res = $db->field( "max(id) as id" )->select( 'roomid' );
	if( $res && isset( $res[0]['id'] ) )
	{
		$re = $db->field( "roomid" )->where( "id=" . $res[0]['id'] )->select( 'roomid' );
		return $re[0]['roomid'];
	}
	else
	{
		return 100000;
	}
}

//添加房间
function addRoomid( $uid, $roomid, $db )
{
	if( empty( $uid ) || empty( $roomid ) )
	{
		return false;
	}
	$utime = date( 'Y-m-d H:i:s', time() );
	$sql = "insert into roomid (`uid`, `roomid`,`utime`) value($uid,$roomid,'$utime') on duplicate key update utime='$utime'";
	$res = $db->query( $sql );
	if( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getUidById( $id, $db )
{
	if( empty( $id ) )
	{
		return false;
	}
	$uid = $db->field( 'uid' )->where( 'id=' . $id )->select( 'userrealname' );
	if( empty( $uid ) || false === $uid )
	{
		return false;
	}
	else
	{
		return $uid[0]['uid'];
	}
}

function setInviteTest( $uid, $db )
{
	if( empty( $uid ) )
	{
		return false;
	}
	$res = $db->where( "ruid=$uid" )->update( 'inside_test_inviteRecoed', array( 'status' => 1 ) );
	if( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function addNewAnchor( $id, $db )
{
	if( empty( $id ) )
	{
		return false;
	}
	$uid = $db->field( 'uid' )->where( 'id=' . $id )->select( 'userrealname' );
	if( empty( $uid ) || false === $uid )
	{
		return false;
	}
	$uid = $uid[0]['uid'];
	$utime = date('Y-m-d H:i:s');
	$rate = BASE_RATE;
	$sql = "insert into anchor (`uid`,`utime`,`rate`) value($uid,'$utime',$rate) on duplicate key update utime='$utime',rate=$rate";
	$res = $db->query( $sql );
	if( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function pass_Rate_Record( $adminid, $uid, $role_change_id, $beforInfo, $db )
{
	$data = array(
		'uid' => $uid,
		'before_rate' => (int)$beforInfo['rate'],
		'after_rate' => BASE_RATE,
		'adminid' => $adminid,
		'type' => 1,
		'role_change_id' => (int)$role_change_id,
		'desc' => '通过实名认证引起的比率变化,经纪公司id:' . $beforInfo['cid']
	);
	$res = addRateChangeRecord( $data, $db );
	if( $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}

function pass_Role_change_Record( $adminid, $uid, $beforInfo, $db )
{
	$data = array(
		'uid' => $uid,
		'before_cid' => (int)$beforInfo['cid'],
		'after_cid' => 0,
		'adminid' => $adminid,
		'desc' => '通过实名认证'
	);
	$res = addRoleChangeRecord( $data, $db );
	if( false !== $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}


$id = isset( $_POST['id'] ) ? (int)( $_POST['id'] ) : '';
$type = isset( $_POST['type'] ) ? (int)( $_POST['type'] ) : '';
$adminid = isset( $_POST['uid'] ) ? (int)( $_POST['uid'] ) : '';
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
$utype = isset( $_POST['utype'] ) ? (int)( $_POST['utype'] ) : 1;//管理员类
if( empty( $id ) || !in_array( $type, array( 1, 2 ) ) )
{
	error( -1005 );
}
if( empty( $adminid ) || empty( $encpass ) )
{
	error( -1005 );
}
if( !is_numeric( $utype ) )
{
	error( -1023 );
}
$adminHelp = new AdminHelp( $adminid, $utype );
$err = $adminHelp->loginError( $encpass );
if( $err )
{
	error( $err );
}
$res = passAnchor( $id, $type, $db );
if( $res['isSuccess'] == 1 )
{
	if( $type == 1 )
	{//审核完成添加到主播表
		$res = addNewAnchor( $id, $db );
		if( false === $res )
		{
			error( -1010 );
		}
		else
		{

			$lastRoomId = getMax( $db );//获取最后一个房间号
			$roomid = checkRoomId( $lastRoomId + 1 );
			$uid = getUidById( $id, $db );
			setInviteTest( $uid, $db );//内测纪录
			if( $uid)
			{
				addRoomid( $uid, $roomid, $db );//添加房
			}
			$beforeInfo = getBeforeRateByUid( $uid, $db );
			$roleId =pass_Role_change_Record( $adminid, $uid, $beforeInfo, $db );//添加一条记录到角色变更表
			if( $roleId )
			{
				$list[$uid] = pass_Rate_Record( $adminid, $uid, $roleId, $beforeInfo, $db );//添加一条记录到汇率变更表
			}
			//调用setRate
			$r=publicRequist::outside_setRate($list,BASE_RATE,'完成实名认证');//通知财务系统
			if($r){
				updateNoticStatus( $list[$uid], $db );//是否通知到财务系统
			}else{
				unsuccessLogForFinanceBack('实名认证后，比率变化 财务系统返回失败',array('financeBack'=>$r,'uid'=>$uid,'adminid'=>$adminid,'rateRecordId'=>$list,'roleid'=>$roleId,'beforeinfo'=>$beforeInfo),$db);
			}
		}
	}
	$review = new ReviewUser( $db );
	$res=$review->setFinish( $id, $adminid);
	if(false !== $review->setFinish( $id, $adminid  ) )
	{
		succ();
	}
	else
	{
		error( -1010 );
	}
}
else
{

	error( -1010 );
}

