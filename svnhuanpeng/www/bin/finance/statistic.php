<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/5/3
 * Time: 17:53
 */

include __DIR__ . "/../../include/init.php";

use lib\FinanceStatistic;

$db = new \DBHelperi_huanpeng(  );

$redis = new \RedisHelp();

$statisticObj = new FinanceStatistic( $db, $redis );

$date = strtotime( '2017-05-01' );

//内部发放
$result = $statisticObj->getMonthInnerRechargeList( $date );
print_r( json_encode($result)."\n");

//本月充值总金额
$result = $statisticObj->getTotalRechargeByMonth( $date );

print_r( json_encode($result)."\n");

//本月余额 （减去 上次提现的）
$result = $statisticObj->getMonthBalance( $date );
print_r( json_encode($result)."\n");

//获取当月主播的财产
$result = $statisticObj->getMonthAnchorBalanceList( $date );
//print_r(json_encode($result));


$isCompanyAnchor = function ( $uid ) use ( $db )
{
	$sql = "select uid from anchor where uid=$uid and cid !=0 and cid !=15";
	$res = $db->query( $sql );
	$row = $res->fetch_assoc();

	return $row['uid'];
};

$tmp = [];

foreach ( $result as $key => $value )
{
	if ( ( intval( $value['gb'] ) + intval( $value['gd'] ) ) >= 50 )
	{
		if ( $isCompanyAnchor( $value['uid'] ) )
			//所有大于50RMB （减去上次提现的）
		{
			array_push($tmp,$result[$key]);
//			print_r( $result[$key] );
		}
	}
}

print_r(json_encode($tmp)."\n");

