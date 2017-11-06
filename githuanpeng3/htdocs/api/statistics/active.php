<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/19
 * Time: 下午3:13
 */

include "../../../include/init.php";

$statistidLog = LOG_DIR . "userActive/login-" . date( "Ymd" ) . "log";
$logDir       = dirname( $statistidLog );

if ( !is_dir( $logDir ) )
{
	mkdir( $logDir, 0777, true );
}

$data = $_POST;

$data['ctime'] = date( "Y-m-d H:i:s" );

$port = 0;
$ip   = fetch_real_ip( $ip, $port );

$data['ip']   = $ip;
$data['port'] = $port;

file_put_contents( $STATISTICS_LOG, json_encode($data)."\n", FILE_APPEND );

succ();