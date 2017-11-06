<?php

/**
 * 修改经纪公司比率
 * yandong@6rooms.com
 * date 2017-02-23 11:11
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/publicRequist.class.php';
$db = new DBHelperi_admin();
/**
 * 添加数据
 *
 * @param name $db
 * @param obj  $db
 *
 * @return array()
 */
function updateCompany( $cid, $rate, $db )
{
	if( empty( $cid ) || empty( $rate ) )
	{
		return false;
	}
	$res = $db->where( "id = $cid" )->update( 'company', array( 'rate' => $rate ) );
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
 * 获去修改前的比率
 *
 * @param int $cid 经纪公司id
 * @param     $db
 *
 * @return bool
 */
function getRateByCid( $cid, $db )
{
	if( empty( $cid ) )
	{
		return false;
	}
	$res = $db->field( 'rate' )->where( "id=$cid" )->limit( 1 )->select( 'company' );
	if( false !== $res )
	{
		return $res[0]['rate'];
	}
	else
	{
		return false;
	}
}


/**添加经纪公司修改比率记录
 *
 * @param int $adminid 管理员id
 * @param int $cid     经纪公司id
 * @param int $rate    比率
 * @param     $db
 *
 * @return bool
 */
function update_Rate_Record( $adminid, $uid , $cid, $rate, $beforeRate, $db )
{
	$data = array(
		'uid' => $uid,
		'before_rate' => (int)$beforeRate,
		'after_rate' => (int)$rate,
		'adminid' => $adminid,
		'type' => 2,
		'role_change_id' => (int)$cid,
		'desc' => '修改经纪公司比率'
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

/**
 * 获取经纪公司旗下主播列表
 *
 * @param int $cid 经纪公司id
 * @param     $db
 *
 * @return array|bool
 */
function getCompanyAnchorListByCid( $cid, $db )
{
	if( empty( $cid ) )
	{
		return false;
	}
	$res = $db->field( "uid" )->where( "cid=$cid" )->select( 'anchor' );
	if( $res )
	{
		return array_column( $res, 'uid' ) ? array_column( $res, 'uid' ) : array();
	}
	else
	{
		return array();
	}
}

/*更改经纪公司旗下所有主播的汇率
 *
 * @param  array $uidArray 经纪公司旗下主播id
 * @param  int $cid  经纪公司id
 * @param  int $rate 比率
 * @param $db
 *
 * @return bool
 */
function updateCompanyAnchorRate( $uidArray, $cid, $rate, $db )
{
	if( empty( $uidArray ) || !is_array( $uidArray ) || empty( $cid ) || empty( $rate ) )
	{
		return false;
	}
	$uids = implode( ',', $uidArray );
	$res = $db->where( "uid in ($uids)" )->update( 'anchor', array( 'rate' => $rate ) );
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
$adminId = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : '';
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
$type = isset( $_POST['type'] ) ? (int)$_POST['type'] : 1;
$cid = isset( $_POST['cid'] ) ? (int)( $_POST['cid'] ) : '';
$rate = isset( $_POST['rate'] ) ? (int)( $_POST['rate'] ) : 0;
if( empty( $adminId ) || empty( $encpass ) || empty( $type ) || empty( $cid ) || $rate < 0 || $rate > 100 )
{
	error( -1007 );
}

$adminHelp = new AdminHelp( $adminId, $type );
$err = $adminHelp->loginError( $encpass );
if( $err )
{
	error( $err );
}
$beforeRate = getRateByCid( $cid, $db );
$afterRes = updateCompany( $cid, $rate, $db );
$res = update_Rate_Record( $adminId, 0, $cid, $rate, $beforeRate, $db );//添加比率变换纪录
if( $afterRes && $res )
{
	$list = array();
	$uidsArray = getCompanyAnchorListByCid( $cid, $db );//获取经纪公司旗下主播
	if( $uidsArray )
	{
		$checkRes = updateCompanyAnchorRate( $uidsArray, $cid, $rate, $db );//更改经纪公司旗下主播比率
		if( $checkRes )
		{
			for ( $i = 0, $k = count( $uidsArray ); $i < $k; $i++ )
			{
				$recordResId = update_Rate_Record( $adminId, $uidsArray[$i], $cid, $rate, $beforeRate, $db );//同步到比率变更记录表中
				$list[$uidsArray[$i]] = $recordResId;
			}
			$res=publicRequist::outside_setRate($list,$rate,'修改经纪公司比率');//通知财务系统
			if($res){
				$recordlist=implode(',',$list);
				updateNoticStatus( $recordlist, $db );//是否通知到财务系统
			}else{
				unsuccessLogForFinanceBack('修改经纪公司比率 财务系统返回失败',array('adminid'=>$adminId,'cid'=>$cid,'rate'=>$rate,'beforeRate'=>$beforeRate,'uidlist'=>$recordlist),$db);
			}
		}
	}
	succ();
}
else
{
	error( -1014 );
}
