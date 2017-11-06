<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/27
 * Time: 上午12:39
 */

namespace service\payment;

use lib\User;
use lib\Finance;
use service\user\UserAuthService;

class IAPHelper
{
	const SANDBOX_PAY_URL = 'https://sandbox.itunes.apple.com/verifyReceipt';//'http://old.huanpeng.com/a/t.php';//
	const APPLE_PAY_URL   = 'https://buy.itunes.apple.com/verifyReceipt';

	const FAILED_CONNECT = -1;

	const ERROR_PARAM                  = -4013;
	const ERROR_CREATE_ORDER           = -5554;
	const ERROR_RECHARGE_RANGE_INVALID = -5555;
	const ERROR_PRODUCT_NOTVAILD       = -5556;

	const RECORD_STATUS_CREATE        = 0;
	const RECORD_STATUS_SUCCESS       = 10;
	const RECORD_STATUS_VERIFY_FAILED = 20;

	const RUNTABLE_STATUS_RUN    = 0;
	const RUNTABLE_STATUS_FINISH = 1;


	public static $errorMsg = [
		self::ERROR_PARAM                  => "缺少参数",
		self::ERROR_CREATE_ORDER           => "创建订单失败",
		self::ERROR_RECHARGE_RANGE_INVALID => "充值金额范围10-5000",
		self::ERROR_PRODUCT_NOTVAILD       => "购买的商品不存在"
	];

	protected $_db;
	protected $_redis;

	private $_params = [];

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

