<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/5
 * Time: 下午2:36
 */

namespace service\payment;

use lib\Finance;
use lib\User;
use service\user\UserAuthService;

class UnifiedOrder
{
	const ERROR_PARAM        = -4013;
	const ERROR_CREATE_ORDER = -5554;
	const ERROR_RECHARGE_RANGE_INVALID=-5555;


	const WXPAY_RMB_RATE = 100;
	const ALIPAY_RMB_RATE = 1;

	public static $errorMsg = [
		self::ERROR_PARAM        => "缺少参数",
		self::ERROR_CREATE_ORDER => "创建订单失败",
		self::ERROR_RECHARGE_RANGE_INVALID => "充值金额范围10-5000"
	];

	protected $_params = [];

	protected $_db;
	protected $_redis;

	public function __construct( \DBHelperi_huanpeng $db, \RedisHelp $redisHelp )
	{
		if ( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new \DBHelperi_huanpeng();
		}

		if ( $redisHelp )
		{
			$this->_redis = $redisHelp;
		}
		else
		{
			$this->_redis = new \RedisHelp();
		}


		$this->_financeObj = new Finance( $this->_db, $this->_redis );
	}

	protected function _getUnifiedParamRule()
	{
		$params = array(
			'uid'         => [
				'type' => 'int',
				'must' => true
			],
			'encpass'     => [
				'type' => 'string',
				'must' => true,
			],
			'quantity'    => [//数量
				'type' => 'int',
				'must' => true,
			],
			'productID'   => [
				'type' => 'int',
				'must' => true
			],
			'channel'     => [
				'type'   => 'string',
				'must'   => true,
				'values' => [ 'wechat', 'alipay' ]
			],
			'client'      => [
				'type'   => 'string',
				'must'   => true,
				'values' => [ 'web', 'android', 'ios','wxjs', 'h5' ]
			],
			'refUrl'      => 'string',
			'promationID' => 'int'
		);

		return $params;
	}

	protected function _initUnifiedParam( array $data )
	{

		$result = array();
		$param  = $this->_getUnifiedParamRule();

		if($data['client'] == 'wxjs')
		{
			$param['openid'] = [
				'type' => 'string',
				'must' => true
			];
		}

		if ( !checkParam( $param, $data, $result ) )
		{
			$this->_errorJson( self::ERROR_PARAM );
		}

		if($data['quantity'] <= 0)
		{
			$this->_errorJson(self::ERROR_PARAM);
		}

		foreach ( $result as $key => $value )
		{
			$this->_params[$key] = $value;
		}

		if($data['quantity']<10 || $data['quantity'] > 50000)
		{
			$this->_errorJson(self::ERROR_RECHARGE_RANGE_INVALID);
		}

		$this->_params['productname'] = '欢朋币充值';
	}

	protected function _errorJson( $code = 0, $type = 1 )
	{
		$msg = self::$errorMsg[$code];
		$this->_responseJson( $msg, $code, $type );
	}

	protected function _responseJson( $content, $errorCode = 0, $type = 1 )
	{
		$data['status'] = 1;
		if ( $errorCode != 0 )
		{
			$data['status']          = 0;
			$data['content']['code'] = $errorCode;
			$data['content']['desc'] = (string)$content;
			$data['content']['type'] = $type;
		}
		else
		{
			$data['content'] = $content;
		}
		mylog("create orderid responseJson result".json_encode($data),LOG_DIR."service\\payment\\WxpayHP.log");
		exit( json_encode( $data ) );
	}

	protected function _unifiedorder($data, &$rmb)
	{
		$this->_initUnifiedParam( $data );
		$userObj = new User( $this->_params['uid'], $this->_db, $this->_redis );

//		$code = $userObj->checkStateError( $this->_params['encpass'] );

		$auth = new UserAuthService();
		$auth->setUid($this->_params['uid']);
		$auth->setEnc($this->_params['encpass']);

		if($auth->checkLoginStatus() !== true)
		{
			error2(-1013);
		}

//		if ( true !== $code )
//		{
//			error2( $code );
//		}

		//todo  change
		$rmb = intval( $data['rmb'] ) ? intval( $data['rmb'] ) : $this->_params['quantity'] / 10;
		//todo insert into huanpeng record and get tid
		$tid = 0;
		$id  = $this->_financeObj->rechargeOrderCreate( $this->_params['uid'], $rmb, $this->_params['channel'], $this->_params['client'], $this->_params['refUrl'], $this->_params['promationID'], json_encode( [] ), $tid );

		if ( !$id )
		{
			$this->_errorJson( self::ERROR_CREATE_ORDER );
		}

		return $id;
	}
}