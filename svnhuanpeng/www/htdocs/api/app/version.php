<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/5/31
 * Time: 上午10:11
 */

include '../../../include/init.php';
$redis = new redishelp();

$versionList = array(
	'105' => 1,//'版本'=>array('是否强制更新'=>0)  0否 1是
	'107' => 1,
	'108' => 1
);


$version = isset( $_POST['version'] ) ? intval( $_POST['version'] ) : 0;
$channel = isset( $_POST['channel'] ) ? intval( $_POST['channel'] ) : 0;

$app = new \service\app\AppUpdateService( $channel, $version );

$app->setForcedVersionList( $versionList );

$app->display();


//$param   = isset( $_POST['version'] ) ? (int)$_POST['version'] : 0;
//$version = getApkVersion( $redis );
//$url     = WEB_ROOT_URL . "api/app/download.php";
//$list    = array();
//
//
//if ( $version['version'] > $param )
//{
//	foreach ( $versionList as $k => $v )
//	{
//		if ( $k > $param )
//		{
//			array_push( $list, $v );
//		}
//	}
//
//	$l = array_sum( $list );
//
//	if ( $l > 0 )
//	{
//		$isMostUp = 1;
//	}
//	else
//	{
//		$isMostUp = 0;
//	}
//}
//else
//{
//	$isMostUp = 0;
//}
//
//$array = array(
//	'url'          => $url,
//	'version'      => $version['version'],
//	"version_name" => $version['name'],
//	"version_desc" => $version['desc'],
//	"isMustUp"     => $isMostUp
//);
//succ( $array );
