<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/7/28
 * Time: 13:37
 */

namespace dota\app\http;

use api\due\rongCloud\sendMsg;
use lib\LivePush;
use lib\MsgPackage;
use lib\SiteMsgBiz;
use service\due\rongCloud\RongCloudService;
use service\push\SystemMsgTask;

class Push
{
	const ERROR_PARAM_NOT_VALID = "-2001";
	const ERROR_SEND_MSG_FAILED = '-2002';

	static $errorMsg = [
		self::ERROR_PARAM_NOT_VALID => "无效的参数",
		self::ERROR_SEND_MSG_FAILED => "发送消息失败"
	];

	private $_log = 'dota_push';

	public function sendPushMsg()
	{
		$conf = [
			'uids'      => [
				'must' => true,
				'type' => 'string'
			],
			'title'     => [
				'must' => true,
				'type' => 'string'
			],
			'msg'       => [
				'must' => true,
				'type' => 'string'
			],
			'type'      => [
				'must'    => true,
				'type'    => 'string',
				'values'  => [ 0, 2 ],
				'default' => 0
			],
			'isSiteMsg' => [
				'must'    => true,
				'type'    => 'int',
				'default' => 0
			],
			'adminuid' => [
				'must'    => true,
				'type'    => 'int',
				'default' => 0
			]
		];

		$data  = $_POST;
		$param = [];

		if ( !checkParam( $conf, $data, $param ) )
		{
			$code = self::ERROR_PARAM_NOT_VALID;
			render_error_json( static::$errorMsg[$code], $code );
		}

		$uids      = $param['uids'];
		$msg       = $param['msg'];
		$title     = $param['title'];
		$isSiteMsg = $param['isSiteMsg'];
		$type      = $param['type'];
		$adminuid  = $param['adminuid'];


		$action = $isSiteMsg == 1 ? 'site-msg' : 'nothing';

		$msgTask = new SystemMsgTask();

		if ( $type == MsgPackage::SITEMSG_TYPE_TO_ALL )
		{
			$result = $msgTask->addAllUserMsg( $adminuid, $title, $msg, $action );
			write_log("管理员uid：".$adminuid." | 消息标题：".$title." | 消息内容：".$msg." | ".$action." | 调用结果：".$result,'systemMsgTest');
		}
		elseif ( $type == MsgPackage::SITEMSG_TYPE_TO_USER )
		{
			$uids = explode( ",", $uids );

			$uidsList = [];

			foreach ( $uids as $uid )
			{
				array_push( $uidsList, intval( $uid ) );
			}

			$uidsList = implode( ",", $uidsList );
			$result = $msgTask->addSystemMsg( $uidsList, $title, $msg, $action );
			write_log("接受用户uid：".$uidsList." | 消息标题：".$title." | 消息内容：".$msg." | ".$action." | 调用结果：".$result,'systemMsgTest');
		}
		else
		{
			$result = false;
		}

		if ( $result )
		{
			render_json( [] );
		}
		else
		{
			$code = self::ERROR_SEND_MSG_FAILED;
			render_json( static::$errorMsg[$code], $code );
		}
	}

	public function sendSiteMsg()
	{
		$conf = [
			'uids'     => 'string',
			'msg'      => [
				'must' => true,
				'type' => 'string'
			],
			'title'    => [
				'must' => true,
				'type' => 'string'
			],
			'type'     => [
				'must'    => true,
				'type'    => 'int',
				'values'  => [ 0, 2 ],
				'default' => 0
			],
			'adminuid' => [
				'must'    => true,
				'type'    => 'int',
				'default' => 0
			]
		];

		$data  = $_POST;
		$param = [];

		if ( !checkParam( $conf, $data, $param ) )
		{
			$code = self::ERROR_PARAM_NOT_VALID;
			render_error_json( static::$errorMsg[$code], $code );
		}

		$uid      = isset( $param['uids'] ) ? $param['uids'] : 0;
		$msg      = $param['msg'];
		$title    = $param['title'];
		$type     = $param['type'];
		$adminUid = $param['adminuid'];

		$siteMsg = new SiteMsgBiz();

		$r = false;

		$errmsg = '';

		if ( $type == MsgPackage::SITEMSG_TYPE_TO_ALL )
		{
			$uid     = 0;
			$package = MsgPackage::getSiteMsgPackage( $uid, $title, $msg, $type, $adminUid );
			$r       = $siteMsg->sendMsg( $package );
		}
		elseif ( $type == MsgPackage::SITEMSG_TYPE_TO_USER )
		{
			$uidList = explode( ',', $uid );
//            var_dump($uid);
//            var_dump($uidList);

			if ( !is_array( $uidList ) && empty( $uidList ) )
			{
				$r      = false;
				$errmsg = "uid list is not vaild";
			}
			else
			{
				$r = true;

				$package = MsgPackage::getSiteMsgPackage( $uidList, $title, $msg, $type, $adminUid );
//                var_dump($package);
				if ( !$siteMsg->sendMsg( $package ) )
				{
					$r      = false;
					$errmsg = "sendMsg is falied";
					\write_log( "sendMsg is failed", $this->_log );
				}
			}
		}
		else
		{
			$r      = false;
			$errmsg = "type failed";
		}

		if ( $r )
		{
//			succ();
			render_json( [] );
		}
		else
		{
			$code = self::ERROR_SEND_MSG_FAILED;
			// render_json(static::$errorMsg[$code], $code);
			render_json( $errmsg, $code );
		}
	}
}
