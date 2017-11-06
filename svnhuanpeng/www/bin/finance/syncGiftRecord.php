<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/5/11
 * Time: 下午4:23
 */
include __DIR__ . "/../../include/init.php";
$db = new DBHelperi_huanpeng();

function createGiftMonthTable( $suffix, $type, $db )
{
	if( $type == 1 )
	{ //免费礼物
		$sql = "
				CREATE TABLE IF NOT EXISTS `giftrecord_" . $suffix . "` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
			  `luid` int(10) unsigned NOT NULL COMMENT '主播id',
			  `liveid` int(10) unsigned NOT NULL COMMENT '直播id',
			  `uid` int(10) unsigned NOT NULL COMMENT '送礼人id',
			  `giftid` tinyint(3) unsigned NOT NULL COMMENT '礼物id',
			  `giftnum` int(10) unsigned NOT NULL COMMENT '礼物数',
			  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '送礼时间',
			  `otid` bigint(20) unsigned NOT NULL DEFAULT 0,
			  `income` float(14,2) NOT NULL DEFAULT '0.00',
			  `cost` float(14,2) NOT NULL DEFAULT '0.00',
			  PRIMARY KEY (`id`),
			  KEY `luid` (`luid`),
			  KEY `liveid` (`liveid`),
			  KEY `uid` (`uid`),
			  KEY `ctime` (`ctime`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;	
			";
	}
	else
	{ //收费礼物
		$sql = "
				create table IF NOT EXISTS `giftrecordcoin_" . $suffix . "` (
				  `id` varchar(20) NOT NULL DEFAULT'' COMMENT '记录id',
				  `luid` int(10) unsigned NOT NULL COMMENT '主播id',
				  `liveid` int(10) unsigned NOT NULL COMMENT '直播id',
				  `uid` int(10) unsigned NOT NULL COMMENT '送礼人id',
				  `giftid` tinyint(3) unsigned NOT NULL COMMENT '礼物id',
				  `giftnum` tinyint(3) unsigned NOT NULL COMMENT '礼物数',
				  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '送礼时间',
			      `otid` bigint(20) unsigned NOT NULL DEFAULT 0,
			      `income` float(14,2) NOT NULL DEFAULT '0.00',
			       `cost` float(14,2) NOT NULL DEFAULT '0.00',
				  PRIMARY KEY (`id`),
				  KEY `luid` (`luid`),
				  KEY `liveid` (`liveid`),
				  KEY `uid` (`uid`),
				  KEY `ctime` (`ctime`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			";
	}
	$res = $db->doSql( $sql );
	if( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}


/**
 * 同步欢豆
 *
 * @param $suffix
 * @param $db
 *
 * @return bool
 */
function syncGiftRecord( $suffix, $db )
{
	$isok = createGiftMonthTable( $suffix, 1, $db );
	if( $isok )
	{
		$time = substr( $suffix, 0, 4 ) . '-' . substr( $suffix, -2 );
		$res = getOlderGiftRecord( $time, 1, $db );
		if( $res )
		{
			foreach ( $res as $v )
			{
				if( $suffix == 201703 )
				{
					$v['otid'] = 10086;
					addToNewGiftRecord( $v, $suffix, $db );
				}
				else
				{
					if($v['id']){
						$fres = finaceTable( $suffix, 1, $v['id'], $db );
						if( $fres )
						{
							$v['otid'] = $fres['id'];
							$v['ctime'] = $fres['ctime'];
							$v['cost'] = $fres['gd'];
							$v['income'] = $fres['gd'] / 1000;
							addToNewGiftRecord( $v, $suffix, $db );
						}
					}
				}
			}

		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

/**
 * 同步欢豆
 *
 * @param $suffix
 * @param $db
 *
 * @return bool
 */
function syncGiftRecordCoin( $suffix, $db )
{
	$isok = createGiftMonthTable( $suffix, 2, $db );
	if( $isok )
	{
		$time = substr( $suffix, 0, 4 ) . '-' . substr( $suffix, -2 );
		$res = getOlderGiftRecord( $time, 2, $db );
		if( $res )
		{
			foreach ( $res as $v )
			{
				if( $suffix == 201703 )
				{
					$v['otid'] = 10086;
					addToNewGiftRecordCoin( $v, $suffix, $db );
				}
				else
				{
					if($v['id']){
						$fres = finaceTable( $suffix, 2, $v['id'], $db );
						if( $fres )
						{
							$v['otid'] = $fres['id'];
							$v['ctime'] = $fres['ctime'];
							$v['cost'] = abs( $fres['hb'] ) / 1000;
							$v['income'] = $fres['gb'] / 1000;
							addToNewGiftRecordCoin( $v, $suffix, $db );
						}
					}
				}
			}

		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function getOlderGiftRecord( $time, $type, $db )
{
	if( $type == 1 )
	{//免费
		$res = $db->where( "ctime like '$time%'" )->select( 'giftrecord' );
	}
	else
	{
		$res = $db->where( "ctime like '$time%'" )->select( 'giftrecordcoin' );
	}
	if( $res )
	{
		return $res;
	}
	else
	{
		return array();
	}
}

function addToNewGiftRecord( $data, $suffix, $db )
{
	if( $data )
	{
		$db->insert( 'giftrecord_' . $suffix, $data );
	}
}

function addToNewGiftRecordCoin( $data, $suffix, $db )
{
	if( $data )
	{
		$db->insert( 'giftrecordcoin_' . $suffix, $data);
	}
}


function finaceTable( $suffix, $type, $id, $db )
{
	if( $type == 1 )
	{//免费礼物
		$res = $db->field( 'id,gd,ctime' )->where( 'otid=' . $id )->select( "hpf_sendBeanRecord_" . $suffix );
	}
	else
	{
		$res = $db->field( 'id,hb,gb,ctime' )->where( 'otid=' . $id )->select( "hpf_sendGiftRecord_" . $suffix);
	}
	if( $res )
	{
		return $res[0];
	}
	else
	{
		return array();
	}
}

function run( $db )
{
	for ( $i = 0; $i < 3; $i++ )
	{
		syncGiftRecord( date( 'Ym', strtotime( "-$i month" ) ), $db );//同步免费礼物数据
		syncGiftRecordCoin( date( 'Ym', strtotime( "-$i month" )), $db );//同步收费礼物数据
	}
}

run( $db );