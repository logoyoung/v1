<?php
include_once '/usr/local/huanpeng/include/init.php';

$port = 0;
$ip = fetch_real_ip($port);


function getIP_test()
{
	$cip = "unknow";
	if( $_SERVER[ "REMOTE_ADDR" ] )
	{
		$cip = $_SERVER["REMOTE_ADDR"];
	}
	elseif( getenv("REMOTE_ADDR") )
	{
		$cip = getenv("REMOTE_ADDR");
	}

	return $cip;
}

var_dump($ip);

var_dump(getIP_test());


