<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/4/11
 * Time: 下午8:28
 */

include __DIR__ . "/../../include/init.php";
use lib;
use lib\Finance;
use \DBHelperi_huanpeng;
use \RedisHelp;

$redisObj = new RedisHelp();
$db       = new DBHelperi_huanpeng();
$key      = 'financesync';

$redisObj->getMyRedis()->del( $key );

function syncFinanceToRedisPre( $sql, $tabname, $type = 0 )
{
	global $db;
	$res = $db->query( $sql );
	while ( $row = $res->fetch_assoc() )
	{
		syncFinanceToRedis( $row, $tabname, $type );
	}
}

function syncFinanceToRedis( $row, $tabname, $type )
{
	global $redisObj;
	global $key;
	$ctime = strtotime( $row['ctime'] );

	unset( $row['ctime'] );
	asort( $row );

	$id     = $row;
	$member = array(
		'id'    => $id,
		'table' => $tabname,
		'type'  => $type,
		'ctime' => $ctime
	);
	$redisObj->zadd( $key, $ctime, json_encode( $member ) );
}

$stime = '2017-04-01 00:00:00';
	//task
$sql = "select id, ctime from task where status=2 and ctime >= '$stime'";
syncFinanceToRedisPre( $sql, 'task' );

$sql = "select uid,treasureid,ctime from pickTreasure where ctime >= '$stime'";
syncFinanceToRedisPre( $sql, 'pickTreasure' );

$sql = "select `date`,uid,pickid,ctime from pickupHpbean where status=1 and ctime >= '$stime'";
syncFinanceToRedisPre( $sql, 'pickupHpbean' );

$sql = "select id,ctime from giftrecord where ctime >= '$stime'";
syncFinanceToRedisPre( $sql, 'giftrecord' );

$sql = "select id,ctime from billdetail where ctime >= '$stime'";
syncFinanceToRedisPre( $sql, 'billdetail', 1 );

$sql = "select id,ctime from internal_distribution_record where ctime >= '$stime'";
syncFinanceToRedisPre( $sql, 'internal_distribution_record', 2 );

$sql = "SELECT id, ctime from update_nick_record where ctime >='$stime'";
syncFinanceToRedisPre( $sql, "update_nick_record", 3 );



//print_r($redisObj->getMyRedis()->zRange($key, 0,-1));