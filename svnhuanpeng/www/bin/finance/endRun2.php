<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/23
 * Time: 上午9:38
 */


include __DIR__ . "/../../include/init.php";
use lib\Finance;
use lib\AnchorExchange;

$redis = new \RedisHelp();
$db    = new \DBHelperi_huanpeng();

$financeObj = new Finance( $db, $redis );

function syncToUser_property( $uid, $bean, $coin, $db )
{
	$sql = "update useractive set hpbean=$bean, hpcoin=$coin where uid=$uid";

	var_dump("sql $sql doResult").$db->query( $sql );
}

function syncToAnchor_property( $uid, $bean, $coin, $db )
{
	$sql = "update anchor set coin=$coin,bean=$bean where uid=$uid";
	$db->query( $sql );

	var_dump("sql $sql doResult").$db->query( $sql );
}

$sql = "select * from hpf_balance";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$hpbean = $financeObj->getOutputNumber( $row['hd'] );
	$hpcoin = $financeObj->getOutputNumber( $row['hb'] );
	syncToUser_property( $row['uid'], $hpbean, $hpcoin, $db );


	$coin = $financeObj->getOutputNumber( $row['gb'] );
	$bean = $financeObj->getOutputNumber( $row['gd'] );
	syncToAnchor_property( $row['uid'], $bean, $coin, $db );
}