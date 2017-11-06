<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/4/11
 * Time: 下午9:21
 */

include __DIR__ . "/../../include/init.php";

use lib;
use lib\Finance;
use \DBHelperi_huanpeng;
use \RedisHelp;


$redis = new RedisHelp();
$db    = new DBHelperi_huanpeng();

//$financeObj = new Finance( $db, $redis, date );

//$list = $financeObj->getTabList();
//unset( $list['rate'] );
//foreach ( $list as $tabname )
//{
//	$db->query( "truncate $tabname" );
//}


function getRechargeInfo_financeSync( $id, $db )
{
	$sql = "select * from recharge_order where id=$id";
	$res = $db->query( $sql );

	return $res->fetch_assoc();
}

$cmd = "php /usr/local/huanpeng/bin/finance/statement.php " . $GLOBALS['env'];

print_r( `$cmd` );
//exit();

$cmd = "php /usr/local/huanpeng/bin/finance/syncData.php " . $GLOBALS['env'];
print_r( `$cmd` );

$key    = 'financesync';
$result = $redis->getMyRedis()->zRange( $key, 0, -1 );

$i = 0;

$curMonth = '';
foreach ( $result as $value )
{
	$i++;
//	echo $i."\n";
//	$db->connect(DBHELPERI_DBW);
	$json = json_decode( $value, true );

	if ( $json['ctime'] < strtotime( '2017-04-01 00:00:00' ) )
	{
		continue;
	}

	if ( !$financeObj )
	{
		$financeObj = new Finance( $db, $redis, date( "Y-m-d H:i:s", $json['ctime'] ) );
	}

	if ( !$curMonth )
	{
		$curMonth = (int)date( "m", $json['ctime'] );
		echo $curMonth . "\n";
	}

	if ( $curMonth != (int)date( "m", $json['ctime'] ) )
	{
		echo $curMonth . "\n";
		$curMonth = (int)date( "m", $json['ctime'] );
		echo $curMonth;
		$financeObj = new Finance( $db, $redis, date( 'Y-m-d H:i:s', $json['ctime'] ) );
	}

	//目前时间还是有问题的
	$financeObj->setCtime( date( 'Y-m-d H:i:s', $json['ctime'] ) );

	if ( $json['type'] == 0 )
	{
		$where = $json['id'];
		$table = $json['table'];
		if ( $table == 'task' )
		{
//			continue;
			$sql         = $db->field( 'uid,getbean' )->where( $where )->select( $table, true );
			$res         = $db->query( $sql );
			$row         = $res->fetch_assoc();
			$bean        = $row['getbean'];
			$id          = $where['id'];
			$eventResult = $financeObj->addUserBean( $row['uid'], $bean, 1, json_encode( array() ), $id );
			if ( !is_array( $eventResult ) )
			{
				echo "task +++++>";
				print_r( $eventResult . " ===== " );
				echo date( 'Y-m-d H:i:s', $json['ctime'] ) . "{$row['uid']} 任务获取欢朋豆失败 " . json_encode( $where ) . "\n";
			}
			unset( $eventResult );
//			$db->disconnect(DBHELPERI_DBW);

		}
		elseif ( $table == 'pickupHpbean' )
		{
//			continue;
			$sql  = $db->field( 'uid,getNum' )->where( $where )->select( $table, true );
			$res  = $db->query( $sql );
			$row  = $res->fetch_assoc();
			$bean = $row['getNum'];
			$id   = 0;
//			print_r($row);
//			print_r($sql);
			$eventResult = $financeObj->addUserBean( $row['uid'], $bean, 2, json_encode( array() ), $id );

			if ( !is_array( $eventResult ) )
			{
				echo "pick up hpbean +++++> ";
				echo $eventResult . " ===== ";
				echo date( 'Y-m-d H:i:s', $json['ctime'] ) . "{$row['uid']} 到时领取欢豆失败 " . json_encode( $where ) . "\n";
			}

			unset( $eventResult );
//			$db->disconnect(DBHELPERI_DBW);

		}
		elseif ( $table == 'pickTreasure' )
		{
//			continue;
			$sql         = $db->field( 'uid, getnum' )->where( $where )->select( $table, true );
			$res         = $db->query( $sql );
			$row         = $res->fetch_assoc();
			$bean        = $row['getnum'];
			$id          = 0;
			$eventResult = $financeObj->addUserBean( $row['uid'], $bean, 3, json_encode( array() ), $id );

			if ( !is_array( $eventResult ) )
			{
				echo "pickTreasure +++++> ";
				echo $eventResult . " ===== ";
				echo date( 'Y-m-d H:i:s', $json['ctime'] ) . "{$row['uid']} 领取宝箱失败 " . json_encode( $where ) . "\n";
			}

			unset( $eventResult );
//			$db->disconnect(DBHELPERI_DBW);
		}
		elseif ( $table == 'giftrecord' )
		{
//			continue;
			$sql  = $db->field( 'uid,luid,giftnum' )->where( $where )->select( $table, true );
			$res  = $db->query( $sql );
			$row  = $res->fetch_assoc();
			$bean = $row['giftnum'];
			$uid  = $row['uid'];
			$luid = $row['luid'];
			$id   = $where['id'];

			$eventResult = $financeObj->sendBean( $uid, $luid, $bean, json_encode( array() ), $id );
			if ( !is_array( $eventResult ) )
			{
				echo "send bean ++++>";
				echo $eventResult . " ===== ";
				echo date( 'Y-m-d H:i:s', $json['ctime'] ) . " {$row['uid']} to {$row['luid']} 送豆失败 " . json_encode( $where ) . "\n";
			}
			unset( $eventResult );
//			$db->disconnect(DBHELPERI_DBW);

		}
	}
	elseif ( $json['type'] == 1 )
	{

		$where = $json['id'];
		$table = $json['table'];
		$sql   = $db->field( "*" )->where( $where )->select( $table, 1 );
		$res   = $db->query( $sql );
		$row   = $res->fetch_assoc();

		$type = $row['type'];
		if ( $type == 0 )
		{
//			continue;
			$suid        = $row['customerid'];
			$ruid        = $row['beneficiaryid'];
			$coin        = $row['purchase'];
			$id          = $row['info'];
			$eventResult = $financeObj->sendGift( $suid, $ruid, $coin, json_encode( array() ), $id );
			if ( !is_array( $eventResult ) )
			{
				echo "send gift +++++> ";
				print_r( $eventResult . " ===== " );
				echo date( 'Y-m-d H:i:s', $json['ctime'] ) . " $suid to $ruid 送礼失败 " . json_encode( $where ) . "\n";
			}
			unset( $eventResult );

		}
		elseif ( $type == 1 )
		{
//			continue;
			$uid = $row['beneficiaryid'];
			$rmb = $row['income'] / 10;
			$id  = $row['info'];

			$rechargeInfo = getRechargeInfo_financeSync( $id, $db );

			if ( !$rechargeInfo )
			{
				echo $id . "\n";
				continue;
			}

			$channel     = $rechargeInfo['channel'];
			$client      = $rechargeInfo['client'];
			$refURL      = $rechargeInfo['refer_url'];
			$promotionID = $rechargeInfo['promation_id'];
			$desc        = json_encode( array() );
			$otid        = $rechargeInfo['id'];
			$orderid     = $rechargeInfo['order_id'];
			$ip          = $rechargeInfo['ip'];
			$port        = $rechargeInfo['port'];

			if ( $financeObj->rechargeOrderCreate( $uid, $rmb, $channel, $client, $refURL, $promotionID, $desc, $otid, $orderid, $ip, $port ) )
			{
				//todo 同步IP PORT
				$transitionID = $rechargeInfo['thrid_order_id'];
				$openid       = $rechargeInfo['thrid_buyer_id'];

				$eventResult = $financeObj->rechargeOrderFinish( $transitionID, $orderid, $openid );
				if ( !is_array( $eventResult ) )
				{
					echo "order finish +++++> ";
					print_r( $eventResult . " =====" );
					echo date( 'Y-m-d H:i:s', $json['ctime'] ) . "$uid:$orderid 充值失败" . json_encode( $where ) . "\n";
				}
				unset( $eventResult );
			}
			else
			{
				echo "order create +++++> ";
				echo date( 'Y-m-d H:i:s', $json['ctime'] ) . "$uid:$orderid 创建订单失败 " . json_encode( $where ) . "\n";
			}
		}
		elseif ( $type == 3 )
		{

//			continue;

			$uid  = $row['customerid'];
			$coin = $row['purchase'];
			$id   = $row['info'];

			if ( $uid == 1860 && ( $row['income'] / $coin ) == 6 )
			{
				continue;
			}

			$eventResult = $financeObj->exchange( $uid, $coin, json_encode( array() ), $id );
			if ( !is_array( $eventResult ) )
			{
				echo "exchange +++++> ";
				print_r( $eventResult . " ===== " );
				echo date( 'Y-m-d H:i:s', $json['ctime'] ) . "$uid to  兑换失败 " . json_encode( $where ) . "\n";
			}
			unset( $eventResult );
		}
	}
	elseif ( $json['type'] == 2 )
	{
		$where = $json['id'];
		$table = $json['table'];
		$sql   = $db->field( "*" )->where( $where )->select( $table, 1 );

		$res               = $db->query( $sql );
		$innerRechargeInfo = $res->fetch_assoc();
		if ( !$innerRechargeInfo )
		{
			echo $json['id']['id'] . "\n";
			continue;
		}

		$uid  = $innerRechargeInfo['uid'];
		$hb   = $innerRechargeInfo['hpcoin'];
		$gb   = $innerRechargeInfo['coin'];
		$hd   = $innerRechargeInfo['hpbean'];
		$gd   = $innerRechargeInfo['bean'];
		$otid = $innerRechargeInfo['id'];


		$desc = array( 'activeid' => $innerRechargeInfo['activeid'], 'type' => $innerRechargeInfo['type'] );
		$desc = json_encode( $desc );

		$eventResult = $financeObj->innerRecharge( $uid, $hb, $gb, $hd, $gd, $innerRechargeInfo['activeid'], $desc, $otid );

		if ( !is_array( $eventResult ) )
		{
			echo "inner recharge +++++> ";
			print_r( $eventResult . " ===== " );
			echo date( 'Y-m-d H:i:s', $json['ctime'] ) . "$uid to  内部充值失败 " . json_encode( $where ) . "\n";
		}
	}
	elseif ( $json['type'] == 3 )
	{
		$where = $json['id'];
		$table = $json['table'];

		$sql = $db->field( "*" )->where( $where )->select( $table, 1 );

		$res         = $db->query( $sql );
		$modifyNick  = $res->fetch_assoc();
		$desc        = "修改昵称，消费600";
		$eventResult = $financeObj->costUserHb( $modifyNick['uid'], 600, Finance::COST_HB_CHANNEL_NICK, $desc, $modifyNick['id'] );
		if ( !is_array( $eventResult ) )
		{
			echo "cost user hpcoin failed +++++>";
			echo $eventResult . " ====== ";
		}
	}
	elseif ( $json['type'] == 4 )
	{
		$where = $json['id'];
		$table = $json['table'];
		$sql   = $db->field( "*" )->where( $where )->select( $table, 1 );
		$res   = $db->query( $sql );

		$row = $res->fetch_assoc();
//		$desc
	}
}


