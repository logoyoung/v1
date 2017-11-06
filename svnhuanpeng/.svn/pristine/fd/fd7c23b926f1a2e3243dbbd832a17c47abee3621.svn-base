<?php

/**
 * 获取经纪公司列表
 * yandong@6rooms.com
 * date 2017-01-22 16:43
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../../../include/redis.class.php';
require '../../includeAdmin/publicRequist.class.php';
$db = new DBHelperi_admin();

/**
 * 获取数据
 *
 * @param obj $db
 *
 * @return array()
 */
function unsingAnchor( $uid, $db )
{
	$res = $db->where( "uid=$uid" )->update( 'anchor', array( 'cid' => 0, 'rate' => BASE_RATE ) );
	if( false !== $res )
	{
		unAnchorToCompany( $uid, $db );
		return true;
	}
	else
	{
		return false;
	}
}

/**更新company_anchor表的数据
 *
 * @param $uid
 * @param $db
 *
 * @return bool
 */
function unAnchorToCompany( $uid, $db )
{
	$res = $db->where( "uid=$uid" )->delete( 'company_anchor' );
	if( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}


/**添加修改比率记录
 *
 * @param int $adminid 管理员id
 * @param int $cid     经纪公司id
 * @param int $rate    比率
 * @param     $db
 *
 * @return bool
 */
function add_Rate_Record( $adminid, $uid, $role_change_id, $beforInfo, $db )
{
	$data = array(
		'uid' => $uid,
		'before_rate' => (int)$beforInfo['rate'],
		'after_rate' => BASE_RATE,
		'adminid' => $adminid,
		'type' => 1,
		'role_change_id' => (int)$role_change_id,
		'desc' => '取消签约引起的比率变化,经纪公司id:' . $beforInfo['cid']
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

function add_Anchor_Role_change_Record( $adminid, $uid, $beforInfo, $db )
{
	$data = array(
		'uid' => $uid,
		'before_cid' => (int)$beforInfo['cid'],
		'after_cid' => 0,
		'adminid' => $adminid,
		'desc' => '取消签约'
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





/**
 * start
 */
$adminid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : '';
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
$type = isset( $_POST['type'] ) ? (int)$_POST['type'] : 1;
$uid = isset( $_POST['uuid'] ) ? (int)$_POST['uuid'] : 0;

if( empty( $adminid ) || empty( $encpass ) || empty( $type ) )
{
	error( -1007 );
}

$adminHelp = new AdminHelp( $adminid, $type );
$err = $adminHelp->loginError( $encpass );
if( $err )
{
	error( $err );
}
if( empty( $uid ) )
{
	error( -1007 );
}
$beforeInfo = getBeforeRateByUid( $uid, $db );
$result = unsingAnchor( $uid, $db );
if( $result )
{
	$list = array();
	$roleId = add_Anchor_Role_change_Record( $adminid, $uid, $beforeInfo, $db );//添加一条记录到角色变更表
	if( $roleId )
	{
		$list[$uid] = add_Rate_Record( $adminid, $uid, $roleId, $beforeInfo, $db );//添加一条记录到汇率变更表
	}
	$res=publicRequist::outside_setRate($list,BASE_RATE,'取消签约');//通知财务系统
	if($res){
		updateNoticStatus( $list[$uid], $db );//是否通知到财务系统
	}
	succ( $result );
}
else
{
	error();
}

