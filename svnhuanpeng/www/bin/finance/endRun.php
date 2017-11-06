<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/9
 * Time: 下午9:53
 */

include __DIR__ . "/../../include/init.php";
use lib\Finance;
use lib\AnchorExchange;

$redis = new \RedisHelp();
$db    = new \DBHelperi_huanpeng();

$financeObj = new Finance( $db, $redis );

function syncToUser_property( $uid, $bean, $coin, $db )
{
	$sql = "update useractive set hpbean=$bean,set hpcoin=$coin where uid=$uid";
	$db->query( $sql );
}

function syncToAnchor_property( $uid, $bean, $coin, $db )
{
	$sql = "update anchor set coin=$coin,bean=$bean where uid=$uid";
	$db->query( $sql );
}

function sync_withdraw_record( $data, $ctime, $etime, $db, $redis )
{
	$financeObj = new Finance( $db, $redis );

	foreach ( $data as $info )
	{
		$uid  = $info[0];
		$gd   = $info[2];
		$gb   = $info[4];
		$rate = $info[6];

		if ( (int)date( 'm', strtotime( $ctime ) ) == 5 )
		{
			$gb = intval( $gb );
			$gd = $info[3];
		}

		$data['otid']   = strtotime( $ctime ) . "" . rand( 10000, 99999 ) . "" . rand( 1000, 9999 );
		$data['uid']    = $uid;
		$data['type']   = Finance::EXC_GD_GB;
		$data['status'] = AnchorExchange::EXCHANGE_STATUS_03;
		$data['ctime']  = $ctime;
		$data['number'] = $gd;

		$anchorExchange = new AnchorExchange( $uid, '', $db );
		if ( $gd )
		{
			if ( $anchorExchange->insert( $data ) )
			{
				$desc   = [
					'title' => "$ctime 提现，金豆兑换金币",
					'otid'  => $data['otid']
				];
				$result = $financeObj->excGD2GB( $uid, $gd, json_encode( $desc ), $data['otid'] );
				if ( Finance::checkBizResult( $result ) )
				{

					if ( !$anchorExchange->update( $data['otid'], [ 'tid' => $result['tid'] ] ) )
					{
						echo "$uid  $ctime update  {$result['tid']} failed\n";
						continue;
					}
					//todo log
				}
				else
				{
					echo "$uid $ctime {$result['errno']}Finance excGd2Gb failed.\n";
					continue;
				}
			}
			else
			{
				echo "$uid $ctime insert gd =>gb  failed";
				continue;
			}
		}

		unset( $data );

		if ( (int)date( 'm', strtotime( $ctime ) ) == 5 )
		{
			$gb = intval( $gb );
		}

		$data['otid']   = strtotime( $ctime ) . "" . rand( 10000, 99999 ) . "" . rand( 1000, 9999 );
		$data['uid']    = $uid;
		$data['type']   = Finance::EXC_GB_RMB;
		$data['number'] = $gb;
		$data['status'] = AnchorExchange::EXCHANGE_STATUS_01;

		if ( $anchorExchange->insert( $data ) )
		{
			$result = $financeObj->withdraw( $uid, $gb, json_encode( $desc ), $data['otid'] );
			if ( Finance::checkBizResult( $result ) )
			{
				if ( !$anchorExchange->update( $data['otid'], [ 'utime' => $etime, 'status' => AnchorExchange::EXCHANGE_STATUS_03 ] ) )
				{
					echo "$uid $ctime anchor update failed";
					continue;
				}
				else
				{
					echo "$uid $ctime SUCCESS \n\n";
					continue;
				}
			}
			else
			{
				echo "$uid $ctime {$result['errno']} Finance exchange success and  withdraw failed \n";
				continue;
			}
		}
		else
		{
			echo "$uid $ctime insert exchange success and  withdraw failed \n";
			continue;
		}


//		if ( $anchorExchange->insert( $data ) )
//		{
//			$desc = [
//				'title' => "$ctime 提现，金豆兑换金币",
//				'otid'  => $data['otid']
//			];
//
//			$result = $financeObj->excGD2GB( $uid, $gd, json_encode( $desc ), $data['otid'] );
//			if ( Finance::checkBizResult( $result ) )
//			{
//				if ( !$anchorExchange->update( $data['otid'], [ 'tid' => $result['tid'] ] ) )
//				{
//					echo "$uid  $ctime update  {$result['tid']} failed\n";
//				}
//				//todo log
//
//				unset( $data );
//
//				if ( (int)date( 'm', strtotime( $ctime ) ) == 5 )
//				{
//					$gb = intval( $gb );
//				}
//
//				$data['otid']   = strtotime( $ctime ) . "" . rand( 10000, 99999 ) . "" . rand( 1000, 9999 );
//				$data['uid']    = $uid;
//				$data['type']   = Finance::EXC_GB_RMB;
//				$data['number'] = $gb;
//				$data['status'] = AnchorExchange::EXCHANGE_STATUS_01;
//
//				$desc = [
//					'title' => "$ctime 提现",
//					'rate'  => $rate,
//					'otid'  => $data['otid']
//				];
//
//				if ( $anchorExchange->insert( $data ) )
//				{
//					$result = $financeObj->withdraw( $uid, $gb, json_encode( $desc ), $data['otid'] );
//					if ( Finance::checkBizResult( $result ) )
//					{
//						if ( !$anchorExchange->update( $data['otid'], [ 'utime' => $etime, 'status' => AnchorExchange::EXCHANGE_STATUS_03 ] ) )
//						{
//							echo "$uid $ctime anchor update failed";
//						}
//						else
//						{
//							echo "$uid $ctime SUCCESS \n\n";
//						}
//					}
//					else
//					{
//						echo "$uid $ctime {$result['errno']} Finance exchange success and  withdraw failed \n";
//					}
//				}
//				else
//				{
//					echo "$uid $ctime insert exchange success and  withdraw failed \n";
//				}
//			}
//			else
//			{
//				echo "$uid $ctime {$result['errno']}Finance excGd2Gb failed.\n";
//			}
//		}
//		else
//		{
//			echo "$uid $ctime insert gd =>gb  failed";
//		}
	}
}


