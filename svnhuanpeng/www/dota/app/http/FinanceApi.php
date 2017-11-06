<?php
namespace dota\app\http;


use lib\Finance;
use lib\AnchorExchange;

class FinanceApi
{
	const ERROR_PARAM_NOT_VALID     = -2001;
	const ERROR_ORDER_NOT_FOUND     = -2002;
	const ERROR_ORDER_NOT_VALID     = -2003;
	const ERROR_ORDER_HANDLE_FAILED = -3004;
	const ERROR_CHANGE_RATE_FAILED  = -3005;

	static $errorMsg = [
		self::ERROR_PARAM_NOT_VALID     => '无效的参数',
		self::ERROR_ORDER_NOT_FOUND     => '订单不存在',
		self::ERROR_ORDER_NOT_VALID     => "订单状态无效",
		self::ERROR_ORDER_HANDLE_FAILED => '订单处理失败',
		self::ERROR_CHANGE_RATE_FAILED  => "比率设置失败"
	];

//	public $_params;

	private function _withdrawLog( $orderid, $otid, $action, $result )
	{
		$data = [
			'orderid'   => $orderid,
			'handletid' => $otid,
			'ctime'     => date( "Y-m-d H:i:s" ),
			'action'    => $action,
			'result'    => $result
		];

		$log = json_encode( $data );

		$log_dir = LOG_DIR . "withdraw_handle_record.log";

		return file_put_contents( $log_dir, "$log\n", FILE_APPEND );
	}

	public function withdrawHandle()
	{

		$conf = [
			'orderid' => [
				'must' => true,
				'type' => 'int'
			],
			'otid'    => [
				'must' => true,
				'type' => 'string'
			],
			'type'    => [
				'must'   => true,
				'type'   => 'int',
				'values' => [ 1, 2 ]
			],
			'desc'    => 'string'
		];

		/**
		 * orderid
		 * desc
		 * otid
		 */
		$data  = $_POST;
		$param = [];

		if ( !checkParam( $conf, $data, $param ) )
		{
			$code = self::ERROR_PARAM_NOT_VALID;
			render_json( static::$errorMsg[$code], $code );
		}

		$orderid = $param['orderid'];
		$desc    = $param['desc'];
		$otid    = $param['otid'];
		$type    = $param['type'];

		$anchorExchange = new AnchorExchange();

		$orderInfo = $anchorExchange->getOrderInfo( $orderid );

		if ( !$orderInfo )
		{
			$code = self::ERROR_ORDER_NOT_FOUND;

			render_json( static::$errorMsg[$code], $code );
		}

		if ( $orderInfo['status'] == $anchorExchange::EXCHANGE_STATUS_03
			|| $orderInfo['status'] == $anchorExchange::EXCHANGE_STATUS_04
		)
		{
			$code = self::ERROR_ORDER_NOT_VALID;
			render_json( static::$errorMsg[$code], $code );
		}

		if ( $type == 1 )
		{
			$result = $anchorExchange->success( $orderid );
			$ftid   = $orderInfo['otid'];
		}
		else
		{
			$result = $anchorExchange->refund( $orderid, $orderInfo['uid'], $orderInfo['number'], $desc, $otid );
			$ftid   = $result;
		}

		$this->_withdrawLog( $orderid, $otid, $type, $result );

		if ( !$result )
		{
			$code = self::ERROR_ORDER_REFUND_FAILED;
			render_json( static::$errorMsg[$code], $code );
		}

		render_json( [ 'orderid' => "$orderid", 'ftid' => "$ftid" ] );
	}

	public function setDueSetRate()
	{
		$paramRule = [
//			'list' => [
//				'must' => true,
//				'type' => 'string',
//			],
			'rate' => [
				'must' => true,
				'type' => 'int'
			],
			'desc' => [
				'must' => true,
				'type' => 'string'
			]
		];

		$data   = $_POST;
		$params = [];

		if ( !checkParam( $paramRule, $data, $params ) )
		{
			$code = self::ERROR_PARAM_NOT_VALID;
			render_json( static::$errorMsg[$code], $code );
		}



		$list = $_POST['list'];

		if(!is_array($list) || empty($list))
		{
			$code = self::ERROR_PARAM_NOT_VALID;
			render_json( static::$errorMsg[$code], $code );
		}

//		$list = $params['list'];
		$rate = $params['rate'];
		$desc = $params['desc'];

		$fobj = new \lib\Finance();

		$changeDueRate = $fobj->setRate( $list, $rate, $desc, $fobj::EXC_DUE );

		if ( $changeDueRate )
		{
			render_json();
		}
		else
		{
			$code = self::ERROR_CHANGE_RATE_FAILED;
			render_json( static::$errorMsg[$code], $code );
		}
	}

	public function setGiftRate()
	{
		$paramRule = [
			'rate' => [
				'must' => true,
				'type' => 'int'
			],
			'desc' => [
				'must' => true,
				'type' => 'string'
			]
		];

		$data = $_POST;
		$params = [];

		if ( !checkParam( $paramRule, $data, $params ) )
		{
			$code = self::ERROR_PARAM_NOT_VALID;
			render_json( static::$errorMsg[$code], $code );
		}



		$list = $_POST['list'];

		if(!is_array($list) || empty($list))
		{
			$code = self::ERROR_PARAM_NOT_VALID;
			render_json( static::$errorMsg[$code], $code );
		}

		$rate = $params['rate'];
		$desc = $params['desc'];

		$fobj = new \lib\Finance();

		$changeRateResult = $fobj->setRate( $list, $rate, $desc );

		if( $changeRateResult )
		{
			render_json();
		}
		else
		{
			$code = self::ERROR_CHANGE_RATE_FAILED;
			render_error_json( static::$errorMsg[ $code ], $code );
		}
	}
}

