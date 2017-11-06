<?php
namespace lib;

use \DBHelperi_huanpeng;

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/3/28
 * Time: 11:54
 */

/**
 * socket类
 *
 * Class SocketSend
 */
class SocketSend
{
	/**
	 * @var socket 发送类型：对个人用户发送
	 */
	const SEND_TO_USER = 1;

	/**
	 * @var socket 发送类型：对房间发送
	 */
	const SEND_TO_ROOM = 2;

	/**
	 * @var socket 发送类型：对所有人发送
	 */
	const SEND_TO_ALL = 3;

	/**
	 * @var bool debug 模式
	 */
	static $debug = true;

	/**
	 * @var array 存储当前房间ID的所有服务器地址 格式为 $_server = array('1170' => array('112.28.13.22:1103','112.28.13.22:1104'))
	 *            其中 key值为主播ID，value值为服务器IP:PORT列表
	 */
	private static $_servers = array();

	/**
	 * 构建socket 发送地址
	 *
	 * @param array              $content 消息实体
	 * @param DBHelperi_huanpeng $db      数据库类实例
	 *
	 * @return array
	 */
	private static function _buildSendUrl( array $content, DBHelperi_huanpeng $db )
	{
		$type       = (int)$content['type'];
		$uid        = (int)$content['uid'];
		$luid       = (int)$content['luid'];
		$msgContent = $content['content'];
		$methods    = array( 1 => 'sendonce', 2 => 'send', 3 => 'sendtoall' );
		$method     = $methods[$type];
		$urls       = array();

		if( empty( static::$_servers[$luid] ) )
		{
			static::_getServers( $db, $luid );
		}
		$serverList = static::$_servers[$luid] ?? array();
		foreach ( $serverList as $server )
		{
			if( $type == self::SEND_TO_USER )
			{
				$urls[] = "http://$server/$method?roomid=$luid&userid=$uid&encrypted=yes&content=$msgContent";
			}
			else
			{
				$urls[] = "http://$server/$method?roomid=$luid&encrypted=yes&content=$msgContent";
			}
		}

		return $urls;
	}

	/**
	 * 发送socket消息
	 *
	 * @param array              $content 消息实体
	 * @param DBHelperi_huanpeng $db      数据库实例
	 *
	 * @return bool
	 */
	public static function sendMsg( array $content, DBHelperi_huanpeng $db )
	{
		$urls = static::_buildSendUrl( $content, $db );
		//逐个请求，当聊天服务器增多时候，应改为并行请求
		foreach ( $urls as $url )
		{
                        write_log($url,'message_order_push');
			if( static::$debug )
			{
				mylog( $url );
			}
			file_get_contents( $url );
		}

		return true;
	}

	/**
	 * 获取服务器列表
	 *
	 * @param DBHelperi_huanpeng $db   数据库实例
	 * @param int                $luid 主播ID，如果==0 则获取所有在线的服务器IP:PORT地址
	 *
	 * @return bool
	 */
	private static function _getServers( DBHelperi_huanpeng $db, int $luid = 0 )
	{
		$sql = "select distinct(concat(inet_ntoa(serverip),':',serverport)) from liveroom";

		if( $luid )
		{
			$sql = "select distinct(concat(inet_ntoa(serverip),':',serverport)) from liveroom where luid=$luid";
		}
		$res = $db->query( $sql );
		while ( $row = $res->fetch_row() )
		{
			static::$_servers[$luid][] = $row[0];
		}

		return true;
	}
}