<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/1
 * Time: 11:53
 */
error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );
include '/usr/local/huanpeng/include/init.php';

use lib\Finance;
use \RedisHelp;

$whiteList = "(" . WHITE_LIST . ")";
$endTime   = '2017-04-01 00:00:00';
$db        = new DBHelperi_huanpeng();

$anchor = array();
$user   = array();


$rateCompany = 20 / 1.4;
$rateUser    = 20 / 1.2;


$link = "<";

function getCompanyUserIDMap( $db )
{
	$sql    = "select uid, cid,`name` from anchor,company where cid != 0 and cid !=15 and cid=company.id";
	$sql    = "select uid,cid from anchor where cid !=0 and cid !=15";
	$res    = $db->query( $sql );
	$result = array();
	while ( $row = $res->fetch_assoc() )
	{
		$result[$row['uid']]['cid']     = $row['cid'];
		$result[$row['uid']]['company'] = $row['name'];
	}

	return $result;
}

function updateUserProperty( $uid, $coin, $bean, &$user )
{
	if( !$user[$uid] || !is_array( $user[$uid] ) )
	{
		$user[$uid] = array(
			'bean' => 0,
			'coin' => 0
		);
	}
	$user[$uid]['bean'] += $bean;
	$user[$uid]['coin'] += $coin;
//	print_r(json_encode($user));
}

function updateAnchorProperty( $uid, $coin, $bean, &$anchor )
{
	if( !$anchor[$uid] || !is_array( $anchor[$uid] ) )
	{
		$anchor[$uid] = array(
			'bean' => 0,
			'coin' => 0
		);
	}

	$anchor[$uid]['bean'] += $bean;
	$anchor[$uid]['coin'] += $coin;
//	print_r(json_encode($anchor));
}

function getUserNick_statement( $uid, $db )
{
	$sql = "select nick from userstatic where uid =$uid";
	$res = $db->query( $sql );
	$row = $res->fetch_assoc();

	return $row['nick'];
}

$companyAnchorList = getCompanyUserIDMap( $db );

//充值
$sql = "select beneficiaryid, SUM(income) as income from billdetail where ctime $link '$endTime' and beneficiaryid not in $whiteList and `type`=1 GROUP BY beneficiaryid";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid  = $row['beneficiaryid'];
	$coin = $row['income'];
	$bean = 0;
	updateUserProperty( $uid, $coin, $bean, $user );
}

unset( $row );

//收益
$sql = "select beneficiaryid, sum(income) as income from billdetail where ctime $link '$endTime' and beneficiaryid not in $whiteList and `type`=0 GROUP BY beneficiaryid";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid  = $row['beneficiaryid'];
	$coin = $row['income'];
	$bean = 0;
	updateAnchorProperty( $uid, $coin, $bean, $anchor );
}
unset( $row );

//送礼
$sql = "select customerid, sum(purchase) as purchase from billdetail where ctime $link '$endTime' and customerid not in $whiteList and `type`=0 GROUP BY customerid";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid  = $row['customerid'];
	$coin = -(int)$row['purchase'];
	$bean = 0;
	updateUserProperty( $uid, $coin, $bean, $user );
}
unset( $row );

//兑换欢朋币数量
$sql = "select beneficiaryid, sum(income) as income, sum(purchase) as purchase from billdetail where ctime $link '$endTime' and beneficiaryid not in $whiteList and `type`=3 GROUP BY beneficiaryid";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid      = $row['beneficiaryid'];
	$income   = $row['income'];
	$purchase = $row['purchase'];
	if( $uid == 1860 )
	{
		$income -= 420;
//		$purchase -= 70;
	}
	if( $companyAnchorList[$uid] != 0 )
	{
		$purchase = -abs( $purchase * 20 );
	}
	else
	{
		$purchase = -abs( $purchase * 20 );
	}
//	$purchase = -abs($purchase * 20/1.2);
	updateAnchorProperty( $uid, $purchase, 0, $anchor );
	updateUserProperty( $uid, $income, 0, $user );
}
unset( $row );

//到时领取欢朋豆数量
$sql = "select uid, sum(getNum) as income from pickupHpbean where ctime $link '$endTime'  group by uid";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
//	echo "+++++++++++++++" . json_encode( $row ) . "++++++++++++++++++++\n";
	$uid  = $row['uid'];
	$bean = $row['income'];
	if( $row['uid'] == 15445 )
	{
		echo "=============$bean\n";
	}
	$coin = 0;
	updateUserProperty( $uid, $coin, $bean, $user );
}