$sql = "select * from (
select useractive.uid as cuid,
hb/1000 as hb,
useractive.hpcoin as chb,
gb/1000 as gb,
case  when anchor.cid=15 then anchor.coin/20*1.2 when anchor.cid=0 then anchor.coin/20*1.2 else anchor.coin/20*1.4 end as cgb, 
hd/1000 as hd,
useractive.hpbean as chd,
gd, 
anchor.bean as cgd,cid
from hpf_balance,anchor,useractive 
where useractive.uid=hpf_balance.uid and useractive.uid=anchor.uid 
) as result where (hb != chb or gb != cgb or hd != chd or gd != cgd )";

$res = $db->query( $sql, DBHELPERI_DBW );
$tid = date( "Ymd" ) . '235959';
while ( $row = $res->fetch_assoc() )
{
	$hb   = $row['chb'] - $row['hb'];
	$hd   = $row['chd'] - $row['hd'];
	$gb   = $row['cgb'] - $row['gb'];
	$gd   = $row['cgd'] - $row['gd'];
	$gd   = $gd / 1000;
	$desc = '系统同步发放';
	$financeObj->innerRecharge( $row['cuid'], $hb, $gb, $hd, $gd, 0, $desc, $tid );
}

//返回值可能有问题，需要确定
//明天跑完数据，然后对比当前数据库
//按照时间走

