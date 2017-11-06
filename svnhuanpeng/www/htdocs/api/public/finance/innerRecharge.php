<?php


/**
 * 供后台内部发放调用的方法
 * date 2017-02-24 09:45
 * author yandong@6rooms.com
 */

include '../../../../include/init.php';
use service\event\EventManager;
$db = new DBHelperi_huanpeng();
$requestParam = array( 'uid', 'hpcoin', 'coin', 'hpbean', 'bean', 'activeid', 'desc', 'recordid','tm','sign' );
if( $_POST )
{
	foreach ( $_POST as $k => $v )
	{
		if( !in_array( $k, $requestParam ) )
		{
			echo -4013; //参数错误
		}
	}
	$check=verifySign( $_POST, INNER_SECRET );
	if($check){
		$fobj = new \lib\Finance();
		$res = $fobj->innerRecharge( (int)$_POST['uid'], (int)$_POST['hpcoin'], (int)$_POST['coin'], (int)$_POST['hpbean'], (int)$_POST['bean'], (int)$_POST['activeid'], $_POST['desc'], (int)$_POST['recordid'] );
	}else{
		$res = -1;
	}
	if( is_array( $res ) )
	{
		$Obj = new lib\Anchor( $_POST['uid'] );
		$updateUserHpbean = $Obj->updateUserHpBean( $res['hd'] );
		$updateUserHpcoin = $Obj->updateUserHpCoin( $res['hb'] );
		$updateAnchorBean = $Obj->updateAnchorBean( $res['gd'] );
		$updateAnchorCoin = $Obj->updateAnchorCoin( $res['gb'] );
		if( $updateUserHpbean || $updateUserHpcoin || $updateAnchorBean || $updateAnchorCoin )
		{
			$event = new EventManager();
            $event->trigger(EventManager::ACTION_USER_MONEY_UPDATE,['uid' => (int)$_POST['uid'] ]);
            $event = null;
			echo $res['tid'];
		}
		else
		{
			//记日志 TODO
			echo -1;//更新失败
		}
	}
	else
	{
		echo 0;//发放失败
	}
}
else
{
	echo -4013;//没有参数
}
