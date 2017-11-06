<?php
namespace service\push;

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/7
 * Time: 16:52
 */


use lib\ApplePush;
use lib\LivePush;
use lib\MsgPackage;
use lib\SocketSend;
use DBHelperi_huanpeng;

class SystemPush
{

	const SENDER_UID = 0;

	private static $db;
	private static $applePush;
	private static $livePush;

	public function __construct()
	{
		if ( !self::$db )
		{
			self::$db = new DBHelperi_huanpeng();
		}
		if ( !self::$applePush )
		{
			self::$applePush = new ApplePush();
		}

		if ( !self::$livePush )
		{
			self::$livePush = new LivePush();
		}

	}

	public function add( $uidList, $title, $msg, $action, $custom )
	{

		$tokenList = self::$livePush->getApplePushList( $uidList );

		$this->_pushAndroid( $uidList, $title, $msg, $action, $custom );
		$this->_pushApple( $tokenList, $title, $msg, $action, $custom );

		return true;
	}

	public function send( $uidList, $title, $msg, $action, $custom )
	{
		$tokenList = self::$livePush->getApplePushList( $uidList );

		$this->_pushAndroid( $uidList, $title, $msg, $action, $custom );
		$this->_pushApple( $tokenList, $title, $msg, $action, $custom );

		return true;
	}

	private function _pushApple( $tokenList, $title, $msg, $action, $custom )
	{

		foreach ( $tokenList as $value )
		{
			$token = $value['deviceToken'];
			$uid   = $value['uid'];

			$mid = self::SENDER_UID . "-" . $uid . "-" . microtime( true ) * 10000;

			$message = MsgPackage::getSystemPushApplePushMsgPackage( $token, $title, $msg, $mid, $action, $custom ); 
			self::$applePush->send( $message );
		}

		return true;
	}

	private function _pushAndroid( $uidList, $title, $msg, $action, $custom )
	{
		foreach ( $uidList as $uid )
		{
			$message = MsgPackage::getSystemPushMsgSocketPackage( $uid, $msg, $title, $action, $custom );
			SocketSend::sendMsg( $message, self::$db );
		}

		return true;
	}
}