$tid = date( "Ymd" ) . '235959';

$sql = "select uid,hpcoin,hpbean from useractive where uid not in (select uid from hpf_balance) and (hpcoin != 0 or hpbean != 0)";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$hb   = $row['hpcoin'];
	$hd   = $row['hpbean'];
	$gb   = 0;
	$gd   = 0;
	$desc = '系统同步发放';
	$financeObj->innerRecharge( $row['uid'], $hb, $gb, $hd, $gd, 0, $desc, $tid );
}

$sql = "select uid,coin,bean,cid from anchor where uid not in (select uid from hpf_balance) and (coin != 0 or bean != 0)";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$hb = 0;
	$hd = 0;

	if ( $row['cid'] == 0 || $row['cid'] == 15 )
	{
		$gb = $row['coin'] / 20 * 1.2;
	}
	else
	{
		$gb = $row['coin'] / 20 * 1.4;
	}

	$gd   = $row['bean'] / 1000;
	$desc = '系统同步发放';
	$financeObj->innerRecharge( $row['uid'], $hb, $gb, $hd, $gd, 0, $desc, $tid );
}

$sql = "select * from hpf_balance where uid in (" . WHITE_LIST . ")";
$res = $db->query( $sql );
while ( $row = $res->fetch_assoc() )
{
	$hb   = -$financeObj->getOutputNumber( $row['hb'] );
	$hd   = -$financeObj->getOutputNumber( $row['hd'] );
	$gb   = -$financeObj->getOutputNumber( $row['gb'] );
	$gd   = -$financeObj->getOutputNumber( $row['gd'] );
	$desc = '系统同步发放';
	$financeObj->innerRecharge( $row['uid'], $hb, $gb, $hd, $gd, 0, $desc, $tid );
}

//todo withdraw flow

