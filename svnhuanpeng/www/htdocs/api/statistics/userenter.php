<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/22
 * Time: 上午10:20
 */

/** record  is user first enter room */

include '../../../include/init.php';


$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int)$_POST['luid'] : 0;

$db = new DBHelperi_huanpeng();

if ( empty( $uid ) || empty( $encpass ) || empty( $luid ) )
{
	error2( -4013 );
}

$uid = checkInt( $uid );
$luid = checkInt( $luid );
$encpass = checkStr( $encpass );

$s = CheckUserIsLogIn( $uid, $encpass, $db );

if ( true !== $s )
{
	error2( -4067 );
}

$isUserHasRecord = function ( $uid ) use ( $db )
{
	$sql = "select `enterroom` from `useractive` where uid = $uid";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	return (int)$row['enterroom'];
};

$updateUserEnterStatus = function( $uid, $luid ) use ( $db )
{
	$sql = "UPDATE `useractive` SET `enterroom` = $luid where `uid` = $uid";
	$res = $db->query( $sql );

	return $res;
};

if( $isUserHasRecord( $uid ) > 0 )
{
	succ();
}

if( true == $updateUserEnterStatus( $uid, $luid))//ENTER_ROOM_STATUS ) )
{
	succ();
}
else
{
	error2( -1007 );
}