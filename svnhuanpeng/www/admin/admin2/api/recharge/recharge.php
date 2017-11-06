<?php
/**
 * 充值记录
 * Created by PhpStorm.
 * User: dong
 * Date: 17/4/21
 * Time: 上午9:58
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/publicRequist.class.php';
$db = new DBHelperi_admin();

/**
 * 添加一条内部充值记录
 *
 * @param array  $data
 * @param object $db
 *
 * @return bool
 */
function add_internal_distribution_record( $data, $db )
{
	$res = $db->insert( 'internal_distribution_record', $data );
	if( $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}

function updateFtidToInternal( $recordid, $ftid, $db )
{
	if( empty( $recordid ) || empty( $ftid ) )
	{
		return false;
	}
	$res = $db->where( "id=$recordid" )->update( 'internal_distribution_record', array( 'ftid' => $ftid ) );
	if( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}


$adminid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : 0;//管理员id
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
$type = isset( $_POST['type'] ) ? (int)$_POST['type'] : 1;
$ruid = isset( $_POST['ruid'] ) ? (int)( $_POST['ruid'] ) : 0;//待充值用户id
$hpcoin = isset( $_POST['hpcoin'] ) ? (int)$_POST['hpcoin'] : 0;//欢朋币
$hpbean = isset( $_POST['hpbean'] ) ? (int)$_POST['hpbean'] : 0;//欢朋币
$bean = isset( $_POST['bean'] ) ? (int)$_POST['bean'] : 0;//金豆
$coin = isset( $_POST['coin'] ) ? (int)$_POST['coin'] : 0;//金币
$rtype = isset( $_POST['rtype'] ) ? (int)$_POST['rtype'] : 0;//类型
$activeid = isset( $_POST['activeid'] ) ? (int)$_POST['activeid'] : 0;//活动id
$desc = isset( $_POST['desc'] ) ? trim( $_POST['desc'] ) : 0;//描述

if( empty( $adminid ) || empty( $encpass ) || empty( $type ) )
{
	error( -1007 );
}
if( empty( $hpcoin ) && empty( $hpbean ) && empty( $coin ) && empty( $bean ))
{
	error( -1007 );
}
$adminHelp = new AdminHelp( $adminid, $type );
$err = $adminHelp->loginError( $encpass );
if( $err )
{
	error( $err );
}
$data = array(
	'uid' => $ruid,
	'adminid' => $adminid,
	'hpcoin' => $hpcoin,
	'hpbean' => $hpbean,
	'coin' => $coin,
	'bean' => $bean,
	'type' => $rtype,
	'desc' => $desc,
	'activeid' => $activeid
);
$recordid = add_internal_distribution_record( filterData( $data ), $db );
if( $recordid )
{
	$data['recordid'] = $recordid;
	unset( $data['type'] );
	unset( $data['adminid'] );
	$res = publicRequist::outside_recharge( $data );//通知财务系统
	if( $res )
	{
		if( $res == -1 )
		{
			unsuccessLogForFinanceBack( '财务系统返回成功,但更新账户余额失败', array( 'financeBack' => $res, 'data' => $data), $db );
		}else{
			succ();
		}
		updateFtidToInternal( $recordid, $res, $db );//是否通知到财务系统
	}else{
		unsuccessLogForFinanceBack('财务系统返回失败',array('financeBack'=>$res,'data'=>$data),$db);
		error();
	}

}
else
{
	error();
}