//领取宝箱所得欢朋豆
$sql = "select uid, sum(getNum) as income from pickTreasure where ctime  $link '$endTime' group by uid";//and treasureid in (select id from treasurebox where uid not in $whiteList)";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid  = $row['uid'];
	$bean = $row['income'];
	$coin = 0;
	updateUserProperty( $uid, $coin, $bean, $user );
}

//送豆
$sql = "select uid, sum(giftnum) as purchase from giftrecord where ctime $link '$endTime' group by uid";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid  = $row['uid'];
	$bean = -intval( $row['purchase'] );
	$coin = 0;
	updateUserProperty( $uid, $coin, $bean, $user );
}
unset( $row );

//收豆
$sql = "select luid, sum(giftnum) as income from giftrecord where ctime $link '$endTime' group by luid";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid  = $row['luid'];
	$bean = $row['income'];
	$coin = 0;
	updateAnchorProperty( $uid, $coin, $bean, $anchor );
}
unset( $row );

//做任务获取欢朋豆
$sql = "select sum(getbean) as income, uid from task where ctime $link '$endTime' and status=2 group by uid";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid  = $row['uid'];
	$bean = $row['income'];
	$coin = 0;
	updateUserProperty( $uid, $coin, $bean, $user );
}

//反推逻辑

//4月以后内部发放
//$sql = "select * from internal_distribution_record where ctime $link '$etimeme'";
//$res = $db->query($sql);
//while ($row = $res->fetch_assoc())
//{
//	$uid = $row['uid'];
//	$hpbean = $row['hpbean'];
//	$hpcoin  = $row['hpcoin'];
//	$bean = $row['bean'];
//	$coin = $row['coin'];
//
//	updateUserProperty($uid,$hpcoin,$hpbean,$user);
//	updateAnchorProperty($uid,$coin,$bean,$anchor);
//}
//
//
//$intoFinance = array();
//
//$sql = "select uid,coin,bean from anchor";
//$res = $db->query($sql);
//while ($row = $res->fetch_assoc())
//{
//	$uid = $row['uid'];
//	if(is_array($anchor[$uid]))
//	{
//		$intoFinance[$uid]['gb'] = $row['coin'] - $anchor[$uid]['coin'];
//		$intoFinance[$uid]['gd'] = $row['bean'] - $anchor[$uid]['bean'];
//	}
//
//}
//
//$sql = "select uid,hpcoin,hpbean from useractive";
//$res = $db->query($sql);
//while($row = $res->fetch_assoc())
//{
//	$uid = $row['uid'];
//	if(is_array($user[$uid]))
//	{
//		$intoFinance[$uid]['hb'] = $row['hpcoin'] - $user[$uid]['hpcoin'];
//		$intoFinance[$uid]['hd'] = $row['hpbean'] - $user[$uid]['hpbean'];
//	}
//}
//
//foreach ($intoFinance as $uid => $value)
//{
//	if (  (int)$companyAnchorList[$uid] == 0 )
//	{
//		$intoFinance[$uid]['gb'] = (float)$intoFinance[$uid]['gb'] / 20 * 1.2;
//
//	}else
//	{
//		$intoFinance[$uid]['gb'] = (float)$intoFinance[$uid]['gb'] / 20 * 1.4;
//
//	}
//	$intoFinance[$uid]['gd'] = (float)$intoFinance[$uid]['gd'] / 1000;
//	$intoFinance[$uid]['hb'] = (float)$intoFinance[$uid]['hb'];
//	$intoFinance[$uid]['hd'] = (float)$intoFinance[$uid]['hd'];
//}
//
////print_r($intoFinance);
////exit();
//
//$financeObj = new Finance( $db, $redisObj );
//
//foreach ( $intoFinance as $uid => $property )
//{
//	$desc = json_encode( array( "3月及其以前余额同步" ) );
//	$otid = "201703315959";
//	foreach ( $property as $key => $value )
//	{
//		if ( $value < 0 )
//		{
//			$property[$key] = 0;
//			echo "uid:$uid" . json_encode( $property ) . "\n";
//		}
//	}
//	if ( $uid == 9100 )
//	{
////		$property['hb'] = 0;
//	}
////	if ( $uid == 2290 )
////	{
////		$property['hb'] -= 600;//改名
////	}
////	if ( $uid == 2095 )
////	{
////		$property['hb'] -= 2400;//改名
////	}
//
//
//	$financeObj->setCtime( '2017-04-01 00:00:00' );
//	$financeObj->innerRecharge( $uid, $property['hb'], $property['gb'], $property['hd'], $property['gd'], 0, $desc, $otid );
//}
//
//exit();