$data4 = [
	[ "2290", "思源欧巴", "23", "137", "160", "欢朋官方 ", "0.6" ],
	[ "2625", "浪总总浪", "23", "234", "257", "浪总总浪", "0.7" ],
	[ "3055", "狂丶血", "23", "414", "437", "欢朋官方 ", "0.6" ],
	[ "3345", "小疯子", "7", "88", "95", "武汉七韵道文化传播有限公司", "0.7" ],
	[ "3490", "无情敏哥哥", "13", "109", "122", "欢朋官方 ", "0.6" ],
	[ "3635", "怡宝゛", "8", "128", "136", "浪总总浪", "0.7" ],
	[ "3700", "宇宙超级无敌萌", "75", "703", "778", "欢朋官方 ", "0.6" ],
	[ "4100", "污污污Wuki", "7", "557", "564", "苏州秀涩文化传媒有限公司", "0.7" ],
	[ "4465", "小儿郎、", "10", "158", "168", "欢朋官方 ", "0.6" ],
	[ "8415", "yummy苏小苏", "7", "73", "80", "苏州秀涩文化传媒有限公司", "0.7" ],
	[ "9100", "白景尧", "24", "359", "383", "聚星缘公司", "0.7" ],
	[ "9460", "有有是个女汉子", "9", "207", "216", "聚星缘公司", "0.7" ],
	[ "12000", "译文.", "0", "309", "309", "", "0.6" ],
	[ "13845", "爱作妖的葡萄", "0", "73", "73", "齐齐哈尔市娇媚文化传播有限公司", "0.7" ],
	[ "14445", "源", "6", "117", "123", "欢朋官方 ", "0.6" ],
	[ "24420", "亲切的说爱你", "16", "118", "134", "大同市灿星文化传媒有限公司", "0.7" ],
	[ "24895", "凯凯哥", "10", "49", "59", "枣庄市大喆商贸有限公司", "0.7" ],
	[ "25990", "隔壁小张", "1", "112", "113", "枣庄市大喆商贸有限公司", "0.7" ],
	[ "26080", "最无双手游", "2", "115", "117", "", "0.6" ],
	[ "26980", "掌上舞", "3", "111", "114", "北京红网文化传媒有限公司", "0.7" ],
];

$data5 = [
	[ "3055", " 狂丶血  ", "121.32", "23.755", "145.075", " 欢朋官方", "0.6" ],
	[ "3415", " 权哥大大", "48.84", "14.12", "62.96", " 欢朋官方", "0.6" ],
	[ "3430", " Intro", "87.36", "15.124", "102.484", " 欢朋官方", "0.6" ],
	[ "3490", " 无情敏哥哥", "59.96", "13.506", "73.466", " 欢朋官方", "0.6" ],
	[ "3700", " 宇宙超级无敌萌", "834.68", "35.09", "869.77", " 欢朋官方", "0.6" ],
	[ "4100", "  污污污Wuki ", "204.46", "4.112", "208.572", " 苏州秀涩文化传媒有限公司", "0.7" ],
	[ "8370", "  南南得北", "50.52", "3.562", "54.082", " 欢朋官方", "0.6" ],
	[ "10715", " 柠檬有点甜 ", "79.94", "1.35", "81.29", " 苏州秀涩文化传媒有限公司", "0.7" ],
	[ "11735", " 树深时见鹿。", "54.96", "14.326", "69.286", " 欢朋官方", "0.6" ],
	[ "11885", " 猫某人ฅ ", "83.04", "7.91", "90.95", " 欢朋官方", "0.6" ],
	[ "12050", " A啊浩", "118.2", "24.27", "142.47", " 欢朋官方", "0.6" ],
	[ "15225", " No one and you", "45.84", "5.94", "51.78", "", "0.7" ],
	[ "18955", " 情难忘。", "55.32", "4.75", "60.07", " 欢朋官方", "0.6" ],
	[ "20815", " 王牌-张少 ", "87.64", "2.75", "90.39", " 松原市松视传媒有限公司", "0.7" ],
	[ "21735", " 起点丶酥酥 ", "99.68", "1.209", "100.889", "  磐石市起点网络科技有限公司", "0.7" ],
	[ "23360", " 小祖宗  ", "80.64", "13.806", "94.446", " 欢朋官方", "0.6" ],
	[ "23365", " 欢朋～飞飞", "577.56", "21.568", "599.128", " 欢朋官方", "0.6" ],
	[ "24420", " 亲切的说爱你", "156.54", "22.394", "178.934", " 大同市灿星文化传媒有限公司", "0.7" ],
	[ "26080", "  最无双手游", "74.84", "2.402", "77.242", "", "0.7" ],
	[ "29935", " 梦牵魂绕", "89.6", "4.71", "94.31", " 廊坊市臻艺文化传媒有限公司", "0.7" ],
	[ "49005", "  ❀    念昔", "228", "4.7", "232.7", "", "0.7" ],
	[ "49975", " 枭雄伯爵丶子轩", "71.76", "17.62", "89.38", " 欢朋官方", "0.6" ],
	[ "51340", " ꧁~ ☞T 爷☜~꧂ ", "51.24", "6.56", "57.8", "", "0.7" ],
	[ "62108", " 暖ya", "60", "8.602", "68.602", "", "0.7" ],
];


sync_withdraw_record( $data4, '2017-04-03 13:00:00', '2017-04-15 13:00:00', $db, $redis );

sync_withdraw_record( $data5, '2017-05-03 13:00:00', '2017-05-15 13:00:00', $db, $redis );


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