<?php

/**
 * 供后台改变比率时调用的方法
 * date 2017-02-24 09:45
 * author yandong@6rooms.com
 */
include '../../../../include/init.php';

$requestParam = array( 'list', 'rate', 'desc', 'sign', 'tm' );

if ( $_POST )
{
	foreach ( $_POST as $k => $v )
	{
		if ( !in_array( $k, $requestParam ) )
		{
			echo -4013; //参数错误
		}
	}
	$res = signCheck( $_POST, RATE_SECRET );
	if ( $res )
	{
		$fobj = new \lib\Finance();

		$changeDueRate = $fobj->setRate( $_POST['list'], filterWords( $_POST['rate'] ), filterWords( $_POST['desc'] ), lib\Finance::EXC_DUE );

		echo $res;
	}
	else
	{
		echo -1;
	}
}
else
{
	echo -4013;//没有参数
}