//内部发放
$inner = array(
	'type' => 'inner',
	'desc' => '内部发放',
	'list' => array(
		'2275'  => array( array( 'coin' => '50000', 'bean' => '500000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'2040'  => array( array( 'coin' => '20000', 'bean' => '200000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'2105'  => array( array( 'coin' => '30000', 'bean' => '300000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'2055'  => array( array( 'coin' => '20000', 'bean' => '200000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'2095'  => array( array( 'coin' => '20000', 'bean' => '200000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'2100'  => array( array( 'coin' => '30000', 'bean' => '300000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'2225'  => array( array( 'coin' => '30000', 'bean' => '300000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'2075'  => array( array( 'coin' => '50000', 'bean' => '500000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'2070'  => array( array( 'coin' => '50000', 'bean' => '500000', 'ctime' => '2017-02-08 14:11:30' ) ),
		'3445'  => array(
			array( 'coin' => '5000', 'bean' => '30000', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '20000', 'bean' => '20000', 'ctime' => '2017-03-24 09:40:23' )
		),
		'26260' => array( array( 'coin' => '10000', 'bean' => '10000', 'ctime' => '2017-03-24 09:40:23' ) ),
		'19510' => array( array( 'coin' => '30000', 'bean' => '30000', 'ctime' => '2017-03-24 09:40:23' ) ),
		'15460' => array( array( 'coin' => '10000', 'bean' => '10000', 'ctime' => '2017-03-24 09:40:23' ) ),
		'13325' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'13345' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'13795' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'14570' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'15035' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'15040' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'15045' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'15050' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'15060' => array( array( 'coin' => '1000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'3580'  => array( array( 'coin' => '3000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'3905'  => array( array( 'coin' => '3000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'3640'  => array( array( 'coin' => '3000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'3900'  => array( array( 'coin' => '3000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'3915'  => array( array( 'coin' => '3000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'2175'  => array( array( 'coin' => '3000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) ),
		'14035' => array( array( 'coin' => '3000', 'bean' => '0', 'ctime' => '2017-03-09 10:29:23' ) )
	)
);


$task = array(
	'type' => 'task',
	'desc' => '直播有效时长奖励',
	'list' => array(
		'22060' => array( array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-31 14:22:43' ) ),
		'13460' => array( array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-31 14:22:43' ) ),
		'2290'  => array(
			array( 'coin' => '100', 'bean' => '1000', 'ctime' => '2017-03-31 14:22:43' ),
			array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-23 18:06:20' ),
			array( 'coin' => '200', 'bean' => '0', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '500', 'bean' => '0', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '0', 'bean' => '6000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'4000'  => array( array( 'coin' => '100', 'bean' => '1000', 'ctime' => '2017-03-31 14:22:43' ) ),
		'24895' => array( array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-31 14:22:43' ) ),
		'24505' => array( array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-31 14:22:43' ) ),
		'25055' => array( array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-31 14:22:43' ) ),
		'24745' => array( array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-31 14:22:43' ) ),
		'17065' => array( array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-30 14:37:20' ) ),
		'3415'  => array(
			array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-30 14:37:20' ),
			array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-23 18:06:20' ),
			array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'18075' => array( array( 'coin' => '100', 'bean' => '1000', 'ctime' => '2017-03-30 14:37:20' ) ),
		'11735' => array( array( 'coin' => '100', 'bean' => '1000', 'ctime' => '2017-03-30 14:37:20' ) ),
		'4675'  => array(
			array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-30 14:37:20' ),
			array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-23 18:06:20' )
		),
		'12515' => array( array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-30 14:37:20' ) ),
		'13565' => array( array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-30 14:37:20' ) ),
		'5215'  => array( array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-30 14:37:20' ) ),
		'21905' => array( array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-29 11:06:20' ) ),
		'4565'  => array(
			array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-29 11:06:20' ),
			array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'9460'  => array( array( 'coin' => '100', 'bean' => '1000', 'ctime' => '2017-03-29 11:06:20' ) ),
		'14445' => array( array( 'coin' => '100', 'bean' => '1000', 'ctime' => '2017-03-29 11:06:20' ) ),
		'3630'  => array(
			array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-29 11:06:20' ),
			array( 'coin' => '100', 'bean' => '1000', 'ctime' => '2017-03-23 18:06:20' ),
			array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'3635'  => array(
			array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-29 11:06:20' ),
			array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-29 11:06:20' ),
			array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'8815'  => array( array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-29 11:06:20' ) ),
		'4410'  => array(
			array( 'coin' => '0', 'bean' => '1000', 'ctime' => '2017-03-29 11:06:20' ),
			array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'3490'  => array(
			array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-23 18:06:20' ),
			array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '200', 'bean' => '0', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '0', 'bean' => '6000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'4380'  => array(
			array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-23 18:06:20' ),
			array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'4465'  => array(
			array( 'coin' => '100', 'bean' => '1000', 'ctime' => '2017-03-23 18:06:20' ),
			array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'9100'  => array( array( 'coin' => '200', 'bean' => '0', 'ctime' => '2017-03-13 14:08:21' ) ),
		'2625'  => array(
			array( 'coin' => '5000', 'bean' => '50000', 'ctime' => '2017-02-23 18:00:00' ),
			array( 'coin' => '500', 'bean' => '0', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '200', 'bean' => '0', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '0', 'bean' => '6000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'3055'  => array(
			array( 'coin' => '200', 'bean' => '0', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-03 18:09:20' ),
			array( 'coin' => '0', 'bean' => '6000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'3430'  => array(
			array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-23 18:06:20' ),
			array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'5285'  => array(
			array( 'coin' => '100', 'bean' => '0', 'ctime' => '2017-03-23 18:06:20' ),
			array( 'coin' => '0', 'bean' => '6000', 'ctime' => '2017-03-03 18:09:20' )
		),
		'3700'  => array( array( 'coin' => '0', 'bean' => '6000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4245'  => array( array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4260'  => array( array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'2780'  => array( array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'8485'  => array( array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'7930'  => array( array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'3250'  => array( array( 'coin' => '0', 'bean' => '4000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'3710'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'2225'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'3100'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'7945'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4240'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4655'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'2105'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'1860'  => array( array( 'coin' => '420', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4530'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4295'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'3070'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'3505'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'3685'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4310'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4140'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) ),
		'4160'  => array( array( 'coin' => '0', 'bean' => '2000', 'ctime' => '2017-03-03 18:09:20' ) )
	)
);

foreach ( $inner['list'] as $uid => $value )
{
	foreach ( $value as $property )
	{
		$coin = $property['coin'];
		$bean = $property['bean'];
		$innerSend['bean'] += $property['bean'];
		$innerSend['coin'] += $property['coin'];
		updateUserProperty( $uid, $coin, $bean, $user );
	}
}

foreach ( $task['list'] as $uid => $value )
{
	foreach ( $value as $property )
	{
		$coin = $property['coin'];
		$bean = $property['bean'];
		$innerSend['bean'] += $property['bean'];
		$innerSend['coin'] += $property['coin'];
		updateUserProperty( $uid, $coin, $bean, $user );
	}
}


$intoFinance = array();

foreach ( $user as $uid => $value )
{
	$intoFinance[$uid]['hb'] = $value['coin'];
	$intoFinance[$uid]['hd'] = $value['bean'];
	$intoFinance[$uid]['gb'] = 0;
	$intoFinance[$uid]['gd'] = 0;
	if( isset( $anchor[$uid] ) )
	{
		if( (int)$companyAnchorList[$uid] == 0 )
		{
			$intoFinance[$uid]['gb'] = $anchor[$uid]['coin'] / 20 * 1.2;
		}
		else
		{
			$intoFinance[$uid]['gb'] = $anchor[$uid]['coin'] / 20 * 1.4;
		}

		$intoFinance[$uid]['gd'] = $anchor[$uid]['bean'] / 1000;
	}
}

$redisObj = new RedisHelp();

//$db->debug(true);

$financeObj = new Finance( $db, $redisObj, '2017-03-31 23:59:59' );

var_dump( count( $intoFinance ) );

foreach ( $intoFinance as $uid => $property )
{
	$desc = json_encode( array( "3月及其以前余额同步" ) );
	$otid = "201703315959";
	foreach ( $property as $key => $value )
	{
		if( $value < 0 )
		{
			echo "uid:$uid" . json_encode( $property ) . "\n";
			$property[$key] = 0;
		}
	}
	if( $uid == 9100 )
	{
//		$property['hb'] = 0;
	}
	if( $uid == 2290 )
	{
		$property['hb'] -= 600;//改名
	}
	if( $uid == 2095 )
	{
		$property['hb'] -= 2400;//改名
	}

	$financeObj->setCtime( '2017-03-31 23:59:59' );
	$financeObj->innerRecharge( $uid, $property['hb'], $property['gb'], $property['hd'], $property['gd'], 0, $desc, $otid );
}

exit();
$sql = "select uid,hpcoin, hpbean from useractive";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid                  = $row['uid'];
	$bean                 = $row['hpbean'];
	$coin                 = $row['hpcoin'];
	$user[$uid]['hpbean'] = $bean;
	$user[$uid]['hpcoin'] = $coin;
}

unset( $row );

$sql = "select uid, coin, bean from anchor";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid  = $row['uid'];
	$bean = $row['bean'];
	$coin = $row['coin'];

	$anchor[$uid]['hpbean'] = $bean / 1000;
	$anchor[$uid]['hpcoin'] = $coin;
}

echo "uid \t";
echo "用户昵称\t";
echo "可兑换金豆数量\t";
echo "可兑换金币数量\t";
echo "兑换总额\t";
echo "公司名称\t";
echo "兑换比例\t\n";


$monthAnchor = array();
$monthUser   = array();

$withdraw = array();

foreach ( $anchor as $key => $value )
{
	$uid    = $key;
	$hpbean = floor( $value['hpbean'] );

	$bean = $value['bean'];
	$nick = getUserNick_statement( $uid, $db );
	if( !empty( $companyAnchorList[$uid] ) || $companyAnchorList[$uid]['cid'] != 15 )
	{
		$hpcoin = floor( $value['hpcoin'] / $rateCompany );
		$coin   = $value['coin'] / $rateCompany;
		$total  = floor( $bean ) + floor( $coin );
		if( $total > 50 )
		{
			echo "$uid\t";
			echo "$nick\t";
			echo floor( $bean ) . "\t";
			echo floor( $coin ) . "\t";
			echo "$total \t";
			echo "{$companyAnchorList[$uid]['company']}\t";
			echo "0.7\t\n";
			$withdraw['coin'] += floor( $coin );
			$withdraw['bean'] += floor( $bean );
		}
	}
	elseif( $companyAnchorList[$uid]['cid'] == 15 )
	{
		$hpcoin = floor( $value['hpcoin'] / $rateCompany );
		$coin   = $value['coin'] / $rateUser;
		$total  = floor( $bean ) + floor( $coin );
		if( $total > 90 )
		{
			$companyName = '';
			if( !empty( $companyAnchorList[$uid] ) && $companyAnchorList[$uid]['cid'] == 15 )
			{
				$companyName = $companyAnchorList[$uid]['company'];
			}
			echo "$uid\t";
			echo "$nick\t";
			echo floor( $bean ) . "\t";
			echo floor( $coin ) . "\t";
			echo "$total \t";
			echo "$companyName \t";
			echo "0.6\t\n";
			$withdraw['coin'] += floor( $coin );
			$withdraw['bean'] += floor( $bean );
		}
	}
	else
	{
		$coin = $value['coin'] / $rateUser;
	}

//	$coin = $value['coin']/20 *1.2;
//	$bean = $value['bean'];
//	echo sprintf("%4d %6d %6d %6d %6d", $uid, $hpbean, $bean, $hpcoin, $coin)."\n";
//	$total = $bean + $coin;

	$monthAnchor['coin'] += $coin;
	$monthAnchor['bean'] += $bean;
}

foreach ( $user as $key => $value )
{

	$coin = $value['coin'];
	$bean = $value['bean'];

	$monthUser['coin'] += $coin;
	$monthUser['bean'] += $bean;
}

$monthAnchor['coin'] = $monthAnchor['coin'] - $withdraw['coin'];
$monthAnchor['bean'] = $monthAnchor['bean'] - $withdraw['bean'];

//echo "主播金币\t主播金豆\t用户欢朋币\t用户欢朋豆\t\n";
//echo "{$monthAnchor['coin']}\t{$monthAnchor['bean']}\t{$monthUser['coin']}\t{$monthUser['bean']}\t\n";

$recharge = array();

$sql = "select * from recharge_order where ctime < '$endTime' and uid NOT IN $whiteList and status=1 ";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$uid     = $row['uid'];
	$coin    = $row['quantity'];
	$rmb     = $row['total_price'] / 100;
	$channel = $row['channel'];
	$recharge[$uid][$channel]['rmb'] += $rmb;
	$recharge[$uid][$channel]['quantity'] += $coin;
}

//echo "uid\t支付宝\t微信\t\n";
foreach ( $recharge as $key => $value )
{
	$uid = $key;
//	echo "$uid\t";
//	echo (int)$value['alipay']['rmb']."\t";
//	echo (int)$value['wechat']['rmb']."\t\n";
}

//echo "内部发放欢朋币数量\t内部发放欢朋豆数量\t\n";
//echo "{$innerSend['coin']}\t{$innerSend['bean']}\t\n";


