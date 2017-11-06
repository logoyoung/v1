<?php

/**
 * 添加经纪公司
 * yandong@6rooms.com
 * date 2017-02-23 11:11
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 添加数据
 *
 * @param name $db
 * @param obj  $db
 *
 * @return array()
 */
function addCompany( $ctype, $name, $ownerid, $db )
{
	if( empty( $name ) )
	{
		return false;
	}
	$res = $db->insert( 'company', array( 'name' => $name, 'owner_id' => $ownerid, 'type' => $ctype, 'rate' => OTHER_RATE ) );
	if( false !== $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}

function addRecord( $adminid, $cid, $db )
{
	$data = array(
		'uid' => '0',
		'before_rate' => 0,
		'after_rate' => OTHER_RATE,
		'adminid' => $adminid,
		'type' => 2,
		'role_change_id' => (int)$cid,
		'desc' => '新增经纪公司or家族时初始化比率,此时role_change_id 为经纪公司id'
	);
	$res = addRateChangeRecord($data,$db);
	if( $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}


/**
 * start
 */
$uid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : '';
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
$type = isset( $_POST['type'] ) ? (int)$_POST['type'] : 1;
$name = isset( $_POST['name'] ) ? trim( $_POST['name'] ) : '';
$ctype = isset( $_POST['ctype'] ) ? trim( $_POST['ctype'] ) : 0;
$ownerid = isset( $_POST['ownerid'] ) ? (int)$_POST['ownerid'] : 0;
if( empty( $uid ) || empty( $encpass ) || empty( $type ) )
{
	error( -1007 );
}
if( empty( $name ) )
{
	error( -1038 );
}

$adminHelp = new AdminHelp( $uid, $type );
$err = $adminHelp->loginError( $encpass );
if( $err )
{
	error( $err );
}
$name = filterWords( $name );
$res = addCompany( $ctype, $name, $ownerid, $db );
if( $res )
{
	addRecord( $uid, $res, $db );//添加比率变换纪录
	succ();
}
else
{
	error( -1014 );
}