		$this->_createTable();
	}


	public function order( $data )
	{
		$this->_log( "request data" . json_encode( $data ) );
		$this->_initParams( $data );

//		$this->_log('md5 before '.$this->_params['r']);
		$this->_log( "md5 result = " . $this->_params['mix'] );

		$userObj = new User( $this->_params['uid'], $this->_db, $this->_redis );

//		$code = $userObj->checkStateError( $this->_params['encpass'] );
		$auth = new UserAuthService();
		$auth->setUid($this->_params['uid']);
		$auth->setEnc($this->_params['encpass']);

		if ( $auth->checkLoginStatus() !== true )
		{
			error2( -1013 );
		}

		//TODO  notice 这里有问题，可以使用两个月以前的票据进行下单，这样一定会成功
		$info = $this->getOrderInfoByMixDeep( $this->_params['mix'] );
		if ( !$info )
		{
			if ( !$this->createOrder( $this->_params['uid'], $this->_params['mix'], $this->_params['r'] ) )
			{
				//todo exit and todo log
				$this->_log( "create order failed" );

			}
			else
			{
				$this->_backFormatData( self::RECORD_STATUS_CREATE );
			}
		}

		if ( $info['status'] == self::RECORD_STATUS_SUCCESS )
		{
			$property = $userObj->getUserProperty();

			if($this->_params['uid'] == IOS_TEST_USER_LIST)
			{
				$rmb = $info['quantity'] * 6 * 0.7;
			}
			else
			{
				$rechargeInfo = $this->_financeObj->getRechargeOrderInfo($info['ftid']);
				$rmb = $rechargeInfo['rmb'];
			}

			$isFirstPay = $userObj->getRechargeNumber() == 1 ? 1 : 0;

			$this->_backFormatData( self::RECORD_STATUS_SUCCESS, '', intval( $property['coin']), $info['channelid'], $rmb, $isFirstPay, $info['ftid'] );
		}

		if ( $info['status'] == self::RECORD_STATUS_VERIFY_FAILED )
		{
			$this->_backFormatData( self::RECORD_STATUS_VERIFY_FAILED, $info['errorno'], '' );
		}

		$this->_backFormatData( self::RECORD_STATUS_CREATE );
	}

	public function recharge( $data, $type = 0 )
	{
		$this->_params['mix']  = $data['mix'];
		$this->_params['uid']  = $data['uid'];
		$this->_params['id']   = $data['id'];
		$this->_params['r']    = $data['receipt'];
		$this->_params['type'] = $type;

		$userObj = new User( $this->_params['uid'], $this->_db, $this->_redis );

		$error       = 0;
		$receiptData = [];
		$this->queryOrder( $type, $error, $receiptData );

		if ( $error == 0 )
		{
			if ( $this->_recharge( $receiptData, $userObj ) )
			{
			}
			else
			{
				$this->_log( "_recharge failed" );
			}
		}
		else
		{
			//如果 error == self::FAILED_CONNECT 是否应该进行重试，
			//如何进行对账？ 是否需要将传入的 base64 encoded receipt data 入库呢？
			//如果入库，对于用户来说，如何进行对账

			if ( $error == self::FAILED_CONNECT )
			{
				$this->_log( "failed connect" );
			}
			else
			{
				if ( $error == 21007 )
				{
					if ( $GLOBALS['env'] != "PRO" )
					{
						$this->_log( __FUNCTION__ . "error 21007 handle" );
						$this->recharge( $data, 1 );
					}
					else
					{
						$this->_updateRecordById( [ 'status' => self::RECORD_STATUS_VERIFY_FAILED, 'errorno' => $error ], $this->_params['id'] );
					}
				}
				else
				{
					$this->_log( "vertify failed" );
					//todo  what code is should close the order???
					if ( $error == 21002 || $error == 21003 )
					{
						$this->_updateRecordById( [ 'status' => self::RECORD_STATUS_VERIFY_FAILED, 'errorno' => $error ], $this->_params['id'] );
					}
				}
			}
		}
	}

	private function _recharge( $receiptData, User $userObj )
	{
//		$this->_log( __FUNCTION__ . json_encode( $receiptData ) );

		if ( $this->_vertifyHandle( $this->_params['id'], $receiptData ) )
		{
			$info        = $this->getOrderInfoById( $this->_params['id'] );
			$productInfo = $this->_getProductInfo( $receiptData['item_id'] );

			if ( !$productInfo )
			{
				$this->_errorJson( self::ERROR_PRODUCT_NOTVAILD );
			}

			if ( $this->_params['uid'] == IOS_TEST_USER_LIST )
			{
				$property = $userObj->getUserProperty();
				$hb       = $productInfo['hpcoin_amount'];
				$userObj->updateUserHpCoin( $property['coin'] + $hb );
				$result = $this->_updateRecordById( [ 'ftid' => '110', 'status' => self::RECORD_STATUS_SUCCESS, 'channelid' => $productInfo['channel_id'] ], $this->_params['id'] );
				if ( !$result )
				{
					//todo log;
					$this->_log( "update self record failed" );

					return false;
				}
				else
				{
					$this->_log( "{$this->_params['uid']} test handle success" );
				}

				return true;
			}

			$uid     = $this->_params['uid'];
			$rmb     = $productInfo['cash_amount'];
			$channel = 'iap';
			$client  = 'ios';
			$refUrl  = '';

			$desc = json_encode( [] );
			$otid = $info['id'];

			//todo 这块应该 放在创建订单时候调用，并且需要考虑到当出现跨月的时候的操作处理,并且orderID 直接绑定到对应记录的ftid上去
			$this->_financeObj->setCtime( $info['ctime'] );
			$this->_log("_recharge set time is ".$info['ctime']);

			$orderid = $this->_financeObj->rechargeOrderCreate( $uid, $rmb, $channel, $client, $refUrl, 0, $desc, $otid );

			if ( !$orderid )
			{
				//todo exit and log
				$this->_log( "orderid $orderid create failed" );

				return false;
			}

			$this->_financeObj->setCtime( date( "Y-m-d H:i:s" ) );
//			$result = $this->_financeObj->rechargeOrderFinish( $receiptData['transaction_id'], $orderid, '0' );
//			if ( !Finance::checkBizResult( $result ) )
//			{
//				$this->_log( "finance recharge failed " . json_encode( $result ) );
//
////				todo exit and log
//				return false;
//			}

//			$userObj->updateUserHpCoin( $result['hb'] );

			//如果财务创建提到别处，这里不再更新ftid
			//应该先进行更新操作，然后再进行财务处理操作 防止重复充值

			$resultTid = rechargeHandleFlow( $receiptData['transaction_id'], $orderid, 0, $this->_db, 0, $this->_financeObj );

			if ( !$resultTid )
			{
				$this->_log( "recharge handle flow falied" );

				return false;
			}

			$result = $this->_updateRecordById( [ 'ftid' => $resultTid, 'status' => self::RECORD_STATUS_SUCCESS, 'channelid' => $productInfo['channel_id'] ], $this->_params['id'] );
			if ( !$result )
			{
				//todo log;
				$this->_log( "update self record failed" );

				return false;
			}

			return true;
		}
		else
		{
			//todo log
			$this->_log( "_vertify handle failed" );

			return false;
		}
	}

	public function getOrderInfoByMixDeep( $mix )
	{
		$info = $this->getOrderInfoByMix( $mix );
		if ( !$info )
		{
			$info = $this->getOrderInfoByMix( $mix, $this->_getLastMonthTimeStamp() );
		}

		return $info;
	}

	public function getOrderInfoByMix( $mix, $timestamp = 0 )
	{
		$table = $this->_getRecordTableName( $timestamp );

		$sql = "select * from $table where mix='$mix'";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			//log
			return false;
		}
		$row = $res->fetch_assoc();

		if ( $row['id'] )
		{
			return $row;
		}
		else
		{

			//todo log
			return false;
		}
	}

	public function getOrderInfoById( $id )
	{
		$table = $this->_getRecordTableNameById( $id );

		$sql = "select * from $table where id='$id'";
		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			//todo log
			return false;
		}

		$row = $res->fetch_assoc();

		if ( $row['id'] )
		{
			return $row;
		}
		else
		{
			return false;
		}
	}

	private function _getLastMonthTimeStamp( $timestamp = 0 )
	{
		$timestamp = $timestamp ? $timestamp : time();


		$timestamp = strtotime( '-1 month', strtotime( date( "Y-m", $timestamp ) ) );

		return $timestamp;
	}

	//
	public function createOrder( $uid, $mix, $receipt )
	{

		$tablename = $this->_getRecordTableName();
		$data      = [
			'id'      => getOtid(),
			'uid'     => $uid,
			'mix'     => $mix,
			'receipt' => $receipt
		];
		$sql       = $this->_db->insert( $tablename, $data, true );
		if ( $res = $this->_db->query( $sql ) )
		{
			return $data['id'];
		}
		else
		{
			//todo log

			return false;
		}
	}

	/**
	 * 暂时不启用
	 *
	 * @param $uid
	 * @param $mix
	 * @param $receipt
	 *
	 * @return bool|mixed
	 */
	private function _createIAPOrder($uid,$mix,$receipt)
	{
		$tablename = $this->_getRecordTableName();
		$data      = [
			'id'      => getOtid(),
			'uid'     => $uid,
			'mix'     => $mix,
			'receipt' => $receipt
		];
		$sql       = $this->_db->insert( $tablename, $data, true );
		if ( $res = $this->_db->query( $sql ) )
		{
			return $data['id'];
		}
		else
		{
			//todo log

			return false;
		}
	}

	/**
	 * 创建财务订单 暂时不启用
	 *
	 * @param        $uid
	 * @param        $iapOrderID
	 * @param string $refurl
	 * @param string $desc
	 *
	 * @return bool|mixed
	 */
	private function _createFinanceOrder($uid,$iapOrderID,$refurl='',$desc = '')
	{
		$rmb = 0;

		$orderid  = $this->_financeObj->rechargeOrderCreate($uid,$rmb,'','',$refurl,0,$desc,$iapOrderID);

		return $orderid;
	}




	//认证成功处理流程
	private function _vertifyHandle( $id, $data )
	{
		$data  = $this->_fieldData( $data );
		$table = $this->_getRecordTableNameById( $id );
		$sql   = $this->_db->where( "id='$id'" )->update( $table, $data, true );
		$this->_log($sql);
		$res = $this->_db->query( $sql );

		if ( $res )
		{
			return true;
		}
		else
		{
			//todo log;
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}
	}


	private function _fieldData( $data )
	{
		$filed = [
			'app_item_id',
			'item_id',
			'product_id',
			'quantity',
			'bvrs',
			'bid',
			'version_external_identifier',
			'original_purchase_date_ms',
			'purchase_date_ms',
			'unique_vendor_identifier',
			'unique_identifier',
			'original_transaction_id',
			'transaction_id'
		];
		$tmp   = [];
		foreach ( $filed as $key )
		{
			$tmp[$key] = $data[$key];
		}

		return $tmp;
	}

	private function _updateRecord( $data, $mix )
	{

		//todo 如果在表单已经创建成功的情况下，出现跨月 应该如何处理呢？
		$table = $this->_getRecordTableName();
		$sql   = $this->_db->where( "mix='$mix'" )->update( $table, $data, true );
		$res   = $this->_db->query( $sql );

		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );
			//todo
		}

		return $res;
	}

	private function _updateRecordById( $data, $id )
	{
		$table = $this->_getRecordTableNameById( $id );
		$sql   = $this->_db->where( "id='$id'" )->update( $table, $data, true );
		$res   = $this->_db->query( $sql );

		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );
			//todo
		}

		return $res;
	}

	private function _getRecordTableName( $ctime = 0 )
	{
		$time = $ctime ? $ctime : time();

		return "iap_record_" . date( "Ym", $time );
	}

	private function _getRecordTableNameById( $id )
	{
		$time = substr( $id, 0, 10 );

		return $this->_getRecordTableName( $time );
	}

	public function queryOrder( $type, &$error, &$receiptData )
	{
		$data['receipt-data'] = $this->_params['r'];
		$postData             = json_encode( $data );

//		$this->_log( $postData );
		$this->_log( "{$this->_params['uid']} query order type is $type" );
		$test = 0;
		if ( $this->_params['uid'] == IOS_TEST_USER_LIST )
		{
			$test = 1;
		}

		if ( $type )
		{
			$result = $this->_makeRequest( $postData, self::SANDBOX_PAY_URL );
		}
		else
		{
			$result = $this->_makeRequest( $postData, self::APPLE_PAY_URL );
		}

		$this->_log( "back result" . $result );

		if ( $result && $result = json_decode( $result, true ) )
		{
			$this->_log( json_encode( $result ) );

			if ( $result['status'] == 0 )
			{
				$error = 0;
				$result['receipt'];
				$receiptData = $this->_paraseReceiptData( $result['receipt'] );

			}
			else
			{
				$error = $result['status'];

				if ( $error == 21007 && $test == 1 && $type == 0 )
				{
					$this->queryOrder( 1, $error, $receiptData );

					return true;
				}
			}
		}
		else
		{
			//连接超时处理流程
			$error = self::FAILED_CONNECT;

			if ( $test == 1 && $type == 0 )
			{
				$this->queryOrder( 1, $error, $receiptData );
			}
			//time out
			//return false;
		}
	}

	private function _queryOrder( $type, $postData, &$error )
	{
		if ( $type )
		{
			$result = $this->_makeRequest( $postData, self::SANDBOX_PAY_URL );
		}
		else
		{
			$result = $this->_makeRequest( $postData, self::APPLE_PAY_URL );
		}

		$this->_log( "back result" . $result );
	}

	private function _getParamsRule()
	{
		return [
			'uid'     => [
				'type' => 'int',
				'must' => true
			],
			'encpass' => [
				'type' => 'string',
				'must' => true,
			],
			'r'       => [
				'type' => 'string',
				'must' => true,
			],
			'type'    => 'int'
		];
	}

	private function _initParams( $data )
	{
		$result = [];
		$param  = $this->_getParamsRule();
		if ( !checkParam( $param, $data, $result ) )
		{
			$this->_errorJson( self::ERROR_PARAM );
		}

		foreach ( $result as $key => $value )
		{
			$this->_params[$key] = $value;
		}

		$this->_params['mix'] = md5( urldecode( $data['r'] ) );

	}

	private function _paraseReceiptData( $data )
	{
		$tmp['app_item_id']                 = $data['app_item_id'];
		$tmp['bid']                         = $data['bundle_id'];
		$tmp['bvrs']                        = $data['application_version'];
		$tmp['version_external_identifier'] = $data['version_external_identifier'];

		$in_app = $data['in_app'][0];

		$tmp['quantity']                  = $in_app['quantity'];
		$tmp['product_id']                = $in_app['product_id'];
		$tmp['original_purchase_date_ms'] = $in_app['original_purchase_date_ms'];
		$tmp['purchase_date_ms']          = $in_app['purchase_date_ms'];
		$tmp['transaction_id']            = $in_app['transaction_id'];
		$tmp['original_transaction_id']   = $in_app['original_transaction_id'];

		$itemid = $this->_getItemID( $tmp['bid'], $tmp['product_id'] );

		$tmp['item_id'] = $itemid;

		return $tmp;
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
		mylog( "create orderid responseJson result" . json_encode( $data ), LOG_DIR . "service\\payment\\WxpayHP.log" );
		exit( json_encode( $data ) );
	}

	private function _createTable()
	{
		$date = date( "Ym" );
		$sql  = "
			CREATE TABLE IF NOT EXISTS iap_record_$date (
  `id`                          BIGINT(20) UNSIGNED NOT NULL,
  `uid`                         INT(10) UNSIGNED    NOT NULL         DEFAULT 0,
  `app_item_id`                 BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `item_id`                     BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `product_id`                  VARCHAR(100)        NOT NULL         DEFAULT '',
  `quantity`                    BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0
  COMMENT 'dollar',
  `bvrs`                        VARCHAR(20)         NOT NULL         DEFAULT ''
  COMMENT 'appversion',
  `bid`                         VARCHAR(100)        NOT NULL         DEFAULT ''
  COMMENT 'APPBundleID',
  `version_external_identifier` VARCHAR(32)         NOT NULL         DEFAULT '',
  `original_purchase_date_ms`   BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `purchase_date_ms`            BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `unique_vendor_identifier`    VARCHAR(100)        NOT NULL         DEFAULT '',
  `unique_identifier`           VARCHAR(100)        NOT NULL         DEFAULT '',
  `original_transaction_id`     BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `transaction_id`              BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `status`                      INT(10) UNSIGNED    NOT NULL         DEFAULT 0
  COMMENT '0:创建,10:验证成功,20:验证失败',
  `mix`                         CHAR(32)            NOT NULL         DEFAULT '',
  `receipt`                     BLOB                NOT NULL         DEFAULT '',
  `errorno`                     INT(10) UNSIGNED    NOT NULL         DEFAULT 0,
  `ftid`                        BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `channelid`                   INT(10) UNSIGNED    NOT NULL         DEFAULT 0,
  `ctime`                       TIMESTAMP           NOT NULL         DEFAULT current_timestamp,
    PRIMARY KEY (`id`),
  UNIQUE (`mix`),
  KEY (`ftid`),
  KEY (`uid`),
  KEY (`channelid`)
);";

		return $res = $this->_db->query( $sql );
	}

	private function _backFormatData( $status, $errorcode = '', $coin = '', $channelID = '', $cost = '', $isFirstPay = '', $orderid=0 )
	{
		$data['rm']         = $this->_params['mix'];
		$data['status']     = $status;
		$data['coin']       = $coin;
		$data['ec']         = $errorcode;
		$data['uid']        = $this->_params['uid'];
		$data['channelID']  = $channelID;
		$data['cost']       = $cost;
		$data['isFirstPay'] = $isFirstPay;

		$rechargeResult = hp_getRechargeActive( $orderid );
		$data = array_merge( $data, $rechargeResult );

		$this->_responseJson( $data );
	}

	private function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}

	private function _makeRequest( $data, $url )
	{
		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );

		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
		$data   = curl_exec( $ch );
		$errno  = curl_errno( $ch );
		$errmsg = curl_error( $ch );

		curl_close( $ch );

		if ( $errno != 0 )
		{
			$this->_log( __FUNCTION__ . "$errmsg" );
//			throw new \Exception( $errmsg, $errno );
		}

		return $data;
	}

	private function _getProductInfo( $itemid )
	{
		$sql = "select * from iap_product_info where  item_id=$itemid";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}

		$row = $res->fetch_assoc();

		return $row;
	}

	private function _getItemID( $bid, $product_id )
	{
		$sql = "select item_id from iap_product_info where bid='$bid' and product_id='$product_id'";

		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			//todo log
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}

		$row = $res->fetch_assoc();

		return intval( $row['item_id'] );
	}

	private function _getRunTableInfo()
	{
		$sql = "select `name` from iap_handle_table_record where status=" . self::RUNTABLE_STATUS_RUN;
		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			//todo log
			return false;
		}

		$result = [];

		while ( $row = $res->fetch_assoc() )
		{
			array_push( $result, $row['name'] );
		}

		return $result;
	}

	private function _insertRunTable( $name, $status )
	{
		$sql = "insert into iap_handle_table_record (`name`,`status`) VALUE ('$name',$status) on duplicate key update status=$status";
		$res = $this->_db->query( $sql );

		return $res;
	}

	private function _doRecharge( $tableName, $curTab )
	{
		$sql = "select id,uid,receipt,mix from $tableName where status=" . self::RECORD_STATUS_CREATE;
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			if ( $this->_db->errno() == 1146 )
			{
				$this->_createTable();
			}

			return false;
		}
		$i = 0;

		while ( $row = $res->fetch_assoc() )
		{
			$this->recharge( $row );
			$i++;
		}

		if ( $i == 0 && $curTab != $tableName )
		{
			$this->_insertRunTable( $tableName, self::RUNTABLE_STATUS_FINISH );
		}
	}

	public function checkListRun()
	{
		while ( true )
		{
			$curTable     = $this->_getRecordTableName();
			$runTableList = $this->_getRunTableInfo();

			if ( !in_array( $curTable, $runTableList ) )
			{
				$this->_insertRunTable( $curTable, self::RUNTABLE_STATUS_RUN );
			}

			foreach ( $runTableList as $name )
			{
				$this->_doRecharge( $name, $curTable );
			}

			sleep( 1 );
		}
	}
}